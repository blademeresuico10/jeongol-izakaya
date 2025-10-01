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
        'name',             
        'contact',           
        'advance_payment',   
        'payment_method',    
        'payment_proof',     
        'ewallet_number',   
    ];

    public function reservation()
    {
        return $this->belongsTo(\App\Models\reservation::class, 'reservation_id', 'id');
    }

    public function ewalletDetail()
    {
        return $this->belongsTo(\App\Models\EwalletDetail::class, 'ewallet_number', 'id');
    }
}