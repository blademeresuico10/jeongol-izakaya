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
        'reservation_id',
    ];

    public function reservation()
    {
        return $this->belongsTo(reservation::class);
    }

    public function menu()
    {
        return $this->belongsTo(menu::class);
    }

    public function customer()
    {
        return $this->belongsTo(customers::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
