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

    public function orderDetail()
    {
        return $this->belongsTo(OrderDetail::class);
    }

    public function customer()
    {
        return $this->belongsTo(customers::class);
    }
}