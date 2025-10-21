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

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
    ];

    public function reservation()
    {
        return $this->belongsTo(reservation::class, 'reservation_id');
    }

    public function walkin()
    {
        return $this->belongsTo(walkin::class, 'walk_in_id', 'id');
    }

    public function menu()
    {
        return $this->belongsTo(menu::class, 'menu_id');
    }

    public function refills()
    {
        return $this->hasMany(OrderRefill::class, 'order_id');
    }

    public function getLinkedTableAttribute()
    {
        return $this->reservation->table ?? $this->walkin->table ?? null;
    }

    public function scopePending($query) { return $query->where('status', 'Pending'); }
    public function scopeReady($query)   { return $query->where('status', 'Ready'); }
    public function scopeServed($query)  { return $query->where('status', 'Served'); }
    public function scopeCancelled($query) { return $query->where('status', 'Cancelled'); }

    public function isPending() { return $this->status === 'Pending'; }
    public function isReady()   { return $this->status === 'Ready'; }
    public function isServed()  { return $this->status === 'Served'; }
    public function isCancelled() { return $this->status === 'Cancelled'; }

    public function markAsReady() { $this->update(['status' => 'Ready']); }
    public function markAsServed() { $this->update(['status' => 'Served']); }
    public function markAsCancelled() { $this->update(['status' => 'Cancelled']); }
}
