<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ingredientMovements extends Model
{

    protected $table = 'ingredient_movements';
    protected $fillable = [
        'ingredient_id',
        'ingredient_batch_id',  
        'user_id',
        'order_id',  
        'type',
        'quantity',
        'stock_before',
        'stock_after',
        'notes',
    ];

    public function ingredient()
    {
        return $this->belongsTo(ingredients::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault(); 
    }

    public function order()  
    {
        return $this->belongsTo(orders::class);
    }

    public function ingredientBatch()
    {
        return $this->belongsTo(ingredientBatch::class);
    }
}