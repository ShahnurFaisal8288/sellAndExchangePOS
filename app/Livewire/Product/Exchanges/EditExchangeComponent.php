<?php

namespace App\Livewire\Product\Exchanges;

use App\Models\Customer;
use App\Models\Exchange;
use App\Models\Product;
use App\Models\PurchaseItemImei;
use Attribute;
use Auth;
use DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app.base.base')]
class EditExchangeComponent extends Component
{
    public Exchange $exchange;

    // Customer Management
    public bool $is_new_customer = false;
    public ?int $customerId = null;
    public string $new_customer_name = '';
    public string $new_customer_phone = '';

    // Old Product (Trade-In)
    public string $oldProductName = '';
    public string $oldProductImei = '';
    public $oldProductReturnValue = 0;
    public $oldProductSalePrice = 0;
    public ?string $oldProductColor = null;
    public ?string $oldProductCountryCode = null;

    // Outgoing (New) Product
    public string $productSearch = '';
    public ?int $newProductId = null;
    public ?int $selectedImeiId = null;
    public string $selectedImeiNumber = '';
    public string $selectedProductName = '';
    public $newProductPrice = 0;
    public ?string $selectedCountryCode = null;
    public ?string $selectedColor = null;

    // Transaction Details
    public $additionalPayment = 0;
    public array $payments = [];
    public string $notes = '';

    public function mount(Exchange $exchange)
    {
        $this->exchange = $exchange->load(['sale.customer', 'sale.payments', 'oldProduct', 'newProduct']);

        // Load Customer info from associated Sale
        $this->customerId = $exchange->sale?->customer_id;

        // Load Old Product (Trade-in) info
        $this->oldProductName = $exchange->oldProduct?->name ?? '';
        $this->oldProductImei = $exchange->oldProduct?->imei_serial ?? '';
        $this->oldProductReturnValue = $exchange->old_product_return_value;
        $this->oldProductSalePrice = $exchange->oldProduct?->sale_price ?? 0;
        $this->oldProductColor = $exchange->oldProduct?->color;
        $this->oldProductCountryCode = $exchange->oldProduct?->country_code;

        // Load New Product info
        if ($exchange->newProduct) {
            $this->newProductId = $exchange->newProduct->id;
            $this->selectedProductName = trim($exchange->newProduct->name . ' ' . ($exchange->newProduct->model ?? ''));
            $this->newProductPrice = $exchange->new_product_price;
            $this->selectedCountryCode = $exchange->newProduct->country_code;
            $this->selectedColor = $exchange->newProduct->color;
        }

        $this->notes = $exchange->notes ?? '';
        $this->additionalPayment = $exchange->additional_payment;

        // Load existing payments or create a default row
        if ($exchange->sale && $exchange->sale->payments->count() > 0) {
            foreach ($exchange->sale->payments as $payment) {
                $this->payments[] = [
                    'method' => $payment->payment_method,
                    'amount' => $payment->amount,
                    'notes' => $payment->notes,
                ];
            }
        } else {
            $this->addPaymentRow();
        }
    }

    public function toggleNewCustomer()
    {
        $this->is_new_customer = !$this->is_new_customer;
        $this->customerId = null;
        $this->new_customer_name = '';
        $this->new_customer_phone = '';
    }

    public function getColorsProperty()
    {
        return \App\Models\Attribute::where('name', 'Color')->orderBy('label')->get();
    }

