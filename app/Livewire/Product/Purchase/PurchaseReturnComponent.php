<?php

namespace App\Livewire\Product\Purchase;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItemImei;
use App\Models\PurchaseReturn;
use DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app.base.base')]
class PurchaseReturnComponent extends Component
{
    public $purchaseId = null;

    public $supplier_id = '';
    public $supplier_name = '';
    public $invoice_no = '';
    public $return_date = '';
    public $total_amount = 0;

    // Original purchase's financial snapshot, shown for context
    public $original_total = 0;
    public $original_paid = 0;
    public $original_due = 0;

    public $items = [];

    public function mount($id = null)
    {
        if (!$id) {
            session()->flash('error', 'Please select a purchase to return from the list.');
            return $this->redirect(route('purchases.index'));
        }

        $this->purchaseId = $id;

        // Load ALL imeis here (no query-time filter) so we can tell the
        // difference between "serialized product with zero units left"
        // and "this product was never serialized in the first place".
        $purchase = Purchase::with(['items.product', 'items.imeis'])
            ->findOrFail($id);

        $this->supplier_id = $purchase->supplier_id;
        $this->supplier_name = $purchase->supplier?->name ?? 'Walk-in / No Supplier';
        $this->invoice_no = 'RET-' . str_replace('PUR-', '', $purchase->invoice_no) . '-' . now()->format('His');
        $this->return_date = now()->format('Y-m-d');

        $this->original_total = (float) $purchase->total_amount;
        $this->original_paid = (float) $purchase->paid_amount;
        $this->original_due = (float) $purchase->due_amount;

        foreach ($purchase->items as $item) {
            // Only units that are still physically in stock (not sold,
            // not already returned) are eligible to be returned. This is
            // the fix: previously sold units were showing up as returnable.
            $availableImeisCollection = $item->imeis
                ->where('is_sold', false)
                ->where('is_returned', false);

            $hasSerials = $item->imeis->isNotEmpty();

            $remainingQty = $hasSerials
                ? $availableImeisCollection->count()
                : max(0, $item->quantity - $item->returned_quantity); // legacy / non-serialized fallback

            if ($remainingQty <= 0) {
                continue;
            }

            $availableImeis = $availableImeisCollection->pluck('imei_serial', 'id')->toArray();

            $this->items[] = [
                'purchase_item_id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product?->name ?? '',
                'country_code' => $item->product?->country_code ?? '',
                'color' => $item->product?->color ?? '',
                'max_returnable' => $remainingQty,
                'quantity' => $remainingQty,
                'unit_price' => $item->unit_price,
                'subtotal' => number_format($remainingQty * $item->unit_price, 2, '.', ''),
                'available_imeis' => $availableImeis,
                'selected_imei_ids' => array_keys($availableImeis), // pre-select all IMEIs by default
            ];
        }

        $this->calculateTotals();
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->calculateTotals();
    }

    public function toggleImei($index, $imeiId)
    {
        $selected = $this->items[$index]['selected_imei_ids'];

        if (in_array($imeiId, $selected)) {
            $this->items[$index]['selected_imei_ids'] = array_values(array_diff($selected, [$imeiId]));
        } else {
            $this->items[$index]['selected_imei_ids'][] = $imeiId;
        }

        if (!empty($this->items[$index]['available_imeis'])) {
            $this->items[$index]['quantity'] = count($this->items[$index]['selected_imei_ids']);
        }

        $this->recalculateItem($index);
        $this->calculateTotals();
    }

    public function updatedItems($value, $key)
    {
        $parts = explode('.', $key);
        if (count($parts) === 2) {
            $index = $parts[0];
            $field = $parts[1];

            if ($field === 'quantity') {
                $max = (int) ($this->items[$index]['max_returnable'] ?? 0);
                $qty = (int) $value;

                if ($qty > $max) {
                    $qty = $max;
                    $this->items[$index]['quantity'] = $qty;
                }
                if ($qty < 1) {
                    $qty = 1;
                    $this->items[$index]['quantity'] = $qty;
                }
            }

            $this->recalculateItem($index);
        }
        $this->calculateTotals();
    }

    private function recalculateItem($index)
    {
        $qty = (int) ($this->items[$index]['quantity'] ?? 0);
        $price = (float) ($this->items[$index]['unit_price'] ?? 0);
        $this->items[$index]['subtotal'] = number_format($qty * $price, 2, '.', '');
    }

