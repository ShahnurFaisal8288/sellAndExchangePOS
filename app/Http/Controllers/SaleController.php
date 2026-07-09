<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function show(Sale $sale)
    {
        $sale->load(['customer', 'user', 'items.product', 'payments.user']);
        return view('product.sales.show', compact('sale'));
    }
    public function print(Sale $sale)
    {
        $sale->load(['customer', 'user', 'items.product']);
        return view('product.sales.print', compact('sale'));
    }
}
