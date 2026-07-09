<?php

namespace App\Livewire\Product\Sales;

use App\Models\Sale;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
#[Layout('layouts.app.base.base')]

class AllSaleComponent extends Component
{
    use WithPagination;

    public string $search = '';
    public string $dateFrom = '';
    public string $dateTo = '';

    protected $queryString = ['search'];

    public function updatingSearch() { $this->resetPage(); }
    public function render()
    {
        $sales = Sale::with(['customer', 'user'])
            ->when($this->search, fn ($q) => $q->where('invoice_no', 'like', "%{$this->search}%"))
            ->when($this->dateFrom, fn ($q) => $q->whereDate('sale_date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('sale_date', '<=', $this->dateTo))
            ->latest('sale_date')
            ->paginate(15);
        return view('livewire.product.sales.all-sale-component', compact('sales'));
    }
}
