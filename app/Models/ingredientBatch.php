<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ingredientBatch extends Model
{
    protected $fillable = ['ingredient_id','arrived_at', 'expiration_date', 'quantity'];

    public function ingredient()
    {
        return $this->belongsTo(ingredients::class);
    }
}
