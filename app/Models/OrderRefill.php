<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderRefill extends Model
{
    protected $fillable = [
        'order_id',
        'ingredient_id',
        'quantity',
        'status'
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function order()
    {
        return $this->belongsTo(orders::class);
    }

    public function ingredient()
    {
        return $this->belongsTo(ingredients::class);
    }

    public function refillConfiguration()
    {
        return $this->hasOne(RefillConfiguration::class, 'ingredient_id', 'ingredient_id');
    }

    // Scopes for easier querying
    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    public function scopeReady($query)
    {
        return $query->where('status', 'Ready');
    }

    public function scopeServed($query)
    {
        return $query->where('status', 'Served');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'Cancelled');
    }
}