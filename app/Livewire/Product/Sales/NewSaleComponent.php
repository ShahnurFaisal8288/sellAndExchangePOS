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
    public array $payments = [];
    // public $paidAmount = 0;
    // public string $paymentMethod = 'cash';

    public function getSearchResultsProperty()
{
    $search = trim($this->productSearch);

    if (strlen($search) < 2) {
        return [];
    }

    $searchLower = strtolower($search);

    $usedImeiIds = array_filter(array_column($this->cart, 'imei_id'));

    return Product::where('status', 'active')
        ->where('stock_quantity', '>', 0)
        ->where(function ($q) use ($searchLower) {
            $q->whereRaw('LOWER(name) LIKE ?', ["%{$searchLower}%"])
              ->orWhereRaw('LOWER(model) LIKE ?', ["%{$searchLower}%"])
              ->orWhereRaw('LOWER(country_code) LIKE ?', ["%{$searchLower}%"])
              ->orWhereHas('purchaseItemImeis', function ($imeiQuery) use ($searchLower) {
                  $imeiQuery->where('is_sold', false)
                            ->whereRaw('LOWER(imei_serial) LIKE ?', ["%{$searchLower}%"]);
              });
        })
        // 👇 replace the old ->with([...]) call with this one
        ->with(['purchaseItemImeis' => function ($q) use ($usedImeiIds) {
            $q->where('is_sold', false);

            if (!empty($usedImeiIds)) {
                $q->whereNotIn('id', $usedImeiIds);
            }

            $q->with(['colorAttribute', 'countryAttribute'])->latest()->limit(10);
        }])
        ->limit(10)
        ->get();
}

    public function addToCart($productId, $selectedImei = null, $selectedImeiId = null)
{
    $product = Product::findOrFail($productId);
    $search = trim($this->productSearch);

    $imeiColor = null;
    $imeiCountry = null;

    if (!$selectedImeiId && !empty($search)) {
        $usedImeiIds = array_filter(array_column($this->cart, 'imei_id'));

        $matchedImei = PurchaseItemImei::where('product_id', $productId)
            ->where('is_sold', false)
            ->whereRaw('LOWER(imei_serial) = ?', [strtolower($search)])
            ->when(!empty($usedImeiIds), fn($q) => $q->whereNotIn('id', $usedImeiIds))
            ->with(['colorAttribute', 'countryAttribute'])
            ->first();

        if ($matchedImei) {
            $selectedImei = $matchedImei->imei_serial;
            $selectedImeiId = $matchedImei->id;
            $imeiColor = $matchedImei->colorAttribute?->label;
            $imeiCountry = $matchedImei->countryAttribute?->label;
        }
    } elseif ($selectedImeiId) {
        $matchedImei = PurchaseItemImei::with(['colorAttribute', 'countryAttribute'])->find($selectedImeiId);
        $imeiColor = $matchedImei?->colorAttribute?->label;
        $imeiCountry = $matchedImei?->countryAttribute?->label;
    }

    $cartKey = $selectedImeiId ? "{$productId}_imei_{$selectedImeiId}" : (string) $productId;

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
            'country_code' => $imeiCountry ?? $product->country_code ?? null,
            'color' => $imeiColor ?? $product->color ?? null,
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

    // public function updatedPaidAmount($value)
    // {
    //     $this->paidAmount = is_numeric($value) ? max(0, (float) $value) : 0;
    // }
    public function mount()
{
    $this->addPaymentRow();
}
    public function addPaymentRow()
{
    $this->payments[] = [
        'method' => 'cash',
        'amount' => 0,
        'notes' => '',
    ];
}
public function removePaymentRow($index)
{
    unset($this->payments[$index]);
    $this->payments = array_values($this->payments);

    if (empty($this->payments)) {
        $this->addPaymentRow();
    }
}
public function updatedPayments($value, $key)
{
    $parts = explode('.', $key);
    if (count($parts) === 2 && $parts[1] === 'amount') {
        $amt = is_numeric($value) ? (float) $value : 0;
        $this->payments[$parts[0]]['amount'] = max(0, $amt);
    }
}
public function getPaidAmountProperty(): float
{
    return (float) collect($this->payments)->sum(fn($p) => (float) ($p['amount'] ?? 0));
}

   public function confirmSale()
{
    $rules = [
        'cart' => 'required|array|min:1',
        'payments' => 'required|array|min:1',
        'payments.*.method' => 'required|in:cash,card,mobile_banking',
        'payments.*.amount' => 'required|numeric|min:0',
        'discount' => 'nullable|numeric|min:0',
    ];

    if ($this->is_new_customer) {
        $rules['new_customer_name'] = 'required|string|max:255';
        $rules['new_customer_phone'] = 'required|string|max:20';
    }

    $this->validate($rules, [
        'cart.required' => 'Add at least one product to the cart before confirming.',
        'payments.required' => 'Add at least one payment method.',
        'new_customer_name.required' => 'Please provide the new customer name.',
        'new_customer_phone.required' => 'Please provide the new customer phone number.',
    ]);

    $discount = (float) ($this->discount ?: 0);

    if ($discount > $this->subtotal) {
        $this->addError('discount', 'Discount cannot exceed subtotal.');
        return;
    }

    $activePayments = collect($this->payments)
        ->map(fn($p) => [
            'payment_method' => $p['method'],
            'amount' => (float) ($p['amount'] ?? 0),
            'notes' => trim($p['notes'] ?? '') ?: null,
        ])
        ->filter(fn($p) => $p['amount'] > 0)
        ->values();

    $paidAmount = (float) $activePayments->sum('amount');

    if ($paidAmount > $this->total) {
        $this->addError('payments', 'Total paid (৳' . number_format($paidAmount, 2) . ') cannot exceed the sale total (৳' . number_format($this->total, 2) . ').');
        return;
    }

    DB::transaction(function () use ($discount, $paidAmount, $activePayments) {
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
            'payment_method' => $activePayments->first()['payment_method'] ?? 'cash',
            'sale_date' => now()->toDateString(),
        ]);

        foreach ($activePayments as $payment) {
            $sale->payments()->create([
                ...$payment,
                'user_id' => Auth::id() ?? 1,
                'paid_date' => now()->toDateString(),
            ]);
        }

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

        $this->reset(['cart', 'customerId', 'is_new_customer', 'new_customer_name', 'new_customer_phone', 'discount', 'payments', 'productSearch']);
        $this->addPaymentRow();

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
