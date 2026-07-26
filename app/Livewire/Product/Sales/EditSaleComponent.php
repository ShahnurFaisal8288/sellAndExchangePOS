<?php

namespace App\Livewire\Product\Sales;

use App\Models\Customer;
use App\Models\Product;
use App\Models\PurchaseItemImei;
use App\Models\Sale;
use App\Models\SaleItem;
use Auth;
use DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app.base.base')]
class EditSaleComponent extends Component
{
    public Sale $sale;

    public string $productSearch = '';
    public array $cart = []; // same shape as NewSaleComponent

    // Snapshot of what the sale looked like BEFORE editing, used to
    // (a) let stock/IMEIs that belong to this sale show as "available" while editing
    // (b) revert them correctly in DB when the update is saved
    public array $originalQtyByProduct = [];   // product_id => qty (non-IMEI lines)
    public array $originalImeiIds = [];         // imei ids that belong to this sale

    public bool $is_new_customer = false;
    public ?int $customerId = null;
    public string $new_customer_name = '';
    public string $new_customer_phone = '';

    public $discount = '';
    public $paidAmount = '';
    public string $paymentMethod = 'cash';

    public function mount(Sale $sale)
    {
        $this->sale = $sale->load('items');

        $this->customerId = $sale->customer_id;
        $this->discount = (float) $sale->discount;
        $this->paidAmount = (float) $sale->paid_amount;
        $this->paymentMethod = $sale->payment_method;

        foreach ($sale->items as $saleItem) {
            $product = Product::find($saleItem->product_id);

            // Try to resolve the linked IMEI row (if any) for this sale item
            $imeiRow = null;
            if (!empty($saleItem->imei_serial)) {
                $imeiRow = PurchaseItemImei::where('sale_item_id', $saleItem->id)->first();
            }

            $cartKey = $imeiRow ? "{$saleItem->product_id}_imei_{$imeiRow->id}" : (string) $saleItem->product_id;

            $this->cart[$cartKey] = [
                'product_id'   => $saleItem->product_id,
                'name'         => $product ? trim($product->name . ' ' . ($product->model ?? '')) : $saleItem->product_id,
                'price'        => (float) $saleItem->unit_price,
                'qty'          => (int) $saleItem->quantity,
                // stock for display purposes only (real max is computed via virtualStock())
                'stock'        => $product ? (int) $product->stock_quantity : 0,
                'country_code' => $product->country_code ?? null,
                'color'        => $product->color ?? null,
                'imei'         => $saleItem->imei_serial,
                'imei_id'      => $imeiRow?->id,
            ];

            if ($imeiRow) {
                $this->originalImeiIds[] = $imeiRow->id;
            } else {
                $this->originalQtyByProduct[$saleItem->product_id] =
                    ($this->originalQtyByProduct[$saleItem->product_id] ?? 0) + $saleItem->quantity;
            }
        }
    }

    /**
     * Stock available to this edit session = actual DB stock + whatever quantity
     * of this product was already reserved by the original sale (since that stock
     * hasn't been physically returned to the shelf, only virtually while editing).
     */
    protected function virtualStock(Product $product): int
    {
        return (int) $product->stock_quantity + (int) ($this->originalQtyByProduct[$product->id] ?? 0);
    }

