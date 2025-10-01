<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\IngredientBatch;
use App\Models\ingredientMovements;
use App\Models\expiredIngredients;


class ingredients extends Model
{
    protected $fillable = ['name', 'category', 'unit', 'stocks'];

    public function batches()
    {
        return $this->hasMany(ingredientBatch::class);
    }

    public function movements()
    {
        return $this->hasMany(ingredientMovements::class);
    }

    public function expiredRecords()
    {
        return $this->hasMany(expiredIngredients::class);
    }
}
