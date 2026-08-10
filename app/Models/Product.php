<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'category_id', 'brand_id', 'name', 'model', 'country_code',
        'purchase_price', 'sale_price', 'stock_quantity',
        'min_stock_alert', 'status', 'color',
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

    public function oldExchanges()
    {
        return $this->hasMany(Exchange::class, 'old_product_id');
    }

    public function newExchanges()
    {
        return $this->hasMany(Exchange::class, 'new_product_id');
    }

    public function purchaseItemImeis()
    {
        return $this->hasMany(PurchaseItemImei::class, 'product_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock_quantity', '<=', 'min_stock_alert');
    }

    // "iPhone 15 - Black (US)" — used in search results / pickers
    public function getDisplayNameAttribute(): string
    {
        $parts = array_filter([$this->name, $this->model]);
        $label = implode(' ', $parts);

        if ($this->color) {
            $label .= ' - ' . $this->color;
        }

        if ($this->country_code) {
            $label .= " ({$this->country_code})";
        }

        return $label;
    }

    // live stock from unsold IMEIs, falls back to stock_quantity for
    // non-serialized products (accessories, cables, etc.)
    public function getAvailableStockAttribute(): int
    {
        $imeiCount = $this->purchaseItemImeis()->where('is_sold', false)->count();

        return $imeiCount > 0 ? $imeiCount : $this->stock_quantity;
    }

    // find an existing product with the exact same name+model+color+country,
    // used to block duplicate creation
    public static function findDuplicate(string $name, ?string $model, ?string $color, ?string $countryCode)
    {
        return static::where('name', $name)
            ->where('model', $model)
            ->where('color', $color)
            ->where('country_code', $countryCode)
            ->first();
    }
}
