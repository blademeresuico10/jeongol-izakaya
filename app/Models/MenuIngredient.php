<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuIngredient extends Model
{
    protected $table = 'menu_ingredients';

    protected $fillable = [
        'menu_id',
        'ingredient_id',
        'quantity',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }

    public function ingredient()
    {
        return $this->belongsTo(ingredients::class, 'ingredient_id');
    }
}
