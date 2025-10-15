<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RefillConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('refill_configurations')->insert([
            ['ingredient_id' => 1, 'quantity_per_plate' => 100.00],
            ['ingredient_id' => 2, 'quantity_per_plate' => 100.00],
            ['ingredient_id' => 3, 'quantity_per_plate' => 100.00],
            ['ingredient_id' => 4, 'quantity_per_plate' => 100.00],
            ['ingredient_id' => 5, 'quantity_per_plate' => 50.00],
            ['ingredient_id' => 6, 'quantity_per_plate' => 100.00],
            ['ingredient_id' => 10, 'quantity_per_plate' => 50.00],
            ['ingredient_id' => 11, 'quantity_per_plate' => 100.00],
        ]);
    }
}
