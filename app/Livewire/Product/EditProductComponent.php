<?php

namespace App\Livewire\Product;

use App\Models\Product;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app.base.base')]
class EditProductComponent extends Component
{
    public Product $product;

    public $name = '';
    public $country_code = '';
    public $color = '';
    public $purchase_price = '';
    public $sale_price = '';
    public $stock_quantity = 0;
    public $min_stock_alert = 5;
    public $status = 'active';

    public function mount(Product $product)
    {
        $this->product = $product;

        $this->name = $product->name;
        $this->country_code = $product->country_code ?? '';
        $this->color = $product->color ?? '';
        $this->purchase_price = (string) $product->purchase_price;
        $this->sale_price = (string) $product->sale_price;
        $this->stock_quantity = $product->stock_quantity;
        $this->min_stock_alert = $product->min_stock_alert;
        $this->status = $product->status;
    }

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

    public function update()
    {
        $this->validateForm();

        $this->product->update([
            'name' => trim($this->name),
            'country_code' => ! empty($this->country_code) ? strtoupper(trim($this->country_code)) : null,
            'color' => ! empty($this->color) ? trim($this->color) : null,
            'purchase_price' => $this->purchase_price,
            'sale_price' => $this->sale_price,
            'stock_quantity' => $this->stock_quantity,
            'min_stock_alert' => $this->min_stock_alert,
            'status' => $this->status,
        ]);

        session()->flash('success', 'Product updated successfully.');

        return $this->redirect(route('products.index'));
    }

    public function render()
    {
        return view('livewire.product.edit-product-component');
    }
}
