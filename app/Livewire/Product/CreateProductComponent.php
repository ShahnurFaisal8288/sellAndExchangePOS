<?php

namespace App\Livewire\Product;

use App\Models\Product;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app.base.base')]
class CreateProductComponent extends Component
{
    public $name = '';
    public $country_code = '';
    public $purchase_price = '';
    public $sale_price = '';
    public $stock_quantity = 0;
    public $min_stock_alert = 5;
    public $status = 'active';
    public $color = '';

    private function validateForm(): void
    {
        $errors = [];

        if (blank($this->name)) {
            $errors['name'] = 'Product name is required.';
        }

        if ($this->purchase_price === '' || $this->purchase_price === null || (float) $this->purchase_price < 0) {
            $errors['purchase_price'] = 'Purchase price must be zero or more.';
        }

        if ($this->sale_price === '' || $this->sale_price === null || (float) $this->sale_price < 0) {
            $errors['sale_price'] = 'Sale price must be zero or more.';
        }

        if ((int) $this->stock_quantity < 0) {
            $errors['stock_quantity'] = 'Stock quantity must be zero or more.';
        }

        if ((int) $this->min_stock_alert < 0) {
            $errors['min_stock_alert'] = 'Minimum stock alert must be zero or more.';
        }

        if (! in_array($this->status, ['active', 'inactive'], true)) {
            $errors['status'] = 'Invalid status selected.';
        }

        if (! empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }

    public function save()
    {
        $this->validateForm();


        $countryCode = ! empty($this->country_code) ? strtoupper(trim($this->country_code)) : null;
        $color = ! empty($this->color) ? trim($this->color) : null;
        Product::create([
            'category_id' => null,
            'brand_id' => null,
            'name' => trim($this->name),
            'country_code' => $countryCode,
            'color' => $color,
            'purchase_price' => $this->purchase_price,
            'sale_price' => $this->sale_price,
            'stock_quantity' => $this->stock_quantity,
            'min_stock_alert' => $this->min_stock_alert,
            'status' => $this->status,
        ]);

        session()->flash('message', 'Product created successfully.');

        return $this->redirect(route('products.index'));
    }

    public function render()
    {
        return view('livewire.product.create-product-component');
    }
}
