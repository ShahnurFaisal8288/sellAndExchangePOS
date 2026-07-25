<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $table = 'sales';
    protected $fillable = [
        'customer_id',
        'user_id',
        'invoice_no',
        'total_amount',
        'discount',
        'paid_amount',
        'due_amount',
        'payment_method',
        'sale_date',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
        'sale_date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // public function items()
    // {
    //     return $this->hasMany(SaleItem::class);
    // }

    // A sale can have an associated exchange (with_receipt, warranty, trade_in)
    public function exchange()
    {
        return $this->hasOne(Exchange::class);
    }
    public function payments()
    {
        return $this->hasMany(SalePayment::class);
    }
    public function items()
    {
        return $this->hasMany(SaleItem::class, 'sale_id');
    }
}
