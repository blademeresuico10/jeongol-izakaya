<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class reservationPayment extends Model
{
    use HasFactory;

     protected $fillable = [
        'reservation_id',
        'registered_name',
        'number',         
        'amount',
        'method',         
        'ref_no',
        'proof_path',
        'status',
    ];

    public function reservation()
    {
        return $this->belongsTo(\App\Models\reservation::class, 'reservation_id', 'id');
    }
}
