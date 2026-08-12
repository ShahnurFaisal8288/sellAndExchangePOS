<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use Livewire\Component;

class GlobalSearchComponent extends Component
{
    public string $query = '';

    public function getResultsProperty()
    {
        $q = trim($this->query);

        if (strlen($q) < 2) {
            return null;
        }

        $qLower = strtolower($q);

        $purchases = Purchase::where('invoice_no', 'LIKE', "%{$q}%")
            ->with('supplier')
            ->latest()
            ->limit(5)
            ->get();

        $sales = Sale::where('invoice_no', 'LIKE', "%{$q}%")
            ->with('customer')
            ->latest()
            ->limit(5)
            ->get();

        $customers = Customer::where(function ($qq) use ($qLower) {
                $qq->whereRaw('LOWER(name) LIKE ?', ["%{$qLower}%"])
                   ->orWhere('phone', 'LIKE', "%{$qLower}%");
            })
            ->limit(5)
            ->get();

        $products = Product::where(function ($qq) use ($qLower) {
                $qq->whereRaw('LOWER(name) LIKE ?', ["%{$qLower}%"])
                   ->orWhereHas('purchaseItemImeis', function ($imeiQuery) use ($qLower) {
                        $imeiQuery->whereRaw('LOWER(imei_serial) LIKE ?', ["%{$qLower}%"]);
                   });
            })
            ->limit(5)
            ->get();

        if ($purchases->isEmpty() && $sales->isEmpty() && $customers->isEmpty() && $products->isEmpty()) {
            return [];
        }

        return [
            'purchases' => $purchases,
            'sales' => $sales,
            'customers' => $customers,
            'products' => $products,
        ];
    }
    public function render()
    {
        return view('livewire.global-search-component');
    }
}
