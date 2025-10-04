<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class reservationPayment extends Model
{
    use HasFactory;

    protected $table = 'reservation_payment_details';

    protected $fillable = [
        'reservation_id',
        'registered_name',
        'registered_number',
        'advance_payment',
        'payment_method',
        'payment_proof',
        'ewallet_id',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'reservation_id', 'id');
    }
    public function ewalletDetail()
    {
        return $this->belongsTo(EwalletDetail::class, 'ewallet_id', 'id');
    }
    
}
