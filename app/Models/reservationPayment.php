<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'amount',
        'payment_method',
        'ref_no',
        'status',
        'name'
    ];

    public function reservation()
    {
        return $this->belongsTo(\App\Models\Reservation::class);
    }
}
