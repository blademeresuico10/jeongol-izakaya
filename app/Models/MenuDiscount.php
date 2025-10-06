<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuDiscount extends Model
{
    protected $fillable = [
        'menu_id',
        'discount_percentage',
        'discount_type'
    ];

    public function menu()
    {
        return $this->belongsTo(menu::class);
    }
}