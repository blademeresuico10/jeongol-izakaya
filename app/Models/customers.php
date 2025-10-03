<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class customers extends Model
{
    protected $fillable = [
        'name',
        'contact_number',
        'email',
        'id_type',
    ];

    public function orders()
    {
        return $this->hasMany(orders::class);
    }

    public function reservation()
    {
        return $this->hasMany(reservation::class);
    }

    public function feedbacks()
    {
        return $this->hasMany(feedback::class);
    }

    public function transaction()
    {
        return $this->hasMany(transaction::class);
    }
    
    
}
