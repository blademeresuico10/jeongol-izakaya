<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefillConfiguration extends Model
{
    
    public $timestamps = false;

   
    protected $fillable = [
        'ingredient_id',
        'quantity_per_plate',
        'unit',
    ];

    
    protected $casts = [
        'quantity_per_plate' => 'decimal:2',
    ];

    public function ingredient()
    {
        return $this->belongsTo(ingredients::class);
    }

    public function orderRefills()
    {
        return $this->hasMany(OrderRefill::class, 'ingredient_id', 'ingredient_id');
    }
}