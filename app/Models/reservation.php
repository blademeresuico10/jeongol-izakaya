<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'pax',
        'started_at',
        'ended_at',
        'table_id',
        'customer_id',
        'user_id',
        'status',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function orders()
    {
        return $this->hasMany(orders::class, 'reservation_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(customers::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->hasOne(reservationPayment::class, 'reservation_id', 'id');
    }
    
    public function table()
    {
        return $this->belongsTo(table::class);
    }
    
    public function transactions()
    {
        return $this->hasMany(transaction::class);
    }

    public function scopeCurrentlyActive($query)
    {
        $now = now();
        return $query->where('status', 'Active')
                    ->where('started_at', '<=', $now)
                    ->where('ended_at', '>', $now)
                    ->whereDoesntHave('transactions');
    }

}