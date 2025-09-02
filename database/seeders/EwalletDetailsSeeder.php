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
                'wallet_name' => 'GreyC M.',
                'wallet_number' => '09123456789',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'payment_method' => 'maya',
                'wallet_name' => 'GreyC M.',
                'wallet_number' => '09987654321',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}