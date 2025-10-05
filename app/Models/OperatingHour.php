<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperatingHour extends Model
{
    public $timestamps = false;
    protected $table = 'operating_hours';

    protected $fillable = [
        'is_default',
        'date',
        'open_time',
        'close_time',
        'is_closed'
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_closed' => 'boolean',
        'date' => 'date'
    ];
}
