<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_no',
        'orders_total',      
        'discount_total',
        'advance_payment',
        'grand_total',       
        'to_pay',           
        'cash_received',
        'change',
        'payment_method',
        'status',
        'reservation_id',
        'walk_in_id',
        'customer_id',
        'cashier_id',
    ];

    protected $casts = [
        'orders_total' => 'decimal:2',     
        'discount_total' => 'decimal:2',
        'advance_payment' => 'decimal:2',
        'grand_total' => 'decimal:2',      
        'to_pay' => 'decimal:2',          
        'cash_received' => 'decimal:2',
        'change' => 'decimal:2',
    ];

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function customer()
    {
        return $this->belongsTo(customers::class);
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function orders()
    {
        return $this->hasMany(orders::class);
    }

    public function walkin()
    {
        return $this->belongsTo(walkin::class);
    }
}