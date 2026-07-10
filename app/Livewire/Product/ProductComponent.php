<?php

namespace App\Livewire\Product;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\QueryException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
#[Layout('layouts.app.base.base')]
class ProductComponent extends Component
{
    use WithPagination;

    // ----- Filters -----
    public string $search = '';
    public string $categoryFilter = '';
    public string $brandFilter = '';
    public bool $lowStockOnly = false;

    // ----- Form state -----
    public bool $showModal = false;
    public ?int $editingId = null;

    public string $category_id = '';
    public string $brand_id = '';
    public string $name = '';
    public string $model = '';
    public string $specification = '';
    public string $imei_serial = '';
    public string $purchase_price = '';
    public string $sale_price = '';
    public string $stock_quantity = '0';
    public string $min_stock_alert = '0';
    public string $status = 'active';

    // ----- Delete confirmation -----
    public ?int $deletingId = null;

    protected function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'brand_id' => ['required', 'exists:brands,id'],
            'name' => ['required', 'string', 'max:150'],
            'model' => ['nullable', 'string', 'max:100'],
            'specification' => ['nullable', 'string'],
            'imei_serial' => [
                'nullable',
                'string',
                'max:100',
                'unique:products,imei_serial,' . ($this->editingId ?? 'NULL') . ',id',
            ],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'min_stock_alert' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function updatingBrandFilter(): void
    {
        $this->resetPage();
    }

    public function updatingLowStockOnly(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(int $productId): void
    {
        $product = Product::findOrFail($productId);

        $this->editingId = $product->id;
        $this->category_id = (string) $product->category_id;
        $this->brand_id = (string) $product->brand_id;
        $this->name = $product->name;
        $this->model = (string) $product->model;
        $this->specification = (string) $product->specification;
        $this->imei_serial = (string) $product->imei_serial;
        $this->purchase_price = (string) $product->purchase_price;
        $this->sale_price = (string) $product->sale_price;
        $this->stock_quantity = (string) $product->stock_quantity;
        $this->min_stock_alert = (string) $product->min_stock_alert;
        $this->status = $product->status;

        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function save(): void
    {
        $validated = $this->validate();

        // Normalize blanks to null for nullable text fields.
        foreach (['model', 'specification', 'imei_serial'] as $field) {
            if ($validated[$field] === '') {
                $validated[$field] = null;
            }
        }

        if ($this->editingId) {
            Product::findOrFail($this->editingId)->update($validated);
            session()->flash('success', 'Product updated.');
        } else {
            Product::create($validated);
            session()->flash('success', 'Product created.');
        }

        $this->closeModal();
    }

    public function confirmDelete(int $productId): void
    {
        $this->deletingId = $productId;
    }

    public function cancelDelete(): void
    {
        $this->deletingId = null;
    }

    public function delete(int $productId): void
    {
        $product = Product::findOrFail($productId);

        try {
            $product->delete();
        } catch (QueryException $e) {
            if ($e->getCode() === '23503') {
                session()->flash('error', "This product can't be deleted because it has existing purchase or sales records. You can deactivate it instead.");
                return;
            }
            throw $e;
        }

        session()->flash('success', 'Product deleted.');
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingId',
            'category_id',
            'brand_id',
            'name',
            'model',
            'specification',
            'imei_serial',
            'purchase_price',
            'sale_price',
            'stock_quantity',
            'min_stock_alert',
        ]);
        $this->status = 'active';
        $this->resetErrorBag();
        $this->resetValidation();
    }
    public function render()
    {
        $products = Product::query()
            ->with(['category', 'brand'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('model', 'like', "%{$this->search}%")
                        ->orWhere('imei_serial', 'like', "%{$this->search}%");
                });
            })
            ->when($this->categoryFilter, fn($query) => $query->where('category_id', $this->categoryFilter))
            ->when($this->brandFilter, fn($query) => $query->where('brand_id', $this->brandFilter))
            ->when($this->lowStockOnly, fn($query) => $query->lowStock())
            ->latest('id')
            ->paginate(10);
        return view('livewire.product.product-component', [
            'products' => $products,
            'categories' => Category::where('status', 'active')->orderBy('name')->get(),
            'brands' => Brand::where('status', 'active')->orderBy('name')->get(),
        ]);
    }
}
