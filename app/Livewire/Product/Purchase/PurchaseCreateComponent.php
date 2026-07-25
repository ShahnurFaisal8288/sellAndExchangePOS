<?php

namespace App\Livewire\Product\Purchase;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItemImei;
use App\Models\Supplier;
use DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app.base.base')]
class PurchaseCreateComponent extends Component
{
    public $editingId = null;

    // Form fields
    public $supplier_id = '';
    public $is_new_supplier = false;
    public $new_supplier_name = '';
    public $new_supplier_phone = ''; // typed free-text, e.g. "+880 1712-345678"

    public $invoice_no = ''; // auto-generated, read-only in the UI
    public $purchase_date = '';
    public $paid_amount = 0;
    public $total_amount = 0;
    public $due_amount = 0;
    public $items = [];
    public $total_sale_value = 0;

    public function mount($id = null)
    {
        if ($id) {
            $purchase = Purchase::with(['items.product', 'items.imeis'])->findOrFail($id);
            $this->editingId = $purchase->id;
            $this->supplier_id = $purchase->supplier_id;
            $this->invoice_no = $purchase->invoice_no;
            $this->purchase_date = $purchase->purchase_date->format('Y-m-d');
            $this->paid_amount = $purchase->paid_amount;
            $this->total_amount = $purchase->total_amount;
            $this->due_amount = $purchase->due_amount;

            foreach ($purchase->items as $item) {
                // Build a locked/unlocked IMEI list instead of plain strings.
                // A sold or returned IMEI must never be silently editable or
                // deletable from this form -- its history has to stay intact.
                $imeiRows = $item->imeis->map(function ($imei) {
                    return [
                        'id' => $imei->id,
                        'imei_serial' => $imei->imei_serial,
                        'locked' => (bool) ($imei->is_sold || $imei->is_returned),
                    ];
                })->values()->toArray();

                if (empty($imeiRows)) {
                    $imeiRows = [['id' => null, 'imei_serial' => '', 'locked' => false]];
                }

                $lockedCount = collect($imeiRows)->where('locked', true)->count();

                $this->items[] = [
                    'purchase_item_id' => $item->id,
                    'is_new_product' => false,
                    'product_id' => $item->product_id,
                    'product_name' => '',
                    'country_code' => $item->product?->country_code ?? '',
                    'color' => $item->product?->color ?? '',
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'sale_price' => $item->product?->sale_price ?? 0,
                    'subtotal' => $item->subtotal,
                    'sale_subtotal' => number_format($item->quantity * ($item->product?->sale_price ?? 0), 2, '.', ''),
                    'imeis' => $imeiRows,
                    // Minimum quantity this line can be edited down to, because
                    // this many units are already sold or returned and can't be undone here.
                    'min_quantity' => max(1, $lockedCount),
                ];
            }
        } else {
            $this->purchase_date = now()->format('Y-m-d');
            $this->invoice_no = $this->generateInvoiceNumber();
            $this->addItem();
        }
    }

    public function addItem()
    {
        $this->items[] = [
            'purchase_item_id' => null,
            'is_new_product' => false,
            'product_id' => '',
            'product_name' => '',
            'country_code' => '',
            'color' => '',
            'quantity' => 1,
            'unit_price' => 0,
            'sale_price' => 0,
            'subtotal' => 0,
            'sale_subtotal' => 0,
            'imeis' => [['id' => null, 'imei_serial' => '', 'locked' => false]],
            'min_quantity' => 1,
        ];
        $this->calculateTotals();
    }

    public function removeItem($index)
    {
        // Never allow removing a line that has sold/returned units attached --
        // that would silently destroy their history via cascade delete.
        $lockedCount = collect($this->items[$index]['imeis'] ?? [])->where('locked', true)->count();

        if ($lockedCount > 0) {
            $this->addError("items.$index.quantity", 'This line has sold or returned units and cannot be removed.');
            return;
        }

        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->calculateTotals();
    }

