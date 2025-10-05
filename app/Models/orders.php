<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class orders extends Model
{
    protected $fillable = [
        'reservation_id',
        'walk_in_id',
        'menu_id',
        'quantity',
        'price',
        'notes',
        'status',
    ];

    public function table()
    {
        return $this->belongsTo(table::class, 'table_id');
    }

    public function menu()
    {
        return $this->belongsTo(menu::class, 'menu_id');
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'reservation_id');
    }

    public function walkin()
    {
        return $this->belongsTo(walkin::class, 'walk_in_id');
    }
}
