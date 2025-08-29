<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'subtotal',
        'discount_amount',
        'total_amount',
        'payment_method',
        'status',
        'reservation_id',
        'customer_id',
        'cashier_id'
    ];

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function customer()
    {
        return $this->belongsTo(customers::class);
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}
