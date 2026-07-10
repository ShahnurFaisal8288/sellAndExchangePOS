<?php

namespace App\Livewire\Product;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app.base.base')]
class CreateProductComponent extends Component
{
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

    protected function rules(): array
    {
        return [
            'category_id'     => ['required', 'exists:categories,id'],
            // 'brand_id'        => ['required', 'exists:brands,id'],
            'name'            => ['required', 'string', 'max:150'],
            'model'           => ['nullable', 'string', 'max:100'],
            'specification'   => ['nullable', 'string'],
            'imei_serial'     => ['nullable', 'string', 'max:100', 'unique:products,imei_serial'],
            'purchase_price'  => ['required', 'numeric', 'min:0'],
            'sale_price'      => ['required', 'numeric', 'min:0'],
            'stock_quantity'  => ['required', 'integer', 'min:0'],
            'min_stock_alert' => ['required', 'integer', 'min:0'],
            'status'          => ['required', 'in:active,inactive'],
        ];
    }

    protected function messages(): array
    {
        return [
            'imei_serial.unique' => 'This IMEI/serial is already assigned to another product.',
        ];
    }

    public function save()
    {
        $validated = $this->validate();

        // Normalize blanks to null for nullable text fields.
        foreach (['model', 'specification', 'imei_serial'] as $field) {
            if ($validated[$field] === '') {
                $validated[$field] = null;
            }
        }

        Product::create($validated);

        session()->flash('success', 'Product created successfully.');

        return $this->redirect(route('products.index'));
    }

    public function render()
    {
        return view('livewire.product.create-product-component', [
            'categories' => Category::where('status', 'active')->orderBy('name')->get(),
            'brands'     => Brand::where('status', 'active')->orderBy('name')->get(),
        ]);
    }
}