    public function toggleNewSupplier()
    {
        $this->is_new_supplier = !$this->is_new_supplier;
        $this->supplier_id = '';
        $this->new_supplier_name = '';
        $this->new_supplier_phone = '';
    }

    public function toggleNewProduct($index)
    {
        $this->items[$index]['is_new_product'] = !$this->items[$index]['is_new_product'];
        $this->items[$index]['product_id'] = '';
        $this->items[$index]['product_name'] = '';
        $this->items[$index]['country_code'] = '';
        $this->items[$index]['color'] = '';
        $this->items[$index]['sale_price'] = 0;
    }

    public function addImeiField($index)
    {
        $this->items[$index]['imeis'][] = ['id' => null, 'imei_serial' => '', 'locked' => false];
    }

    public function removeImeiField($index, $imeiIndex)
    {
        // A locked (sold/returned) IMEI can't be removed from the form.
        if (!empty($this->items[$index]['imeis'][$imeiIndex]['locked'])) {
            $this->addError("items.$index.imeis", 'This IMEI is already sold or returned and cannot be removed.');
            return;
        }

        unset($this->items[$index]['imeis'][$imeiIndex]);
        $this->items[$index]['imeis'] = array_values($this->items[$index]['imeis']);
        if (empty($this->items[$index]['imeis'])) {
            $this->items[$index]['imeis'] = [['id' => null, 'imei_serial' => '', 'locked' => false]];
        }
    }

    public function updatedItems($value, $key)
    {
        $parts = explode('.', $key);
        if (count($parts) === 2) {
            $index = $parts[0];
            $field = $parts[1];

            if ($field === 'product_id' && !empty($value)) {
                $product = Product::find($value);
                if ($product) {
                    $this->items[$index]['country_code'] = $product->country_code ?? '';
                    $this->items[$index]['color'] = $product->color ?? '';
                    $this->items[$index]['unit_price'] = $product->purchase_price ?? 0;
                    $this->items[$index]['sale_price'] = $product->sale_price ?? 0;
                }
            }

            if ($field === 'quantity') {
                $min = (int) ($this->items[$index]['min_quantity'] ?? 1);
                if ((int) $value < $min) {
                    $this->items[$index]['quantity'] = $min;
                }
            }

            if (in_array($field, ['quantity', 'unit_price', 'sale_price'])) {
                $qty = (int) ($this->items[$index]['quantity'] ?? 0);
                $price = (float) ($this->items[$index]['unit_price'] ?? 0);
                $salePrice = (float) ($this->items[$index]['sale_price'] ?? 0);

                $this->items[$index]['subtotal'] = number_format($qty * $price, 2, '.', '');
                $this->items[$index]['sale_subtotal'] = number_format($qty * $salePrice, 2, '.', '');
            }
        }
        $this->calculateTotals();
    }

    public function updatedPaidAmount()
    {
        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        $total = 0;
        $saleTotal = 0;
        foreach ($this->items as $item) {
            $qty = (int) ($item['quantity'] ?? 0);
            $total += $qty * ((float) ($item['unit_price'] ?? 0));
            $saleTotal += $qty * ((float) ($item['sale_price'] ?? 0));
        }
        $this->total_amount = number_format($total, 2, '.', '');
        $this->due_amount = number_format(max(0, $total - (float) $this->paid_amount), 2, '.', '');
        $this->total_sale_value = number_format($saleTotal, 2, '.', '');
    }

    public function getLowStockProductsProperty()
    {
        return Product::whereColumn('stock_quantity', '<=', 'min_stock_alert')
            ->orderBy('stock_quantity')
            ->limit(10)
            ->get(['id', 'name', 'stock_quantity', 'min_stock_alert']);
    }

    private function generateInvoiceNumber(): string
    {
        $next = (Purchase::max('id') ?? 0) + 1;

        return 'PUR-' . str_pad((string) $next, 6, '0', STR_PAD_LEFT);
    }