    public function getCountriesProperty()
    {
        return \App\Models\Attribute::where('name', 'Country')->orderBy('id')->get();
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
                    $q->where('is_sold', false)
                        ->with(['colorAttribute', 'countryAttribute'])
                        ->latest()
                        ->limit(10);
                }
            ])
            ->limit(10)
            ->get();
    }

    public function selectNewProduct($productId, $selectedImeiId = null, $selectedImei = null)
    {
        $product = Product::findOrFail($productId);
        $search = trim($this->productSearch);

        $imeiColor = null;
        $imeiCountry = null;

        if (!$selectedImeiId && !empty($search)) {
            $matchedImei = PurchaseItemImei::where('product_id', $productId)
                ->where('is_sold', false)
                ->whereRaw('LOWER(imei_serial) = ?', [strtolower($search)])
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

        $this->newProductId = $product->id;
        $this->selectedProductName = trim($product->name . ' ' . ($product->model ?? ''));
        $this->selectedCountryCode = $imeiCountry ?? $product->country_code ?? null;
        $this->selectedColor = $imeiColor ?? $product->color ?? null;
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
        $this->selectedCountryCode = null;
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

    public function updateExchange()
    {
        $rules = [
            'oldProductName' => 'required|string|max:150',
            'oldProductImei' => 'nullable|string|max:100',
            'oldProductReturnValue' => 'required|numeric|min:0',
            'oldProductSalePrice' => 'required|numeric|min:0',
            'newProductId' => 'required|exists:products,id',
            'newProductPrice' => 'required|numeric|min:0',
        ];

        if ($this->is_new_customer) {
            $rules['new_customer_name'] = 'required|string|max:100';
            $rules['new_customer_phone'] = 'required|string|max:20';
        }

        if ($this->additionalPayment > 0) {
            $rules['payments'] = 'required|array|min:1';
            $rules['payments.*.method'] = 'required|in:cash,card,mobile_banking';
            $rules['payments.*.amount'] = 'required|numeric|min:0';
        }

        $this->validate($rules);

        DB::transaction(function () {
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
            $oldProductDescription = $this->oldProductName . ($this->oldProductImei ? " [IMEI: {$this->oldProductImei}]" : '');

            // 1. Update Old Product (Trade-In) record
            if ($this->exchange->oldProduct) {
                $this->exchange->oldProduct->update([
                    'name' => $this->oldProductName,
                    'imei_serial' => $this->oldProductImei ?: null,
                    'purchase_price' => $returnVal,
                    'sale_price' => (float) $this->oldProductSalePrice,
                    'color' => $this->oldProductColor ?: null,
                    'country_code' => $this->oldProductCountryCode ?: null,
                ]);
            }

            // 2. Handle stock adjustment for old product change if needed, or update Sale & Sale Items
            $sale = $this->exchange->sale;
            if ($sale) {
                $actualPaidAmount = $cashDifference > 0 ? min($this->paidAmount, $cashDifference) : 0;
                $actualDue = $cashDifference > 0 ? max(0, $cashDifference - $this->paidAmount) : 0;

                $sale->update([
                    'customer_id' => $finalCustomerId ?: null,
                    'total_amount' => max(0, $newPrice - $returnVal),
                    'discount' => $returnVal,
                    'paid_amount' => $actualPaidAmount,
                    'due_amount' => $actualDue,
                ]);

                // Update Sale Item
                $saleItem = $sale->items()->first();
                if ($saleItem) {
                    // Revert old product stock count if item changed
                    if ($saleItem->product_id != $this->newProductId) {
                        Product::where('id', $saleItem->product_id)->increment('stock_quantity', 1);
                        Product::where('id', $this->newProductId)->decrement('stock_quantity', 1);
                    }

                    $saleItem->update([
                        'product_id' => $this->newProductId,
                        'unit_price' => $newPrice,
                        'subtotal' => $newPrice,
                    ]);
                }

                // Refresh payments
                $sale->payments()->delete();
                if ($cashDifference > 0) {
                    foreach ($this->payments as $payment) {
                        if ((float)$payment['amount'] > 0) {
                            $sale->payments()->create([
                                'payment_method' => $payment['method'],
                                'amount' => (float) $payment['amount'],
                                'notes' => trim($payment['notes'] ?? '') ?: null,
                                'user_id' => Auth::id() ?? 1,
                                'paid_date' => now()->toDateString(),
                            ]);
                        }
                    }
                }
            }

            // 3. Update Exchange Log entry
            $this->exchange->update([
                'old_product_description' => $oldProductDescription,
                'imei_serial' => $this->oldProductImei ?: null,
                'new_product_id' => $this->newProductId,
                'new_product_price' => $newPrice,
                'old_product_return_value' => $returnVal,
                'additional_payment' => $cashDifference,
                'notes' => $this->notes ?: null,
            ]);

            session()->flash('success', "Exchange transaction updated successfully!");
        });

        return redirect()->route('exchanges.index');
    }

    public function render()
    {
        return view('livewire.product.exchanges.edit-exchange-component');
    }
}
