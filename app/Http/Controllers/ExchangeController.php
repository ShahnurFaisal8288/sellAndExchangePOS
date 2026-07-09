<?php

namespace App\Http\Controllers;

use App\Models\Exchange;
use Illuminate\Http\Request;

class ExchangeController extends Controller
{
    public function show(Exchange $exchange)
    {
        $exchange->load([
            'sale.customer', 'sale.user',
            'purchase',
            'oldProduct', 'newProduct',
        ]);

        return view('product.exchanges.show', compact('exchange'));
    }

    public function print(Exchange $exchange)
    {
        $exchange->load([
            'sale.customer', 'sale.user',
            'purchase',
            'oldProduct', 'newProduct',
        ]);

        return view('product.exchanges.print', compact('exchange'));
    }
}
