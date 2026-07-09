<?php

namespace App\Livewire\Product;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app.base.base')]
class EditProductComponent extends Component
{
    public Product $product;

    public string $category_id = '';
    public string $brand_id = '';
    public string $name = '';
    public string $model = '';
    public string $specification = '';
    public string $imei_serial = '';
    public string $purchase_price = '';
    public string $sale_price = '';
    public string $stock_quantity = '';
    public string $min_stock_alert = '';
    public string $status = 'active';

    public function mount(Product $product)
    {
        $this->product = $product;

        $this->category_id = (string) $product->category_id;
        $this->brand_id = (string) $product->brand_id;
        $this->name = $product->name;
        $this->model = $product->model ?? '';
        $this->specification = $product->specification ?? '';
        $this->imei_serial = $product->imei_serial ?? '';
        $this->purchase_price = (string) $product->purchase_price;
        $this->sale_price = (string) $product->sale_price;
        $this->stock_quantity = (string) $product->stock_quantity;
        $this->min_stock_alert = (string) $product->min_stock_alert;
        $this->status = $product->status;
    }

    protected function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            // 'brand_id' => ['required', 'exists:brands,id'],
            'name' => ['required', 'string', 'max:150'],
            'model' => ['nullable', 'string', 'max:100'],
            'specification' => ['nullable', 'string'],
            'imei_serial' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('products', 'imei_serial')->ignore($this->product->id),
            ],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'min_stock_alert' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }

    protected function messages(): array
    {
        return [
            'imei_serial.unique' => 'This IMEI/serial is already assigned to another product.',
        ];
    }

    public function update()
    {
        $validated = $this->validate();

        foreach (['model', 'specification', 'imei_serial'] as $field) {
            if ($validated[$field] === '') {
                $validated[$field] = null;
            }
        }

        $this->product->update($validated);

        session()->flash('success', 'Product updated successfully.');

        return $this->redirect(route('products.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.product.edit-product-component', [
            'categories' => Category::where('status', 'active')->orderBy('name')->get(),
            'brands' => Brand::where('status', 'active')->orderBy('name')->get(),
        ]);
    }
}
