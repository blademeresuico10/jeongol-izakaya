<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class stock extends Model
{
    use HasFactory;

    use HasFactory;

    protected $table = 'stock';

    protected $fillable = [
        'stock_name',
        'stock_quantity'
    ];
}
