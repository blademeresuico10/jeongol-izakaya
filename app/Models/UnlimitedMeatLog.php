<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnlimitedMeatLog extends Model
{
    protected $fillable = [
        'table_id',
        'ingredient_id',
        'quantity',
        'unit',
        'reservation_id',
        'walk_in_id',
    ];

    public function table()
    {
        return $this->belongsTo(table::class, 'table_id');
    }

    public function ingredient()
    {
        return $this->belongsTo(ingredients::class, 'ingredient_id');
    }

    public function reservation()
    {
        return $this->belongsTo(reservation::class, 'reservation_id');
    }

    public function walkin()
    {
        return $this->belongsTo(walkin::class, 'walk_in_id');
    }
}