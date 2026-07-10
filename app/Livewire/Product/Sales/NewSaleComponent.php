<?php

namespace App\Livewire\Product\Sales;

use App\Models\Customer;
use App\Models\Product;
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
    public array $cart = []; // [product_id => ['name'=>, 'price'=>, 'qty'=>, 'stock'=>]]

    public ?int $customerId = null;

    // Changed types slightly or cleaned up spacing to prevent casting errors from input strings
    public float $discount = 0;
public float $paidAmount = 0;

    public string $paymentMethod = 'cash';

   public function getSearchResultsProperty()
{
    if (strlen($this->productSearch) < 2) {
        return [];
    }

    // Convert search string to lowercase once
    $search = strtolower($this->productSearch);

    return Product::where('status', 'active')
        ->where('stock_quantity', '>', 0)
        ->where(function ($q) use ($search) {
            $q->whereRaw('LOWER(name) like ?', ["%{$search}%"])
              ->orWhereRaw('LOWER(model) like ?', ["%{$search}%"])
              ->orWhereRaw('LOWER(imei_serial) like ?', ["%{$search}%"]);
        })
        ->limit(8)
        ->get();
}

    public function addToCart($productId)
    {
        $product = Product::findOrFail($productId);

        if (isset($this->cart[$productId])) {
            if ($this->cart[$productId]['qty'] < $product->stock_quantity) {
                $this->cart[$productId]['qty']++;
            }
        } else {
            $this->cart[$productId] = [
                'name' => $product->name . ' ' . $product->model,
                'price' => $product->sale_price,
                'qty' => 1,
                'stock' => $product->stock_quantity,
            ];
        }

        $this->productSearch = '';
    }

    public function updateQty($productId, $qty)
    {
        if ($qty < 1) {
            $this->removeFromCart($productId);
            return;
        }

        if ($qty > $this->cart[$productId]['stock']) {
            $qty = $this->cart[$productId]['stock'];
        }

        $this->cart[$productId]['qty'] = (int) $qty;
    }

    public function removeFromCart($productId)
    {
        unset($this->cart[$productId]);
    }

    public function getSubtotalProperty()
    {
        return collect($this->cart)->sum(fn ($item) => $item['price'] * $item['qty']);
    }

    public function getTotalProperty()
    {
        return max(0, (float)$this->subtotal - (float)$this->discount);
    }

    public function getDueProperty()
    {
        return max(0, (float)$this->total - (float)$this->paidAmount);
    }

    public function confirmSale()
{
    $this->validate([
        'cart' => 'required|array|min:1',
        'paymentMethod' => 'required|in:cash,card,mobile_banking',
    ], [
        'cart.required' => 'Add at least one product to the cart.',
    ]);

    // 1. Opens parenthesis '(' and curly brace '{'
    DB::transaction(function () {
        $sale = Sale::create([
            'customer_id' => $this->customerId,
            'user_id' => Auth::id(),
            'invoice_no' => 'INV-' . now()->format('Ymd') . '-' . str_pad(Sale::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT),
            'total_amount' => $this->total,
            'discount' => $this->discount,
            'paid_amount' => $this->paidAmount,
            'due_amount' => $this->due,
            'payment_method' => $this->paymentMethod,
            'sale_date' => now()->toDateString(),
        ]);

        foreach ($this->cart as $productId => $item) {
            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $productId,
                'quantity' => $item['qty'],
                'unit_price' => $item['price'],
                'subtotal' => $item['price'] * $item['qty'],
            ]);

            Product::where('id', $productId)->decrement('stock_quantity', $item['qty']);
        }

        session()->flash('success', "Sale {$sale->invoice_no} recorded successfully.");
        $this->reset(['cart', 'customerId', 'discount', 'paidAmount', 'productSearch']);
        $this->paymentMethod = 'cash';

        $this->dispatch('sale-completed', invoiceNo: $sale->invoice_no);
    }); // <--- 2. CORRECTED: Closes curly brace '}' and parenthesis ')'
}

    public function render()
    {
        return view('livewire.product.sales.new-sale-component', [
            'customers' => Customer::orderBy('name')->get(),
        ]);
    }
}
