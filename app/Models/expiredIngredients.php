<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class expiredIngredients extends Model
{
    protected $fillable = [
        'ingredient_id',
        'ingredient_name',
        'category',
        'quantity',
        'unit',
        'expiration_date',
        'expired_at',
        'notes'
    ];

    public function ingredient()
    {
        return $this->belongsTo(ingredients::class);
    }
}
