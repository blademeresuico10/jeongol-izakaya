<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class menuingredientsseeder extends Seeder
{
    public function run(): void
    {
        DB::table('menu_ingredients')->insert([
            ['menu_id' => 1, 'ingredient_id' => 1, 'quantity' => 100.00], 
            ['menu_id' => 1, 'ingredient_id' => 2, 'quantity' => 100.00], 
            ['menu_id' => 1, 'ingredient_id' => 3, 'quantity' => 100.00], 
            ['menu_id' => 1, 'ingredient_id' => 4, 'quantity' => 100.00], 
            ['menu_id' => 1, 'ingredient_id' => 5, 'quantity' => 50.00],  
            ['menu_id' => 1, 'ingredient_id' => 6, 'quantity' => 50.00],  
        ]);

        DB::table('menu_ingredients')->insert([
            ['menu_id' => 2, 'ingredient_id' => 1, 'quantity' => 100.00], 
            ['menu_id' => 2, 'ingredient_id' => 2, 'quantity' => 100.00],
            ['menu_id' => 2, 'ingredient_id' => 10, 'quantity' => 50.00], 
            ['menu_id' => 2, 'ingredient_id' => 11, 'quantity' => 80.00], 
            ['menu_id' => 2, 'ingredient_id' => 6, 'quantity' => 50.00],  
        ]);

        DB::table('menu_ingredients')->insert([
            ['menu_id' => 3, 'ingredient_id' => 1, 'quantity' => 100.00], 
            ['menu_id' => 3, 'ingredient_id' => 2, 'quantity' => 100.00], 
            ['menu_id' => 3, 'ingredient_id' => 3, 'quantity' => 100.00], 
            ['menu_id' => 3, 'ingredient_id' => 4, 'quantity' => 100.00], 
            ['menu_id' => 3, 'ingredient_id' => 5, 'quantity' => 50.00],  
            ['menu_id' => 3, 'ingredient_id' => 6, 'quantity' => 100.00], 
            ['menu_id' => 3, 'ingredient_id' => 10, 'quantity' => 50.00], 
            ['menu_id' => 3, 'ingredient_id' => 11, 'quantity' => 100.00], 
        ]);

      

         DB::table('menu_ingredients')->insert([
            ['menu_id' => 4, 'ingredient_id' => 17, 'quantity' => 500.00], 
        ]);

         DB::table('menu_ingredients')->insert([
            ['menu_id' => 5, 'ingredient_id' => 18, 'quantity' => 1.00], 
         ]);

        DB::table('menu_ingredients')->insert([
            ['menu_id' => 6, 'ingredient_id' => 12, 'quantity' => 1.00], 
        ]);
    }
}