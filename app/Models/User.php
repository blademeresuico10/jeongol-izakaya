<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    
    protected $fillable = [
        'firstname', 'lastname', 'role', 'contact_number', 
        'username', 'password', 'status',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
}

    