    public function calculateTotals()
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += ((int) ($item['quantity'] ?? 0)) * ((float) ($item['unit_price'] ?? 0));
        }
        $this->total_amount = number_format($total, 2, '.', '');
    }

    // How much of the return cancels outstanding due vs. gets refunded as cash
    public function getDueCancelledProperty(): float
    {
        return min($this->original_due, (float) $this->total_amount);
    }

    public function getCashRefundProperty(): float
    {
        return max(0, (float) $this->total_amount - $this->original_due);
    }

    private function validateForm(): void
    {
        $errors = [];

        if (blank($this->return_date)) {
            $errors['return_date'] = 'Return date is required.';
        }

        if (empty($this->items)) {
            $errors['items'] = 'No items available to return.';
        }

        foreach ($this->items as $index => $item) {
            $qty = (int) ($item['quantity'] ?? 0);
            $max = (int) ($item['max_returnable'] ?? 0);

            if ($qty < 1) {
                $errors["items.$index.quantity"] = 'Quantity must be at least 1.';
            }
            if ($qty > $max) {
                $errors["items.$index.quantity"] = "Cannot return more than {$max} remaining unit(s).";
            }

            // If this item has trackable IMEIs, the selected count must match quantity
            if (!empty($item['available_imeis']) && count($item['selected_imei_ids']) !== $qty) {
                $errors["items.$index.quantity"] = 'Select exactly ' . $qty . ' IMEI(s) for this item.';
            }
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }

    public function save()
    {
        $this->validateForm();

        DB::transaction(function () {
            $purchase = Purchase::lockForUpdate()->findOrFail($this->purchaseId);

            // Normalize quantity from selected IMEIs BEFORE using it anywhere.
            // This guarantees the number we return/decrement always matches
            // what the user actually checked, even if the quantity field
            // in $this->items drifted out of sync with the checkboxes.
            foreach ($this->items as $index => $item) {
                if (!empty($item['available_imeis'])) {
                    $this->items[$index]['quantity'] = count($item['selected_imei_ids']);
                    $this->items[$index]['subtotal'] = number_format(
                        $this->items[$index]['quantity'] * (float) $item['unit_price'],
                        2, '.', ''
                    );
                }
            }
            $this->calculateTotals();

            $returnTotal = (float) $this->total_amount;
            $dueCancelled = min((float) $purchase->due_amount, $returnTotal);
            $cashRefunded = max(0, $returnTotal - $dueCancelled);

            $purchaseReturn = PurchaseReturn::create([
                'purchase_id' => $purchase->id,
                'supplier_id' => $this->supplier_id ?: null,
                'invoice_no' => $this->invoice_no,
                'total_return_value' => $returnTotal,
                'due_cancelled' => $dueCancelled,
                'cash_refunded' => $cashRefunded,
                'return_date' => $this->return_date,
                'user_id' => auth()->id() ?? 1,
            ]);

            foreach ($this->items as $item) {
                $qty = (int) $item['quantity'];

                if ($qty < 1) {
                    continue; // nothing selected for this line, skip it entirely
                }

                $productId = $item['product_id'];

                $purchaseReturn->items()->create([
                    'purchase_item_id' => $item['purchase_item_id'],
                    'product_id' => $productId,
                    'quantity' => $qty,
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['subtotal'],
                ]);

                // Mark ONLY the specifically selected IMEIs as returned.
                // These are guaranteed (as of mount()) to be unsold units,
                // so this can never flip a customer-sold unit back to "in stock".
                if (!empty($item['selected_imei_ids'])) {
                    PurchaseItemImei::whereIn('id', $item['selected_imei_ids'])
                        ->where('is_sold', false)
                        ->update(['is_returned' => true]);
                }

                // Track partial-return progress on the original line item
                DB::table('purchase_items')
                    ->where('id', $item['purchase_item_id'])
                    ->increment('returned_quantity', $qty);

                // Lock the row, then decrement through Eloquent so the write
                // is unambiguous (lockForUpdate chained onto ->update() does nothing)
                $product = Product::where('id', $productId)->lockForUpdate()->first();

                if (!$product) {
                    throw ValidationException::withMessages([
                        'items' => "Product #{$productId} no longer exists.",
                    ]);
                }

                $product->decrement('stock_quantity', $qty);

                if ($product->stock_quantity < 0) {
                    $product->stock_quantity = 0;
                    $product->save();
                }
            }

            // Correct the ORIGINAL purchase's financials
            $purchase->due_amount = max(0, (float) $purchase->due_amount - $dueCancelled);
            $purchase->paid_amount = max(0, (float) $purchase->paid_amount - $cashRefunded);
            $purchase->total_amount = max(0, (float) $purchase->total_amount - $returnTotal);
            $purchase->save();
        });

        session()->flash(
            'message',
            'Purchase return processed. Due cancelled: ৳' . number_format($this->dueCancelled, 2)
            . ', Cash refunded: ৳' . number_format($this->cashRefund, 2)
        );

        return $this->redirect(route('purchases.index'));
    }

    public function render()
    {
        return view('livewire.product.purchase.purchase-return-component');
    }
}
