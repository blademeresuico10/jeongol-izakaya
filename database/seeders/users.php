<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class users extends Seeder
{
    
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'firstname' => 'Admin',
                'lastname' => 'User',
                'role' => 'admin',
                'contact_number' => '09171234567',
                'username' => 'admin',
                'password' => Hash::make('q12345'), 
                'status' => 'Active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'firstname' => 'Receptionist',
                'lastname' => 'User',
                'role' => 'receptionist',
                'contact_number' => '09172223333',
                'username' => 'receptionist',
                'password' => Hash::make('q12345'),
                'status' => 'Active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'firstname' => 'Cashier',
                'lastname' => 'User',
                'role' => 'cashier',
                'contact_number' => '09173334444',
                'username' => 'cashier',
                'password' => Hash::make('q12345'),
                'status' => 'Active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'firstname' => 'Kitchen',
                'lastname' => 'User',
                'role' => 'kitchen',
                'contact_number' => '09174445555',
                'username' => 'kitchen',
                'password' => Hash::make('q12345'),
                'status' => 'Active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
