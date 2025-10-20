<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class menuingredientsseeder extends Seeder
{
    public function run(): void
    {
        // SAMGYUPSAL
        DB::table('menu_ingredients')->insert([
            ['menu_id' => 1, 'ingredient_id' => 1, 'quantity' => 0.1], 
            ['menu_id' => 1, 'ingredient_id' => 2, 'quantity' => 0.1], 
            ['menu_id' => 1, 'ingredient_id' => 3, 'quantity' => 0.1], 
            ['menu_id' => 1, 'ingredient_id' => 4, 'quantity' => 0.1], 
            ['menu_id' => 1, 'ingredient_id' => 5, 'quantity' => 0.05], 
            ['menu_id' => 1, 'ingredient_id' => 6, 'quantity' => 0.05], 
        ]);

        //  HOTPOT
        DB::table('menu_ingredients')->insert([
            ['menu_id' => 2, 'ingredient_id' => 1, 'quantity' => 0.1], 
            ['menu_id' => 2, 'ingredient_id' => 2, 'quantity' => 0.1], 
            ['menu_id' => 2, 'ingredient_id' => 10, 'quantity' => 0.05], 
            ['menu_id' => 2, 'ingredient_id' => 11, 'quantity' => 0.08], 
            ['menu_id' => 2, 'ingredient_id' => 6, 'quantity' => 0.05], 
        ]);

        //  FUSION
        DB::table('menu_ingredients')->insert([
            ['menu_id' => 3, 'ingredient_id' => 1, 'quantity' => 0.1],
            ['menu_id' => 3, 'ingredient_id' => 2, 'quantity' => 0.1],
            ['menu_id' => 3, 'ingredient_id' => 3, 'quantity' => 0.1],
            ['menu_id' => 3, 'ingredient_id' => 4, 'quantity' => 0.1],
            ['menu_id' => 3, 'ingredient_id' => 5, 'quantity' => 0.05],
            ['menu_id' => 3, 'ingredient_id' => 6, 'quantity' => 0.1],
            ['menu_id' => 3, 'ingredient_id' => 10, 'quantity' => 0.05],
            ['menu_id' => 3, 'ingredient_id' => 11, 'quantity' => 0.1],
        ]);

        //  WAGYU (à la carte)
        DB::table('menu_ingredients')->insert([
            ['menu_id' => 4, 'ingredient_id' => 13, 'quantity' => 0.5], 
        ]);

        // COCO ISLAND (drink)
        DB::table('menu_ingredients')->insert([
            ['menu_id' => 5, 'ingredient_id' => 14, 'quantity' => 1], 
        ]);

        // COKE (drink)
        DB::table('menu_ingredients')->insert([
            ['menu_id' => 6, 'ingredient_id' => 12, 'quantity' => 1], 
        ]);
    }
}
