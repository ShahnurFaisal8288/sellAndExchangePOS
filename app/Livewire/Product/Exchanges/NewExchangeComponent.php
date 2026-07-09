<?php

namespace App\Livewire\Product\Exchanges;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Exchange;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Sale;
use App\Models\SaleItem;
use Auth;
use DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
#[Layout('layouts.app.base.base')]

class NewExchangeComponent extends Component
{
    public string $exchangeType = 'trade_in'; // with_receipt | no_receipt | warranty | trade_in

    // Old product side
    public string $oldProductSource = 'external'; // this_shop | external
    public ?int $oldProductId = null;
    public string $oldProductSearch = '';
    public string $oldProductDescription = '';

    // New product row fields (only used for trade_in)
    public ?int $newRowCategoryId = null;
    public string $newRowName = '';
    public string $newRowModel = '';
    public string $newRowSpecification = '';

    public string $condition = 'resellable'; // resellable | damaged

    // FIXED: Removed invisible space and strict type hint to handle empty input fields elegantly
    public $oldProductReturnValue = 0;

    // New product side (customer takes home)
    public ?int $customerId = null;
    public ?int $newProductId = null;
    public string $newProductSearch = '';
    public $newProductPrice = 0;

    public $additionalPayment = 0;
    public string $notes = '';

    public function getOldProductResultsProperty()
    {
        if (strlen($this->oldProductSearch) < 2) return [];

        // Force lowercase for a case-insensitive lookup guarantee
        $search = strtolower($this->oldProductSearch);

        return Product::whereRaw('LOWER(name) like ?', ["%{$search}%"])
            ->orWhereRaw('LOWER(imei_serial) like ?', ["%{$search}%"])
            ->limit(6)->get();
    }

    public function getNewProductResultsProperty()
    {
        if (strlen($this->newProductSearch) < 2) return [];

        $search = strtolower($this->newProductSearch);

        return Product::where('status', 'active')
            ->where('stock_quantity', '>', 0)
            ->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(name) like ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(imei_serial) like ?', ["%{$search}%"]);
            })->limit(6)->get();
    }

    public function selectOldProduct($id)
    {
        $p = Product::find($id);
        $this->oldProductId = $id;
        $this->oldProductSearch = $p->name . ' ' . $p->model;
    }

    public function selectNewProduct($id)
    {
        $p = Product::find($id);
        $this->newProductId = $id;
        $this->newProductSearch = $p->name . ' ' . $p->model;
        $this->newProductPrice = $p->sale_price;
        $this->recalculate();
    }

    public function updatedOldProductReturnValue()
    {
        $this->recalculate();
    }

    public function updatedNewProductPrice()
    {
        $this->recalculate();
    }

    protected function recalculate()
    {
        $price = (float)($this->newProductPrice ?: 0);
        $return = (float)($this->oldProductReturnValue ?: 0);
        $this->additionalPayment = round($price - $return, 2);
    }

    public function getCategoriesProperty()
    {
        return Category::where('status', 'active')->orderBy('name')->get();
    }

    public function getCustomersProperty()
    {
        return Customer::orderBy('name')->get();
    }

    public function confirmExchange()
    {
        $rules = [
            'exchangeType' => 'required|in:with_receipt,no_receipt,warranty,trade_in',
            'newProductId' => 'required|exists:products,id',
            'newProductPrice' => 'required|numeric|min:0',
            'oldProductReturnValue' => 'required|numeric|min:0',
            'condition' => 'required|in:resellable,damaged',
        ];

        if ($this->exchangeType === 'trade_in') {
            $rules['newRowCategoryId'] = 'required|exists:categories,id';
            $rules['newRowName'] = 'required|string|max:150';
        } elseif (in_array($this->exchangeType, ['with_receipt', 'warranty'])) {
            $rules['oldProductId'] = 'required|exists:products,id';
        }

        $this->validate($rules);

        DB::transaction(function () {
            $sale = null;
            $purchase = null;

            $returnVal = (float)$this->oldProductReturnValue;
            $newPrice = (float)$this->newProductPrice;

            if ($this->exchangeType === 'trade_in') {
                $oldProduct = Product::create([
                    'category_id' => $this->newRowCategoryId,
                    'brand_id' => null,
                    'name' => $this->newRowName,
                    'model' => $this->newRowModel,
                    'specification' => $this->newRowSpecification,
                    'purchase_price' => $returnVal,
                    'sale_price' => 0,
                    'stock_quantity' => 0,
                    'min_stock_alert' => 1,
                    'status' => 'active',
                ]);

                $purchase = Purchase::create([
                    'supplier_id' => null,
                    'source_type' => 'customer_trade_in',
                    'user_id' => Auth::id(),
                    'invoice_no' => 'TRD-' . now()->format('Ymd') . '-' . str_pad(Purchase::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT),
                    'total_amount' => $returnVal,
                    'paid_amount' => $returnVal,
                    'due_amount' => 0,
                    'purchase_date' => now()->toDateString(),
                ]);

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $oldProduct->id,
                    'quantity' => 1,
                    'unit_price' => $returnVal,
                    'subtotal' => $returnVal,
                ]);
                $oldProduct->increment('stock_quantity', 1);

                $this->oldProductId = $oldProduct->id;
            }

            if ($this->exchangeType !== 'no_receipt') {
                $sale = Sale::create([
                    'customer_id' => $this->customerId ?: null,
                    'user_id' => Auth::id(),
                    'invoice_no' => 'INV-' . now()->format('Ymd') . '-' . str_pad(Sale::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT),
                    'total_amount' => $newPrice,
                    'discount' => 0,
                    'paid_amount' => $newPrice,
                    'due_amount' => 0,
                    'payment_method' => 'cash',
                    'sale_date' => now()->toDateString(),
                ]);

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $this->newProductId,
                    'quantity' => 1,
                    'unit_price' => $newPrice,
                    'subtotal' => $newPrice,
                ]);
                Product::where('id', $this->newProductId)->decrement('stock_quantity', 1);
            }

            if ($this->condition === 'resellable'
                && $this->oldProductSource === 'this_shop'
                && $this->oldProductId
                && $this->exchangeType !== 'trade_in') {
                Product::where('id', $this->oldProductId)->increment('stock_quantity', 1);
            }

            $exchange = Exchange::create([
                'exchange_type' => $this->exchangeType,
                'sale_id' => $sale?->id,
                'purchase_id' => $purchase?->id,
                'old_product_source' => $this->oldProductSource,
                'old_product_id' => $this->oldProductId,
                'old_product_description' => $this->oldProductDescription ?: null,
                'new_product_id' => $this->newProductId,
                'condition' => $this->condition,
                'new_product_price' => $newPrice,
                'old_product_return_value' => $returnVal,
                'additional_payment' => $this->additionalPayment,
                'exchange_date' => now()->toDateString(),
                'notes' => $this->notes ?: null,
            ]);

            session()->flash('success', "Exchange #{$exchange->id} recorded successfully.");

            $this->reset();
            $this->exchangeType = 'trade_in';
            $this->oldProductSource = 'external';
            $this->condition = 'resellable';

            $this->dispatch('exchange-completed', exchangeId: $exchange->id);
        });
    }

    public function render()
    {
        return view('livewire.product.exchanges.new-exchange-component');
    }
}
