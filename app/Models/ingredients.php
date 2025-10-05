<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function menuIngredients()
    {
        return $this->hasMany(MenuIngredient::class, 'ingredient_id');
    }

    public function menus()
    {
        return $this->belongsToMany(menu::class, 'menu_ingredients', 'ingredient_id', 'menu_id')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function convertGramsToKg($grams)
    {
        return $grams / 1000;
    }

    public function deductStock($grams, $orderId, $userId, $notes = null)
    {
        $stockBefore = $this->stocks;
        $kgToDeduct = $this->convertGramsToKg($grams);
        
        $this->stocks -= $kgToDeduct;
        $this->save();

        ingredientMovements::create([
            'ingredient_id' => $this->id,
            'user_id' => $userId,
            'order_id' => $orderId,
            'type' => 'used',
            'quantity' => $kgToDeduct,
            'stock_before' => $stockBefore,
            'stock_after' => $this->stocks,
            'notes' => $notes ?? "Used for order #{$orderId}"
        ]);

        return $this;
    }
}