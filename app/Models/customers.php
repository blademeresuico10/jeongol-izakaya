<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class customers extends Model
{
    protected $fillable = [
        'name',
        'contact_number',
        'id_type',
    ];

    
}