    public function getSearchResultsProperty()
    {
        $search = trim($this->productSearch);

        if (strlen($search) < 2) {
            return [];
        }

        $searchLower = strtolower($search);

        // IMEIs currently sitting in the cart shouldn't be offered again
        $usedImeiIds = array_filter(array_column($this->cart, 'imei_id'));

        $products = Product::where('status', 'active')
            ->where(function ($q) use ($searchLower) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$searchLower}%"])
                  ->orWhereRaw('LOWER(model) LIKE ?', ["%{$searchLower}%"])
                  ->orWhereRaw('LOWER(country_code) LIKE ?', ["%{$searchLower}%"])
                  ->orWhereHas('purchaseItemImeis', function ($imeiQuery) use ($searchLower) {
                      $imeiQuery->whereRaw('LOWER(imei_serial) LIKE ?', ["%{$searchLower}%"]);
                  });
            })
            ->with(['purchaseItemImeis' => function ($q) use ($usedImeiIds) {
                // Available = not sold, OR it belongs to this sale (being edited)
                $q->where(function ($sub) {
                        $sub->where('is_sold', false);

                        if (!empty($this->originalImeiIds)) {
                            $sub->orWhereIn('id', $this->originalImeiIds);
                        }
                    });

                if (!empty($usedImeiIds)) {
                    $q->whereNotIn('id', $usedImeiIds);
                }

                $q->latest()->limit(10);
            }])
            ->limit(10)
            ->get();

        // Filter out products with zero virtual stock and no available IMEIs
        return $products->filter(function ($product) {
            return $this->virtualStock($product) > 0 || $product->purchaseItemImeis->isNotEmpty();
        })->values();
    }

    public function addToCart($productId, $selectedImei = null, $selectedImeiId = null)
    {
        $product = Product::findOrFail($productId);
        $search = trim($this->productSearch);

        if (!$selectedImeiId && !empty($search)) {
            $usedImeiIds = array_filter(array_column($this->cart, 'imei_id'));

            $matchedImei = PurchaseItemImei::where('product_id', $productId)
                ->where(function ($q) {
                    $q->where('is_sold', false);
                    if (!empty($this->originalImeiIds)) {
                        $q->orWhereIn('id', $this->originalImeiIds);
                    }
                })
                ->whereRaw('LOWER(imei_serial) = ?', [strtolower($search)])
                ->when(!empty($usedImeiIds), fn($q) => $q->whereNotIn('id', $usedImeiIds))
                ->first();

            if ($matchedImei) {
                $selectedImei = $matchedImei->imei_serial;
                $selectedImeiId = $matchedImei->id;
            }
        }

        $cartKey = $selectedImeiId ? "{$productId}_imei_{$selectedImeiId}" : (string) $productId;
        $virtualStock = $this->virtualStock($product);

        if (isset($this->cart[$cartKey])) {
            if ($this->cart[$cartKey]['qty'] < $virtualStock) {
                $this->cart[$cartKey]['qty']++;
            }
        } else {
            $this->cart[$cartKey] = [
                'product_id'   => $product->id,
                'name'         => trim($product->name . ' ' . ($product->model ?? '')),
                'price'        => (float) $product->sale_price,
                'qty'          => 1,
                'stock'        => $virtualStock,
                'country_code' => $product->country_code ?? null,
                'color'        => $product->color ?? null,
                'imei'         => $selectedImei ?? null,
                'imei_id'      => $selectedImeiId ?? null,
            ];
        }

        $this->productSearch = '';
    }

    public function toggleNewCustomer()
    {
        $this->is_new_customer = ! $this->is_new_customer;
        $this->customerId = null;
        $this->new_customer_name = '';
        $this->new_customer_phone = '';
    }

    public function updateQty($cartKey, $qty)
    {
        if ($qty < 1) {
            $this->removeFromCart($cartKey);
            return;
        }

        if (isset($this->cart[$cartKey])) {
            $item = $this->cart[$cartKey];

            if (!empty($item['imei_id']) && $qty > 1) {
                $qty = 1;
            } else {
                $product = Product::find($item['product_id']);
                $maxStock = $product ? $this->virtualStock($product) : $item['stock'];

                if ($qty > $maxStock) {
                    $qty = $maxStock;
                }
            }

            $this->cart[$cartKey]['qty'] = (int) $qty;
        }
    }
