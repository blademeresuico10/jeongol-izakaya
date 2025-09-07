<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class table extends Model
{
    protected $fillable = ['table_number', 'capacity'];

    use SoftDeletes;

}


