<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class customers extends Model
{
    protected $fillable = [
        'name',
        'contact_number',
        'id_type',
        'id_number',
        'customer_type',
    ];

    public function orders()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
