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
            ['ingredient_id' => 1,  'quantity_per_plate' => 100.00], // Beef
            ['ingredient_id' => 2,  'quantity_per_plate' => 100.00], // Pork
            ['ingredient_id' => 3,  'quantity_per_plate' => 100.00], // Chicken
            ['ingredient_id' => 4,  'quantity_per_plate' => 100.00], // Shrimp
            ['ingredient_id' => 5,  'quantity_per_plate' => 50.00],  // Lettuce
            ['ingredient_id' => 6,  'quantity_per_plate' => 50.00],  // Mushroom
            ['ingredient_id' => 7,  'quantity_per_plate' => 50.00],  // Sweet Carrots
            ['ingredient_id' => 8,  'quantity_per_plate' => 50.00],  // Potatoes
            ['ingredient_id' => 9,  'quantity_per_plate' => 50.00],  // Cabbage
            ['ingredient_id' => 10, 'quantity_per_plate' => 50.00],  // Hotpot Balls
            ['ingredient_id' => 11, 'quantity_per_plate' => 50.00],  // Noodles
            ['ingredient_id' => 12, 'quantity_per_plate' => 1.00],   // Coke
            ['ingredient_id' => 13, 'quantity_per_plate' => 200.00], // Wagyu
            ['ingredient_id' => 14, 'quantity_per_plate' => 1.00],   // Coco Island
        ]);
    }
}
