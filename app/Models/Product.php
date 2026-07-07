<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    // use SoftDeletes;
    protected $table = 'products';
    protected $fillable = [
        'category_id', 'brand_id', 'name', 'model', 'specification',
        'imei_serial', 'purchase_price', 'sale_price', 'stock_quantity',
        'min_stock_alert', 'status',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'sale_price'      => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    // Exchanges where this product was the item being traded in / returned
    public function oldExchanges()
    {
        return $this->hasMany(Exchange::class, 'old_product_id');
    }

    // Exchanges where this product was the item the customer took home
    public function newExchanges()
    {
        return $this->hasMany(Exchange::class, 'new_product_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock_quantity', '<=', 'min_stock_alert');
    }
}
