<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class users extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'firstname' => 'Keith',
                'lastname' => 'Canon',
                'role' => 'Admin',
                'contact_number' => '09171234567',
                'username' => 'keith',
                'email' => 'emsoyalarcon@gmail.com',
                'password' => Hash::make('q12345'),
                'status' => 'Active',
            ],
            [
                'firstname' => 'Cyrus',
                'lastname' => 'Alarcon',
                'role' => 'Receptionist',
                'contact_number' => '09172223333',
                'email' => 'cyrusalarcon@gmail.com',
                'username' => 'cyrus',
                'password' => Hash::make('q12345'),
                'status' => 'Active',
            ],
            [
                'firstname' => 'Kristal',
                'lastname' => 'Fado',
                'role' => 'Cashier',
                'contact_number' => '09173334444',
                'username' => 'kristal',
                'email' => 'kristalfado@gmail.com',
                'password' => Hash::make('q12345'),
                'status' => 'Active',
            ],
            [
                'firstname' => 'Blademere',
                'lastname' => 'Suico',
                'role' => 'Kitchen Staff',
                'contact_number' => '09174445555',
                'username' => 'blademere',
                'email' => 'blademeresuico@gmail.com',
                'password' => Hash::make('q12345'),
                'status' => 'Active',
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(
                ['username' => $user['username']], 
                array_merge($user, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
