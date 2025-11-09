<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IngredientCategory extends Model
{
    protected $fillable = ['name', 'slug'];

    public function ingredients()
    {
        return $this->hasMany(ingredients::class, 'category_id');
    }
}
