<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class customers extends Model
{
    protected $fillable = [
        'name',
        'contact_number',
        'id_type',
    ];

    public function orders()
    {
        return $this->hasMany(OrderDetail::class);
    }
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
