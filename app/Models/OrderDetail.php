<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $fillable = [
        'order_price',
        'quantity',
        'notes',
        'customer_id',
        'user_id',
        'menu_id',
        'is_added_order',
        'reservation_id',
        'status'
    ];
    public function menu()
    {
        return $this->belongsTo(\App\Models\menu::class, 'menu_id');
    }

    public function customer()
    {
        return $this->belongsTo(\App\Models\customers::class, 'customer_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function reservation()
    {
        return $this->belongsTo(reservation::class);
    }
}
