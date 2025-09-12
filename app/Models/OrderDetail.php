<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'customer_id',
        'user_id',
        'menu_id',
        'quantity',
        'order_price',
        'notes',
        'status',
        'is_added_order',
        'change_type',
        'previous_quantity',
        'previous_price',
        'change_timestamp',
    ];

    protected $casts = [
        'order_price' => 'decimal:2',
        'previous_price' => 'decimal:2',
        'change_timestamp' => 'datetime',
        'is_added_order' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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
