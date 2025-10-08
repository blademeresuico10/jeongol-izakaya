<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    
    public function run(): void
    {
        $this->call([
            menuseeder::class,
            tableseeder::class,
            users::class,
            ingredientsSeeder::class,
            EwalletDetailsSeeder::class,
            menuingredientsseeder::class,
            MenuDiscountSeeder::class,
            OperatingHoursSeeder::class,
            StockLevelAlertSeeder::class
        ]);
    }
}
