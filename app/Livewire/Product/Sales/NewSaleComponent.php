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
class NewSaleComponent extends Component
{
    public string $productSearch = '';
    public array $cart = []; // [cart_key => ['product_id'=>, 'name'=>, 'price'=>, 'qty'=>, 'stock'=>, 'imei'=>, 'imei_id'=>]]

    // Customer toggle & fields
    public bool $is_new_customer = false;
    public ?int $customerId = null;
    public string $new_customer_name = '';
    public string $new_customer_phone = '';

    // Financial fields
    public $discount = 0;
    public $paidAmount = 0;
    public string $paymentMethod = 'cash';

    public function getSearchResultsProperty()
    {
        $search = trim($this->productSearch);

        if (strlen($search) < 2) {
            return [];
        }

        $searchLower = strtolower($search);

        // Don't show IMEIs already placed in the active cart
        $usedImeiIds = array_filter(array_column($this->cart, 'imei_id'));

        return Product::where('status', 'active')
            ->where('stock_quantity', '>', 0)
            ->where(function ($q) use ($searchLower) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$searchLower}%"])
                  ->orWhereRaw('LOWER(model) LIKE ?', ["%{$searchLower}%"])
                  ->orWhereRaw('LOWER(country_code) LIKE ?', ["%{$searchLower}%"])
                  // Search ONLY UNSOLD IMEIs
                  ->orWhereHas('purchaseItemImeis', function ($imeiQuery) use ($searchLower) {
                      $imeiQuery->where('is_sold', false)
                                ->whereRaw('LOWER(imei_serial) LIKE ?', ["%{$searchLower}%"]);
                  });
            })
            ->with(['purchaseItemImeis' => function ($q) use ($usedImeiIds) {
                $q->where('is_sold', false);

                if (!empty($usedImeiIds)) {
                    $q->whereNotIn('id', $usedImeiIds);
                }

                $q->latest()->limit(10);
            }])
            ->limit(10)
            ->get();
    }

    public function addToCart($productId, $selectedImei = null, $selectedImeiId = null)
    {
        $product = Product::findOrFail($productId);
        $search = trim($this->productSearch);

        // Auto-detect exact IMEI match from search bar if no specific IMEI button was clicked
        if (!$selectedImeiId && !empty($search)) {
            $usedImeiIds = array_filter(array_column($this->cart, 'imei_id'));

            $matchedImei = PurchaseItemImei::where('product_id', $productId)
                ->where('is_sold', false)
                ->whereRaw('LOWER(imei_serial) = ?', [strtolower($search)])
                ->when(!empty($usedImeiIds), fn($q) => $q->whereNotIn('id', $usedImeiIds))
                ->first();

            if ($matchedImei) {
                $selectedImei = $matchedImei->imei_serial;
                $selectedImeiId = $matchedImei->id;
            }
        }

        // Unique cart key keeps distinct IMEIs as separate line items
        $cartKey = $selectedImeiId ? "{$productId}_imei_{$selectedImeiId}" : (string)$productId;

        if (isset($this->cart[$cartKey])) {
            if ($this->cart[$cartKey]['qty'] < $product->stock_quantity) {
                $this->cart[$cartKey]['qty']++;
            }
        } else {
            $this->cart[$cartKey] = [
                'product_id' => $product->id,
                'name' => trim($product->name . ' ' . ($product->model ?? '')),
                'price' => (float) $product->sale_price,
                'qty' => 1,
                'stock' => (int) $product->stock_quantity,
                'country_code' => $product->country_code ?? null,
                'color' => $product->color ?? null,
                'imei' => $selectedImei ?? null,
                'imei_id' => $selectedImeiId ?? null,
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
            // Serialized items with specific IMEIs should stay at qty = 1
            if (!empty($this->cart[$cartKey]['imei_id']) && $qty > 1) {
                $qty = 1;
            } elseif ($qty > $this->cart[$cartKey]['stock']) {
                $qty = $this->cart[$cartKey]['stock'];
            }
            $this->cart[$cartKey]['qty'] = (int) $qty;
        }
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

    public function confirmSale()
    {
        $rules = [
            'cart' => 'required|array|min:1',
            'paymentMethod' => 'required|in:cash,card,mobile_banking',
            'discount' => 'nullable|numeric|min:0',
            'paidAmount' => 'nullable|numeric|min:0',
        ];

        if ($this->is_new_customer) {
            $rules['new_customer_name'] = 'required|string|max:255';
            $rules['new_customer_phone'] = 'required|string|max:20';
        }

        $this->validate($rules, [
            'cart.required' => 'Add at least one product to the cart before confirming.',
            'new_customer_name.required' => 'Please provide the new customer name.',
            'new_customer_phone.required' => 'Please provide the new customer phone number.',
        ]);

        $discount = (float) ($this->discount ?: 0);
        $paidAmount = (float) ($this->paidAmount ?: 0);

        if ($discount > $this->subtotal) {
            $this->addError('discount', 'Discount cannot exceed subtotal.');
            return;
        }

        DB::transaction(function () use ($discount, $paidAmount) {
            // Resolve Customer
            $resolvedCustomerId = $this->customerId;

            if ($this->is_new_customer) {
                $customer = Customer::create([
                    'name' => trim($this->new_customer_name),
                    'phone' => trim($this->new_customer_phone),
                ]);
                $resolvedCustomerId = $customer->id;
            }

            // Invoice ID
            $invoiceNo = 'INV-' . now()->format('Ymd') . '-' . str_pad((string) (Sale::whereDate('created_at', today())->count() + 1), 4, '0', STR_PAD_LEFT);

            $sale = Sale::create([
                'customer_id' => $resolvedCustomerId ?: null,
                'user_id' => Auth::id() ?? 1,
                'invoice_no' => $invoiceNo,
                'total_amount' => $this->total,
                'discount' => $discount,
                'paid_amount' => $paidAmount,
                'due_amount' => $this->due,
                'payment_method' => $this->paymentMethod,
                'sale_date' => now()->toDateString(),
            ]);

            foreach ($this->cart as $cartKey => $item) {
                $productId = $item['product_id'];

                $saleItem = SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $productId,
                    'quantity' => $item['qty'],
                    'unit_price' => $item['price'],
                    'subtotal' => $item['price'] * $item['qty'],
                    'imei_serial' => $item['imei'] ?? null,
                ]);

                // Mark the specific IMEI as sold and link it to the sale item
                if (!empty($item['imei_id'])) {
                    PurchaseItemImei::where('id', $item['imei_id'])->update([
                        'is_sold' => true,
                        'sale_item_id' => $saleItem->id,
                    ]);
                }

                // Decrement Product Stock
                Product::where('id', $productId)->lockForUpdate()->decrement('stock_quantity', $item['qty']);
            }

            session()->flash('success', "Sale invoice {$sale->invoice_no} processed successfully.");

            $this->reset(['cart', 'customerId', 'is_new_customer', 'new_customer_name', 'new_customer_phone', 'discount', 'paidAmount', 'productSearch']);
            $this->paymentMethod = 'cash';

            $this->dispatch('sale-completed', invoiceNo: $sale->invoice_no);
        });
    }

    public function render()
    {
        return view('livewire.product.sales.new-sale-component', [
            'customers' => Customer::orderBy('name')->get(),
        ]);
    }
}
