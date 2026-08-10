<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseItemImei extends Model
{
    protected $fillable = [
        'purchase_item_id',
        'product_id',
        'imei_serial',
        'color_attribute_id',
        'country_attribute_id',
        'is_sold',
        'sale_item_id',
    ];

    public function purchaseItem(): BelongsTo
    {
        return $this->belongsTo(PurchaseItem::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function colorAttribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class, 'color_attribute_id');
    }

    public function countryAttribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class, 'country_attribute_id');
    }
}
