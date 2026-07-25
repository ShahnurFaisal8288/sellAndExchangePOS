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
    public function updatingDateFrom() { $this->resetPage(); }
    public function updatingDateTo() { $this->resetPage(); }

    public function render()
    {
        $sales = Sale::with(['customer', 'user', 'items.product'])
            ->when($this->search, function ($q) {
                $searchLower = strtolower(trim($this->search));

                $q->where(function ($query) use ($searchLower) {
                    // Case-insensitive search on Invoice Number
                    $query->whereRaw('LOWER(invoice_no) LIKE ?', ["%{$searchLower}%"])
                        // Case-insensitive search on Customer Name & Phone
                        ->orWhereHas('customer', function ($customerQuery) use ($searchLower) {
                            $customerQuery->whereRaw('LOWER(name) LIKE ?', ["%{$searchLower}%"])
                                         ->orWhereRaw('LOWER(phone) LIKE ?', ["%{$searchLower}%"]);
                        })
                        // Case-insensitive search on Product Name, Model, or IMEI
                        ->orWhereHas('items', function ($itemQuery) use ($searchLower) {
                            $itemQuery->whereRaw('LOWER(imei_serial) LIKE ?', ["%{$searchLower}%"])
                                      ->orWhereHas('product', function ($productQuery) use ($searchLower) {
                                          $productQuery->whereRaw('LOWER(name) LIKE ?', ["%{$searchLower}%"])
                                                       ->orWhereRaw('LOWER(model) LIKE ?', ["%{$searchLower}%"]);
                                      });
                        });
                });
            })
            ->when($this->dateFrom, fn ($q) => $q->whereDate('sale_date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('sale_date', '<=', $this->dateTo))
            ->latest('sale_date')
            ->latest('id')
            ->paginate(15);

        return view('livewire.product.sales.all-sale-component', compact('sales'));
    }
}
