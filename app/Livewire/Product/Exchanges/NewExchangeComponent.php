<?php

namespace App\Livewire\Product\Exchanges;

use App\Models\Customer;
use App\Models\Exchange;
use App\Models\Product;
use App\Models\PurchaseItemImei;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app.base.base')]
class NewExchangeComponent extends Component
{
    // Customer Management
    public bool $is_new_customer = false;
    public ?int $customerId = null;
    public string $new_customer_name = '';
    public string $new_customer_phone = '';

    // Old Product (Trade-In) - Logged text-only without adding to stock inventory
    public string $oldProductName = '';
    public string $oldProductImei = '';
    public $oldProductReturnValue = 0;

    // Outgoing (New) Product
    public string $productSearch = '';
    public ?int $newProductId = null;
    public ?int $selectedImeiId = null;
    public string $selectedImeiNumber = '';
    public string $selectedProductName = '';
    public $newProductPrice = 0;

    // Transaction Details
    public $additionalPayment = 0;
    public string $paymentMethod = 'cash';
    public string $notes = '';
    public ?string $selectedCountryCode = null; // add this
    public ?string $selectedColor = null;        // add this

    public function toggleNewCustomer()
    {
        $this->is_new_customer = !$this->is_new_customer;
        $this->customerId = null;
        $this->new_customer_name = '';
        $this->new_customer_phone = '';
    }

    public function getSearchResultsProperty()
    {
        $search = trim($this->productSearch);

        if (strlen($search) < 2) {
            return collect();
        }

        $searchLower = strtolower($search);

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
            ->with([
                'purchaseItemImeis' => function ($q) {
                    $q->where('is_sold', false)->latest()->limit(10);
                }
            ])
            ->limit(10)
            ->get();
    }

    public function selectNewProduct($productId, $selectedImeiId = null, $selectedImei = null)
    {
        $product = Product::findOrFail($productId);
        $search = trim($this->productSearch);

        if (!$selectedImeiId && !empty($search)) {
            $matchedImei = PurchaseItemImei::where('product_id', $productId)
                ->where('is_sold', false)
                ->whereRaw('LOWER(imei_serial) = ?', [strtolower($search)])
                ->first();

            if ($matchedImei) {
                $selectedImei = $matchedImei->imei_serial;
                $selectedImeiId = $matchedImei->id;
            }
        }

        $this->newProductId = $product->id;
        $this->selectedProductName = trim($product->name . ' ' . ($product->model ?? ''));
        $this->selectedCountryCode = $product->country_code ?? null; // add this
        $this->selectedColor = $product->color ?? null;
        $this->newProductPrice = (float) $product->sale_price;
        $this->selectedImeiNumber = $selectedImei ?? '';
        $this->selectedImeiId = $selectedImeiId ?? null;

        $this->productSearch = '';
        $this->recalculate();
    }

    public function clearSelectedProduct()
    {
        $this->newProductId = null;
        $this->selectedImeiId = null;
        $this->selectedImeiNumber = '';
        $this->selectedProductName = '';
        $this->newProductPrice = 0;
        $this->selectedCountryCode = null; // add this
        $this->selectedColor = null;
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
        $price = (float) ($this->newProductPrice ?: 0);
        $return = (float) ($this->oldProductReturnValue ?: 0);
        $this->additionalPayment = round($price - $return, 2);
    }

    public function getCustomersProperty()
    {
        return Customer::orderBy('name')->get();
    }

    public function confirmExchange()
    {
        $rules = [
            'oldProductName' => 'required|string|max:150',
            'oldProductImei' => 'nullable|string|max:100',
            'oldProductReturnValue' => 'required|numeric|min:0',
            'newProductId' => 'required|exists:products,id',
            'newProductPrice' => 'required|numeric|min:0',
            'paymentMethod' => 'required|string|in:cash,card,mobile_banking,bank_transfer',
        ];

        if ($this->is_new_customer) {
            $rules['new_customer_name'] = 'required|string|max:100';
            $rules['new_customer_phone'] = 'required|string|max:20';
        }

        $this->validate($rules);

        $product = Product::find($this->newProductId);
        if (!$product || $product->stock_quantity < 1) {
            $this->addError('newProductId', 'Selected item is no longer in stock.');
            return;
        }

        if ($this->selectedImeiId) {
            $isSold = DB::table('purchase_item_imeis')->where('id', $this->selectedImeiId)->value('is_sold');
            if ($isSold) {
                $this->addError('newProductId', 'The selected IMEI has already been sold.');
                return;
            }
        }

        DB::transaction(function () use ($product) {
            $finalCustomerId = $this->customerId;

            if ($this->is_new_customer) {
                $customer = Customer::create([
                    'name' => $this->new_customer_name,
                    'phone' => $this->new_customer_phone,
                ]);
                $finalCustomerId = $customer->id;
            }

            $returnVal = (float) $this->oldProductReturnValue;
            $newPrice = (float) $this->newProductPrice;
            $cashDifference = $this->additionalPayment;

            // Formulate old product description text
            $oldProductDescription = $this->oldProductName . ($this->oldProductImei ? " [IMEI: {$this->oldProductImei}]" : '');

            // 1. Outgoing Sale Record (Treating trade-in value as part of payment/discount mechanism)
            $actualPaidAmount = $cashDifference > 0 ? $cashDifference : 0;
            $saleCount = Sale::whereDate('created_at', today())->count();

            $sale = Sale::create([
                'customer_id' => $finalCustomerId ?: null,
                'user_id' => Auth::id(),
                'invoice_no' => 'INV-' . now()->format('Ymd') . '-' . str_pad($saleCount + 1, 4, '0', STR_PAD_LEFT),
                'total_amount' => $newPrice,
                'discount' => $returnVal,
                'paid_amount' => $actualPaidAmount,
                'due_amount' => 0,
                'payment_method' => $this->paymentMethod,
                'sale_date' => now()->toDateString(),
            ]);

            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $this->newProductId,
                'quantity' => 1,
                'unit_price' => $newPrice,
                'subtotal' => $newPrice,
            ]);

            if ($this->selectedImeiId) {
                DB::table('purchase_item_imeis')
                    ->where('id', $this->selectedImeiId)
                    ->update(['is_sold' => true]);
            }

            $product->decrement('stock_quantity', 1);

            // 2. Create Exchange Log without creating any product/purchase record for the old trade-in item
            $exchange = Exchange::create([
                'exchange_type' => 'trade_in',
                'sale_id' => $sale->id,
                'purchase_id' => null, // No purchase record created
                'old_product_source' => 'external',
                'old_product_id' => null, // Not added to inventory catalog table
                'old_product_description' => $oldProductDescription,
                'new_product_id' => $this->newProductId,
                'condition' => 'resellable',
                'new_product_price' => $newPrice,
                'old_product_return_value' => $returnVal,
                'additional_payment' => $cashDifference,
                'exchange_date' => now()->toDateString(),
                'notes' => $this->notes ?: null,
            ]);

            session()->flash('success', "Exchange completed! Invoice #{$sale->invoice_no} created successfully.");

            $this->reset();
            $this->dispatch('exchange-completed', exchangeId: $exchange->id);
        });
    }

    public function render()
    {
        return view('livewire.product.exchanges.new-exchange-component');
    }
}
