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

    public function reservation()
    {
        return $this->belongsTo(reservation::class, 'reservation_id');
    }

    public function walkin()
    {
        return $this->belongsTo(walkin::class, 'walk_in_id');
    }

    public function table()
    {
        return $this->belongsTo(table::class, 'table_id');
    }

    public function menu()
    {
        return $this->belongsTo(menu::class, 'menu_id');
    }

    public function getLinkedTableAttribute()
    {
        if ($this->reservation) {
            return $this->reservation->table;
        }

        if ($this->walkin) {
            return $this->walkin->table;
        }

        return null;
    }
}
