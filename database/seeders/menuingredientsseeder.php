<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class menuingredientsseeder extends Seeder
{

    public function run(): void
    {
        // Samgyupsal
        DB::table('menu_ingredients')->insert([
            ['menu_id' => 1, 'ingredient_id' => 1, 'quantity' => 100.00], // Beef
            ['menu_id' => 1, 'ingredient_id' => 2, 'quantity' => 100.00], // Pork
            ['menu_id' => 1, 'ingredient_id' => 3, 'quantity' => 100.00], // Chicken
            ['menu_id' => 1, 'ingredient_id' => 4, 'quantity' => 100.00], // Shrimp
            ['menu_id' => 1, 'ingredient_id' => 5, 'quantity' => 50.00],  // Lettuce
            ['menu_id' => 1, 'ingredient_id' => 6, 'quantity' => 50.00],  // Mushroom
        ]);

        // Hotpot
        DB::table('menu_ingredients')->insert([
            ['menu_id' => 2, 'ingredient_id' => 1, 'quantity' => 100.00], // Beef
            ['menu_id' => 2, 'ingredient_id' => 2, 'quantity' => 100.00], // Pork
            ['menu_id' => 2, 'ingredient_id' => 10, 'quantity' => 50.00], // Hotpot Balls
            ['menu_id' => 2, 'ingredient_id' => 11, 'quantity' => 80.00], // Noodles
            ['menu_id' => 2, 'ingredient_id' => 6, 'quantity' => 50.00],  // Mushroom
        ]);

        // Fusion
        DB::table('menu_ingredients')->insert([
            ['menu_id' => 3, 'ingredient_id' => 1, 'quantity' => 100.00], // Beef
            ['menu_id' => 3, 'ingredient_id' => 2, 'quantity' => 100.00], // Pork
            ['menu_id' => 3, 'ingredient_id' => 3, 'quantity' => 100.00], // Chicken
            ['menu_id' => 3, 'ingredient_id' => 4, 'quantity' => 100.00], // Shrimp
            ['menu_id' => 3, 'ingredient_id' => 5, 'quantity' => 50.00],  // Lettuce
            ['menu_id' => 3, 'ingredient_id' => 6, 'quantity' => 100.00], // Mushroom
            ['menu_id' => 3, 'ingredient_id' => 10, 'quantity' => 50.00], // Hotpot Balls
            ['menu_id' => 3, 'ingredient_id' => 11, 'quantity' => 100.00], // Noodles
        ]);
    }
}
