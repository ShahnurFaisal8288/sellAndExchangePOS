<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\Http\Request;

class PurchaseInvoiceController extends Controller
{
    public function show($id)
    {
        $purchase = Purchase::with(['items.product', 'items.imeis', 'supplier'])
            ->findOrFail($id);

        return view('purchases.invoice', compact('purchase'));
    }
}
