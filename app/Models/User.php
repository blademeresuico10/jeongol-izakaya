<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;

/**
 * @method bool markAsLoggedIn(string $sessionToken)
 * @method bool markAsLoggedOut()
 * @method bool isSessionValid(string $sessionToken)
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'firstname',
        'lastname',
        'role',
        'contact_number',
        'username',
        'email',
        'password',
        'status',
        'profile_picture',
        'is_logged_in',
        'session_token',
    ];

    public function getRoleAttribute($value)
    {
        return $value;
    }

    protected $hidden = [
        'password',
        'remember_token',
        'session_token',
    ];

    protected $dates = ['deleted_at'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_logged_in' => 'boolean',
        'password' => 'hashed',
    ];

    public function transactions()
    {
        return $this->hasMany(transaction::class, 'cashier_id');
    }

    public static function isRoleActive($role, $excludeUserId = null)
    {
        return self::where('role', $role)
            ->where('is_logged_in', true)
            ->where('status', 'Active')
            ->when($excludeUserId, function ($query, $excludeUserId) {
                return $query->where('id', '!=', $excludeUserId);
            })
            ->exists();
    }

    public static function getActiveUserByRole($role)
    {
        return self::where('role', $role)
            ->where('is_logged_in', true)
            ->where('status', 'Active')
            ->first();
    }

    public static function logoutRole($role, $excludeUserId = null)
    {
        return self::where('role', $role)
            ->where('is_logged_in', true)
            ->when($excludeUserId, function ($query, $excludeUserId) {
                return $query->where('id', '!=', $excludeUserId);
            })
            ->update([
                'is_logged_in' => false,
                'session_token' => null
            ]);
    }

  
    public function markAsLoggedIn(string $sessionToken): bool
    {
        return $this->update([
            'is_logged_in' => true,
            'session_token' => $sessionToken
        ]);
    }

   
    public function markAsLoggedOut(): bool
    {
        return $this->update([
            'is_logged_in' => false,
            'session_token' => null
        ]);
    }


    public function isSessionValid(string $sessionToken): bool
    {
        return $this->is_logged_in &&
            $this->session_token === $sessionToken &&
            $this->status === 'Active';
    }

   
    public function getFullNameAttribute()
    {
        return "{$this->firstname} {$this->lastname}";
    }

 
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

  
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    
    public function scopeLoggedIn($query)
    {
        return $query->where('is_logged_in', true);
    }
}
