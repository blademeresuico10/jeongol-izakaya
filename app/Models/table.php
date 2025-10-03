<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class table extends Model
{
    protected $fillable = ['table_number', 'capacity'];

    use SoftDeletes;
    public function walkins()
    {
        return $this->hasMany(walkins::class, 'table_id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'table_id');
    }
}
