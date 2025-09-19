<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EwalletDetailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('ewallet_details')->insert([
            [
                'payment_method' => 'gcash',
                'wallet_name' => 'BL******E S.',
                'wallet_number' => '09388899134',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'payment_method' => 'maya',
                'wallet_name' => 'Blademere Suico',
                'wallet_number' => '09388899134',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}