<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EwalletDetail extends Model
{
    protected $table = 'ewallet_details';
    protected $fillable = [
        'payment_method',
        'wallet_name',
        'wallet_number',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function getActivePaymentMethod($method)
    {
        return self::where('payment_method', $method)
            ->where('is_active', true)
            ->first();
    }

    public static function getAllActivePaymentMethods()
    {
        return self::where('is_active', true)
            ->get()
            ->keyBy('payment_method');
    }
}
