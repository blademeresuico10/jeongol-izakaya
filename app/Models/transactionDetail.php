<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class transactionDetail extends Model
{
    protected $fillable = [
        'transaction_id',
        'order_detail_id',
        'customer_id',
        'item_name',
        'quantity',
        'discount_amount',
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
    ];

    public function transaction()
    {
        return $this->belongsTo(transaction::class);
    }

    public function orders()
    {
        return $this->belongsTo(orders::class, 'order_detail_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(customers::class);
    }

    public function getTotalAttribute()
    {
        if (!$this->orders) {
            return 0;
        }

        $price = $this->orders->price ?? 0;
        $quantity = $this->quantity ?? 0;
        $discount = abs($this->discount_amount ?? 0);

        return ($price * $quantity) - $discount;
    }
}
