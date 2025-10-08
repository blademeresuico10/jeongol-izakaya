<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockAlertLevel extends Model
{
    protected $table = 'stock_level_alerts';

    protected $fillable = [
        'ingredient_id',
        'low_stock',
        'critical_stock',
    ];

    public function ingredient()
    {
        return $this->belongsTo(ingredients::class, 'ingredient_id');
    }
}
