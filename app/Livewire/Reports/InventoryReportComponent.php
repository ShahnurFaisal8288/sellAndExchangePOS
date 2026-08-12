<?php

namespace App\Livewire\Reports;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app.base.base')]
class InventoryReportComponent extends Component
{
    use WithPagination;

    public string $search = '';
    public string $categoryId = '';
    public string $brandId = '';
    public bool $lowStockOnly = false;

    public function updated($property): void
    {
        if (in_array($property, ['search', 'categoryId', 'brandId', 'lowStockOnly'])) {
            $this->resetPage();
        }
    }

    private function baseQuery()
    {
        return Product::query()
            ->where('status', 'active')
            ->when($this->search, fn ($q) => $q->where(function ($q2) {
                $q2->where('name', 'like', "%{$this->search}%")
                   ->orWhere('model', 'like', "%{$this->search}%")
                   ->orWhere('imei_serial', 'like', "%{$this->search}%");
            }))
            ->when($this->categoryId, fn ($q) => $q->where('category_id', $this->categoryId))
            ->when($this->brandId, fn ($q) => $q->where('brand_id', $this->brandId))
            ->when($this->lowStockOnly, fn ($q) => $q->whereColumn('stock_quantity', '<=', 'min_stock_alert'));
    }

    public function render()
    {
        $products = (clone $this->baseQuery())
            ->with(['category', 'brand'])
            ->orderBy('stock_quantity')
            ->paginate(20);

        $summary = (clone $this->baseQuery())->selectRaw('
            COUNT(*) as total_products,
            SUM(stock_quantity) as total_units,
            SUM(stock_quantity * purchase_price) as stock_value_cost,
            SUM(stock_quantity * sale_price) as stock_value_retail
        ')->first();

        return view('livewire.reports.inventory-report-component', [
            'products' => $products,
            'summary' => $summary,
            'categories' => Category::orderBy('name')->get(),
            'brands' => Brand::orderBy('name')->get(),
        ]);
    }
}
