<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'pax',
        'advance_payment',
        'reservation_time',
        'reservation_end_time',
        'table_id',
        'customer_id',
        'user_id',
        'status',
    ];

    public function orderDetails()
    {
        return $this->hasMany(\App\Models\OrderDetail::class);
    }

    public function customer()
    {
        return $this->belongsTo(\App\Models\customers::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function payment()
    {
        return $this->hasOne(\App\Models\reservationPayment::class);
    }
}
