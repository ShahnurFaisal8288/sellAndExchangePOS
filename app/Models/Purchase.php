<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $table = 'purchases';
    protected $fillable = [
        'supplier_id', 'source_type', 'user_id', 'invoice_no',
        'total_amount', 'paid_amount', 'due_amount', 'purchase_date',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_amount'  => 'decimal:2',
        'due_amount'   => 'decimal:2',
        'purchase_date' => 'date',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    // Only present when source_type = customer_trade_in
    public function exchange()
    {
        return $this->hasOne(Exchange::class);
    }

    public function scopeTradeIns($query)
    {
        return $query->where('source_type', 'customer_trade_in');
    }

    public function scopeFromSuppliers($query)
    {
        return $query->where('source_type', 'supplier');
    }
}
