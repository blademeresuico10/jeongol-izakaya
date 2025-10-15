<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOrder extends Model
{
    use HasFactory;


    protected $fillable = [
        'ingredient_id',
        'requested_quantity',
        'status',
    ];

    public function ingredient()
    {
        return $this->belongsTo(ingredients::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