    private function validateForm(): void
    {
        $errors = [];

        if ($this->is_new_supplier) {
            if (blank($this->new_supplier_name)) {
                $errors['new_supplier_name'] = 'Supplier name is required.';
            }
        } elseif (blank($this->supplier_id)) {
            $errors['supplier_id'] = 'Please select a supplier or add a new one.';
        }

        if (blank($this->purchase_date)) {
            $errors['purchase_date'] = 'Purchase date is required.';
        }

        if ($this->paid_amount === '' || $this->paid_amount === null || (float) $this->paid_amount < 0) {
            $errors['paid_amount'] = 'Paid amount must be zero or more.';
        }

        if (empty($this->items)) {
            $errors['items'] = 'Add at least one line item.';
        }

        foreach ($this->items as $index => $item) {
            if ($item['is_new_product']) {
                if (blank($item['product_name'] ?? null)) {
                    $errors["items.$index.product_name"] = 'Product name is required.';
                }
            } elseif (blank($item['product_id'] ?? null)) {
                $errors["items.$index.product_id"] = 'Select a product or add a new one.';
            }

            $qty = (int) ($item['quantity'] ?? 0);
            $minQty = (int) ($item['min_quantity'] ?? 1);

            if ($qty < 1) {
                $errors["items.$index.quantity"] = 'Quantity must be at least 1.';
            }

            // Can't shrink a line below the number of units already sold/returned.
            if ($qty < $minQty) {
                $errors["items.$index.quantity"] = "Cannot reduce below {$minQty}: that many unit(s) are already sold or returned.";
            }

            if ((float) ($item['unit_price'] ?? -1) < 0) {
                $errors["items.$index.unit_price"] = 'Unit cost must be zero or more.';
            }

            if ((float) ($item['sale_price'] ?? -1) < 0) {
                $errors["items.$index.sale_price"] = 'Sale price must be zero or more.';
            }

            $imeiValues = array_values(array_filter(
                array_map(fn($row) => trim((string) ($row['imei_serial'] ?? '')), $item['imeis'] ?? []),
                fn($v) => $v !== ''
            ));
            if (count($imeiValues) > 0 && count($imeiValues) !== $qty) {
                $errors["items.$index.imeis"] = 'Enter one IMEI/serial per unit, or leave all blank.';
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
            $isEditing = (bool) $this->editingId;
            $supplierId = $this->supplier_id ?: null;

            if ($this->is_new_supplier) {
                $supplier = Supplier::create([
                    'name' => $this->new_supplier_name,
                    'phone' => $this->new_supplier_phone,
                ]);
                $supplierId = $supplier->id;
            }

            $purchase = Purchase::updateOrCreate(
                ['id' => $this->editingId],
                [
                    'supplier_id' => $supplierId,
                    'source_type' => 'supplier',
                    'user_id' => auth()->id() ?? 1,
                    'invoice_no' => $this->invoice_no,
                    'total_amount' => $this->total_amount,
                    'paid_amount' => $this->paid_amount,
                    'due_amount' => $this->due_amount,
                    'purchase_date' => $this->purchase_date,
                ]
            );

            $keptPurchaseItemIds = [];

            foreach ($this->items as $item) {
                $salePrice = (float) ($item['sale_price'] ?? 0);
                $newQty = (int) $item['quantity'];

                if ($item['is_new_product']) {
                    $countryCode = !empty($item['country_code']) ? strtoupper(trim($item['country_code'])) : null;
                    $color = !empty($item['color']) ? trim($item['color']) : null;

                    $product = Product::create([
                        'name' => $item['product_name'],
                        'country_code' => $countryCode,
                        'color' => $color,
                        'category_id' => null,
                        'brand_id' => null,
                        'purchase_price' => $item['unit_price'],
                        'sale_price' => $salePrice,
                        'stock_quantity' => 0,
                        'min_stock_alert' => 5,
                        'status' => 'active',
                    ]);
                    $productId = $product->id;
                } else {
                    $productId = $item['product_id'];
                }

                $existingPurchaseItemId = $item['purchase_item_id'] ?? null;
                $oldQty = 0;

                if ($isEditing && $existingPurchaseItemId) {
                    // Update the existing line in place instead of deleting it --
                    // deleting would cascade and destroy sold/returned IMEI history.
                    $purchaseItem = $purchase->items()->findOrFail($existingPurchaseItemId);
                    $oldQty = (int) $purchaseItem->quantity;

                    $purchaseItem->update([
                        'product_id' => $productId,
                        'quantity' => $newQty,
                        'unit_price' => $item['unit_price'],
                        'subtotal' => $item['subtotal'],
                    ]);
                } else {
                    $purchaseItem = $purchase->items()->create([
                        'product_id' => $productId,
                        'quantity' => $newQty,
                        'unit_price' => $item['unit_price'],
                        'subtotal' => $item['subtotal'],
                    ]);
                }

                $keptPurchaseItemIds[] = $purchaseItem->id;

                // Reconcile IMEI rows: keep locked (sold/returned) rows untouched,
                // update text on existing unlocked rows, add/remove only unlocked rows.
                $incomingImeis = $item['imeis'] ?? [];

                // Remove unlocked rows the user deleted from the form
                $incomingIds = collect($incomingImeis)->pluck('id')->filter()->all();
                PurchaseItemImei::where('purchase_item_id', $purchaseItem->id)
                    ->where('is_sold', false)
                    ->where('is_returned', false)
                    ->whereNotIn('id', $incomingIds ?: [0])
                    ->delete();

                foreach ($incomingImeis as $row) {
                    $serial = trim((string) ($row['imei_serial'] ?? ''));
                    if ($serial === '') {
                        continue;
                    }

                    if (!empty($row['id'])) {
                        // Existing row: only update serial text if it's unlocked
                        $existing = PurchaseItemImei::find($row['id']);
                        if ($existing && !$existing->is_sold && !$existing->is_returned) {
                            $existing->update(['imei_serial' => $serial]);
                        }
                        // locked rows are left completely alone
                    } else {
                        // Brand new IMEI row for this line
                        $purchaseItem->imeis()->create([
                            'product_id' => $productId,
                            'imei_serial' => $serial,
                        ]);
                    }
                }

                // Apply only the net stock change for this line, not a blind
                // decrement-then-increment, so nothing double-counts.
                $delta = $newQty - $oldQty;

                $product = Product::where('id', $productId)->lockForUpdate()->first();
                $product->purchase_price = (float) $item['unit_price'];
                $product->sale_price = $salePrice;
                if ($delta !== 0) {
                    $product->stock_quantity = max(0, (int) $product->stock_quantity + $delta);
                }
                $product->save();
            }

            // Any purchase item that existed before but is no longer in the
            // submitted list was removed by the user via removeItem(), which
            // already blocks removal of lines with locked IMEIs. Safe to delete.
            if ($isEditing) {
                $purchase->items()
                    ->whereNotIn('id', $keptPurchaseItemIds ?: [0])
                    ->get()
                    ->each(function ($oldItem) {
                        Product::where('id', $oldItem->product_id)
                            ->lockForUpdate()
                            ->decrement('stock_quantity', $oldItem->quantity);
                        $oldItem->delete(); // cascades imeis, safe: none were locked
                    });
            }
        });

        session()->flash('message', $this->editingId ? 'Purchase transaction updated.' : 'Purchase transaction registered.');

        return $this->redirect(route('purchases.index'));
    }

    public function render()
    {
        return view('livewire.product.purchase.purchase-create-component', [
            'suppliers' => Supplier::all(),
            'products' => Product::all(),
        ]);
    }
}
