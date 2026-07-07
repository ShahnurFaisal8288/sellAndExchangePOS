<?php

use App\Livewire\Dashboard\DashboardComponent;
use App\Livewire\Product\Brand\BrandComponent;
use App\Livewire\Product\Category\CategoryComponent;
use App\Livewire\Product\CreateProductComponent;
use App\Livewire\Product\EditProductComponent;
use App\Livewire\Product\ProductComponent;
use App\Livewire\Product\Purchase\PurchaseComponent;
use App\Livewire\Product\Purchase\PurchaseCreateComponent;
use App\Livewire\Product\Sales\Customer\CustomerComponent;
use App\Livewire\Product\Supplier\SupplierComponent;
use Illuminate\Support\Facades\Route;

// Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', DashboardComponent::class)->name('home');
     Route::get('/product/categories', CategoryComponent::class)->name('categories.index');
     Route::get('/product/brands', BrandComponent::class)->name('brands.index');
     Route::get('/product', ProductComponent::class)->name('products.index');
     Route::get('/products/create', CreateProductComponent::class)->name('products.create');
     Route::get('/products/{product}/edit',EditProductComponent::class)->name('products.edit');

    Route::get('/suppliers', SupplierComponent::class)->name('suppliers.index');
    Route::get('/customers', CustomerComponent::class)->name('customers.index');
    Route::get('/purchases', PurchaseComponent::class)->name('purchases.index');
    Route::get('/purchases/create', PurchaseCreateComponent::class)->name('purchases.create');
Route::get('/purchases/{id}/edit', PurchaseCreateComponent::class)->name('purchases.edit');


});

require __DIR__.'/settings.php';
