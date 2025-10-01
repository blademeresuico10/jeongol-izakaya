<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ingredients;
use App\Models\User;
use App\Models\OrderDetail;


class ingredientMovements extends Model
{
    protected $fillable = [
        'ingredient_id',
        'user_id',
        'order_details_id',
        'type',
        'quantity',
        'stock_before',
        'stock_after',
        'notes'
    ];

    public function ingredient()
    {
        return $this->belongsTo(ingredients::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault(); 
    }

    public function orderDetail()
    {
        return $this->belongsTo(OrderDetail::class)->withDefault();
    }
}
