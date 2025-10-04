<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class walkin extends Model
{
    protected $table = 'walk_ins';
    protected $fillable = [
        'customer_id',
        'table_id',
        'user_id',
        'pax',
        'started_at',
        'ended_at',
        'status',
    ];


    public function customer()
    {
        return $this->belongsTo(customers::class, 'customer_id');
    }

    public function table()
    {
        return $this->belongsTo(table::class, 'table_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function orders()
    {
        return $this->hasMany(orders::class, 'walk_in_id', 'id');
    }

    public function transactions()
    {
        return $this->hasMany(transaction::class, 'walk_in_id', 'id');
    }
}
