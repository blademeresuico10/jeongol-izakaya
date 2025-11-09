<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    public function run(): void
    {
        $this->call([
            MenuCategorySeeder::class,
            menuseeder::class,
            tableseeder::class,
            users::class,
            EwalletDetailsSeeder::class,
            MenuDiscountSeeder::class,
            OperatingHoursSeeder::class,
            IngredientCategorySeeder::class,    
            IngredientUnitSeeder::class,
            ingredientsSeeder::class,
            menuingredientsseeder::class,
            RefillConfigurationSeeder::class,
        ]);
    }
}
