<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;  
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $dates = ['deleted_at'];  

    public function transactions()
    {
        return $this->hasMany(transaction::class, 'cashier_id');
    }

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', 
    ];
}