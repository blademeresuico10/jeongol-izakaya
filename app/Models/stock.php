<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;


class stock extends Model
{
    use HasFactory;

    use SoftDeletes;


    protected $table = 'stock';

    protected $fillable = [
        'stock_name',
        'stock_quantity'
    ];
}
