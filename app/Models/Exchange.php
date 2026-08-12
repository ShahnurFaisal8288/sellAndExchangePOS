<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exchange extends Model
{
    protected $table = 'exchanges';
    protected $fillable = [
        'exchange_type', 'sale_id', 'purchase_id', 'old_product_source',
        'old_product_id', 'old_product_description', 'new_product_id',
        'condition', 'new_product_price', 'old_product_return_value',
        'additional_payment', 'exchange_date', 'notes','imei_serial'
    ];

    protected $casts = [
        'new_product_price'         => 'decimal:2',
        'old_product_return_value'  => 'decimal:2',
        'additional_payment'        => 'decimal:2',
        'exchange_date'             => 'date',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    // Item being returned/traded in
    public function oldProduct()
    {
        return $this->belongsTo(Product::class, 'old_product_id');
    }

    // Item the customer takes home
    public function newProduct()
    {
        return $this->belongsTo(Product::class, 'new_product_id');
    }

    public function scopeTradeIns($query)
    {
        return $query->where('exchange_type', 'trade_in');
    }

    public function scopeResellable($query)
    {
        return $query->where('condition', 'resellable');
    }
}
