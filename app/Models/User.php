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
        'last_login_at',
        'session_token',
    ];

    public function getRoleAttribute($value)
    {
        Log::info('Getting role attribute', ['raw_value' => $value]);
        return $value;
    }

    protected $hidden = [
        'password',
        'remember_token',
        'session_token', // Hide session token from JSON responses
    ];

    protected $dates = ['deleted_at'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_logged_in' => 'boolean',
        'password' => 'hashed',
    ];

    public function transactions()
    {
        return $this->hasMany(transaction::class, 'cashier_id');
    }

    // Session management methods for "one user per role" functionality

    /**
     * Check if another user with same role is logged in
     */
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

    /**
     * Get the currently active user for a specific role
     */
    public static function getActiveUserByRole($role)
    {
        return self::where('role', $role)
            ->where('is_logged_in', true)
            ->where('status', 'Active')
            ->first();
    }

    /**
     * Force logout other users with same role
     */
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

    /**
     * Mark user as logged in with session token
     */
    public function markAsLoggedIn(string $sessionToken): bool
    {
        return $this->update([
            'is_logged_in' => true,
            'last_login_at' => now(),
            'session_token' => $sessionToken
        ]);
    }

    /**
     * Mark user as logged out
     */
    public function markAsLoggedOut(): bool
    {
        return $this->update([
            'is_logged_in' => false,
            'session_token' => null
        ]);
    }

    /**
     * Check if user's session is valid
     */
    public function isSessionValid(string $sessionToken): bool
    {
        return $this->is_logged_in &&
            $this->session_token === $sessionToken &&
            $this->status === 'Active';
    }

    /**
     * Get full name attribute
     */
    public function getFullNameAttribute()
    {
        return "{$this->firstname} {$this->lastname}";
    }

    /**
     * Scope to get only active users
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    /**
     * Scope to get users by role
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope to get logged in users
     */
    public function scopeLoggedIn($query)
    {
        return $query->where('is_logged_in', true);
    }
}