public function updatePrice($cartKey, $price)
{
    if (!isset($this->cart[$cartKey])) {
        return;
    }

    $price = is_numeric($price) ? (float) $price : 0;

    if ($price < 0) {
        $price = 0;
    }

    $this->cart[$cartKey]['price'] = $price;
}
    public function removeFromCart($cartKey)
    {
        unset($this->cart[$cartKey]);
    }

    public function getSubtotalProperty(): float
    {
        return (float) collect($this->cart)->sum(fn($item) => $item['price'] * $item['qty']);
    }

    public function getTotalProperty(): float
    {
        return max(0, $this->subtotal - (float) $this->discount);
    }

    public function getDueProperty(): float
    {
        return max(0, $this->total - (float) $this->paidAmount);
    }

    public function updatedDiscount($value)
    {
        $this->discount = is_numeric($value) ? max(0, (float) $value) : 0;
    }

    public function updatedPaidAmount($value)
    {
        $this->paidAmount = is_numeric($value) ? max(0, (float) $value) : 0;
    }

    public function confirmUpdate()
    {
        $rules = [
            'cart'          => 'required|array|min:1',
            'paymentMethod' => 'required|in:cash,card,mobile_banking',
            'discount'      => 'nullable|numeric|min:0',
            'paidAmount'    => 'nullable|numeric|min:0',
        ];

        if ($this->is_new_customer) {
            $rules['new_customer_name'] = 'required|string|max:255';
            $rules['new_customer_phone'] = 'required|string|max:20';
        }

        $this->validate($rules, [
            'cart.required'            => 'Add at least one product to the cart before saving.',
            'new_customer_name.required'  => 'Please provide the new customer name.',
            'new_customer_phone.required' => 'Please provide the new customer phone number.',
        ]);

        $discount = (float) ($this->discount ?: 0);
        $paidAmount = (float) ($this->paidAmount ?: 0);

        if ($discount > $this->subtotal) {
            $this->addError('discount', 'Discount cannot exceed subtotal.');
            return;
        }

        DB::transaction(function () use ($discount, $paidAmount) {
            $sale = Sale::where('id', $this->sale->id)->lockForUpdate()->firstOrFail();

            // 1) REVERT original sale's effect on stock & IMEIs
            $originalItems = SaleItem::where('sale_id', $sale->id)->get();

            foreach ($originalItems as $originalItem) {
                Product::where('id', $originalItem->product_id)
                    ->lockForUpdate()
                    ->increment('stock_quantity', $originalItem->quantity);

                PurchaseItemImei::where('sale_item_id', $originalItem->id)->update([
                    'is_sold'      => false,
                    'sale_item_id' => null,
                ]);
            }

            SaleItem::where('sale_id', $sale->id)->delete();

            // 2) Resolve customer
            $resolvedCustomerId = $this->customerId;

            if ($this->is_new_customer) {
                $customer = Customer::create([
                    'name'  => trim($this->new_customer_name),
                    'phone' => trim($this->new_customer_phone),
                ]);
                $resolvedCustomerId = $customer->id;
            }

            // 3) REAPPLY the new cart (same as create flow)
            foreach ($this->cart as $item) {
                $productId = $item['product_id'];

                $saleItem = SaleItem::create([
                    'sale_id'     => $sale->id,
                    'product_id'  => $productId,
                    'quantity'    => $item['qty'],
                    'unit_price'  => $item['price'],
                    'subtotal'    => $item['price'] * $item['qty'],
                    'imei_serial' => $item['imei'] ?? null,
                ]);

                if (!empty($item['imei_id'])) {
                    PurchaseItemImei::where('id', $item['imei_id'])->update([
                        'is_sold'      => true,
                        'sale_item_id' => $saleItem->id,
                    ]);
                }

                Product::where('id', $productId)->lockForUpdate()->decrement('stock_quantity', $item['qty']);
            }

            // 4) Update the sale header
            $sale->update([
                'customer_id'    => $resolvedCustomerId ?: null,
                'total_amount'   => $this->total,
                'discount'       => $discount,
                'paid_amount'    => $paidAmount,
                'due_amount'     => $this->due,
                'payment_method' => $this->paymentMethod,
            ]);

            session()->flash('success', "Sale invoice {$sale->invoice_no} updated successfully.");

            $this->dispatch('sale-updated', invoiceNo: $sale->invoice_no);
        });
    }

    public function render()
    {
        return view('livewire.product.sales.edit-sale-component', [
            'customers' => Customer::orderBy('name')->get(),
        ]);
    }
}
