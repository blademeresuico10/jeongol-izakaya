<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class reservation extends Model
{
    protected $fillable = [
    'pax',
    'advance_payment',
    'reservation_time',
    'reservation_end_time',
    'table_number',
    'customer_id',
    'user_id',
    'notes',
];



}
