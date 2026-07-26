<?php

use App\Http\Controllers\ExchangeController;
use App\Http\Controllers\SaleController;
use App\Livewire\Dashboard\DashboardComponent;
use App\Livewire\Product\Brand\BrandComponent;
use App\Livewire\Product\Category\CategoryComponent;
use App\Livewire\Product\CreateProductComponent;
use App\Livewire\Product\EditProductComponent;
use App\Livewire\Product\Exchanges\AllExchangeComponent;
use App\Livewire\Product\Exchanges\NewExchangeComponent;
use App\Livewire\Product\LowStockAlertComponent;
use App\Livewire\Product\PaymentCreate\PaymentCreateComponent;
use App\Livewire\Product\ProductComponent;
use App\Livewire\Product\Purchase\PurchaseComponent;
use App\Livewire\Product\Purchase\PurchaseCreateComponent;
use App\Livewire\Product\Purchase\PurchaseReturnComponent;
use App\Livewire\Product\Sales\AllSaleComponent;
use App\Livewire\Product\Sales\Customer\CustomerComponent;
use App\Livewire\Product\Sales\EditSaleComponent;
use App\Livewire\Product\Sales\NewSaleComponent;
use App\Livewire\Product\Supplier\SupplierComponent;
use App\Livewire\Reports\InventoryReportComponent;
use App\Livewire\Reports\ProfitAndLossReportComponent;
use App\Livewire\Reports\PurchaseReportComponent;
use App\Livewire\Reports\SalesReportComponent;
use Illuminate\Support\Facades\Route;

// Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', DashboardComponent::class)->name('dashboard');
     Route::get('/product/categories', CategoryComponent::class)->name('categories.index');
     Route::get('/product/brands', BrandComponent::class)->name('brands.index');
     Route::get('/product', ProductComponent::class)->name('products.index');
     Route::get('/products/create', CreateProductComponent::class)->name('products.create');
     Route::get('/products/{product}/edit',EditProductComponent::class)->name('products.edit');
     Route::get('/products/low-stock', LowStockAlertComponent::class)->name('products.low-stock');




    Route::get('/suppliers', SupplierComponent::class)->name('suppliers.index');


   Route::get('/sales', AllSaleComponent::class)->name('sales.index');
    Route::get('/sales/create', NewSaleComponent::class)->name('sales.create');
    Route::get('/customers', CustomerComponent::class)->name('customers.index');
    Route::get('/sales/{sale}/edit',EditSaleComponent::class)->name('sales.edit');
    Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
    Route::get('/sales/{sale}/print', [SaleController::class, 'print'])->name('sales.print');



    Route::get('/exchanges', AllExchangeComponent::class)->name('exchanges.index');
    Route::get('/exchanges/create', NewExchangeComponent::class)->name('exchanges.create');
    Route::get('/exchanges/{exchange}', [ExchangeController::class, 'show'])->name('exchanges.show');
    Route::get('/exchanges/{exchange}/print', [ExchangeController::class, 'print'])->name('exchanges.print');




    Route::get('/purchases', PurchaseComponent::class)->name('purchases.index');
    Route::get('/purchases/payment_create', PaymentCreateComponent::class)->name('payment_create');
    Route::get('/purchases/create', PurchaseCreateComponent::class)->name('purchases.create');
    Route::get('/purchases/{id}/edit', PurchaseCreateComponent::class)->name('purchases.edit');


     Route::get('/reports/sales', SalesReportComponent::class)->name('reports.sales');
    Route::get('/reports/purchases', PurchaseReportComponent::class)->name('reports.purchases');
    Route::get('/reports/inventory', InventoryReportComponent::class)->name('reports.inventory');
    Route::get('/reports/profit_loss', ProfitAndLossReportComponent::class)->name('reports.profit_loss');

    Route::get('/purchases/purchase_return/{id?}', PurchaseReturnComponent::class)->name('purchase_return');


});

require __DIR__.'/settings.php';
