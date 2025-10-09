<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class expiredIngredients extends Model
{
    protected $fillable = [
        'quantity',
        'expired_at',
        'ingredient_batch_id'
    ];

    public function ingredient()
    {
        return $this->belongsTo(ingredients::class);
    }

    public function ingredientBatch(){
        return $this->belonsTo(ingredientBatch::class);
    }
}
