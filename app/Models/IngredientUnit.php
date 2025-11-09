<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IngredientUnit extends Model
{
    protected $fillable = ['name', 'abbreviation'];

    public function ingredients()
    {
        return $this->hasMany(ingredients::class, 'unit_id');
    }
}
