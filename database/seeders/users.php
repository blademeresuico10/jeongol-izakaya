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
                'firstname' => 'Admin',
                'lastname' => 'User',
                'role' => 'admin',
                'contact_number' => '09171234567',
                'username' => 'admin',
                'password' => Hash::make('q12345'),
                'status' => 'Active',
            ],
            [
                'firstname' => 'Receptionist',
                'lastname' => 'User',
                'role' => 'receptionist',
                'contact_number' => '09172223333',
                'username' => 'receptionist',
                'password' => Hash::make('q12345'),
                'status' => 'Active',
            ],
            [
                'firstname' => 'Cashier',
                'lastname' => 'User',
                'role' => 'cashier',
                'contact_number' => '09173334444',
                'username' => 'cashier',
                'password' => Hash::make('q12345'),
                'status' => 'Active',
            ],
            [
                'firstname' => 'Kitchen',
                'lastname' => 'User',
                'role' => 'kitchen-staff',
                'contact_number' => '09174445555',
                'username' => 'kitchen',
                'password' => Hash::make('q12345'),
                'status' => 'Active',
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(
                ['username' => $user['username']], // Check by username
                array_merge($user, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
