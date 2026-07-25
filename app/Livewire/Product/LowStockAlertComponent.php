<?php

namespace App\Livewire\Product;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app.base.base')]
class LowStockAlertComponent extends Component
{
    use WithPagination;

    public string $search = '';

    protected $queryString = ['search'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        // Fetch products where available stock <= min_stock_alert
        $products = Product::query()
            ->with(['category'])
            // Dynamic count of available (unsold) IMEIs
            ->withCount(['purchaseItemImeis as available_stock' => function ($q) {
                $q->where('is_sold', false);
            }])
            ->where('status', 'active')
            ->where(function ($query) {
                // Check either dynamic unsold IMEIs or static stock_quantity column
                $query->whereRaw('(SELECT COUNT(*) FROM purchase_item_imeis WHERE purchase_item_imeis.product_id = products.id AND is_sold = false) <= products.min_stock_alert')
                      ->orWhere('stock_quantity', '<=', DB::raw('min_stock_alert'));
            })
            ->when($this->search, function ($query) {
                $searchTerm = "%{$this->search}%";
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'like', $searchTerm)
                      ->orWhere('model', 'like', $searchTerm)
                      // Match ONLY unsold IMEIs
                      ->orWhereHas('purchaseItemImeis', function ($imeiQuery) use ($searchTerm) {
                          $imeiQuery->where('is_sold', false)
                                    ->where('imei_serial', 'like', $searchTerm);
                      });
                });
            })
            ->orderBy('stock_quantity', 'asc')
            ->paginate(15);

        // Summary Statistics for KPI Cards
        $totalOutStock = Product::where('status', 'active')
            ->where('stock_quantity', '<=', 0)
            ->count();

        return view('livewire.product.low-stock-alert-component', [
            'products' => $products,
            'totalOutStock' => $totalOutStock,
        ]);
    }
}
