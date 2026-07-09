<?php

namespace App\Livewire\Product;

use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
#[Layout('layouts.app.base.base')]
class LowStockAlertComponent extends Component
{
    use WithPagination;

    public string $search = '';

    protected $queryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function render()
    {
        $products = Product::query()
            ->whereColumn('stock_quantity', '<=', 'min_stock_alert')
            ->where('status', 'active')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                      ->orWhere('model', 'like', "%{$this->search}%")
                      ->orWhere('imei_serial', 'like', "%{$this->search}%");
                });
            })
            ->orderBy('stock_quantity', 'asc')
            ->paginate(15);
        return view('livewire.product.low-stock-alert-component', [
            'products' => $products,
        ]);
    }
}
