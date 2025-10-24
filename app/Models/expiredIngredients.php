<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class expiredIngredients extends Model
{
    protected $fillable = [
        'ingredient_id',  
        'quantity',
        'expired_at',
        'ingredient_batch_id'

    ];

    protected $casts = [
        'expired_at' => 'date',
        'quantity' => 'decimal:2'
    ];

    public function ingredient()
    {
        return $this->belongsTo(ingredients::class);
    }

    public function ingredientBatch(){
        return $this->belongsTo(ingredientBatch::class);
    }

    public function getIngredientAttribute()
    {
        return $this->ingredientBatch?->ingredient;
    }
}
