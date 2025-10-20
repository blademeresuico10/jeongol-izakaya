<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StockLevelAlertSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $alerts = [
            // MEAT
            ['ingredient_id' => 1, 'low_stock' => 15, 'critical_stock' => 10], // Beef
            ['ingredient_id' => 2, 'low_stock' => 15, 'critical_stock' => 10], // Pork
            ['ingredient_id' => 3, 'low_stock' => 15, 'critical_stock' => 10], // Chicken
            ['ingredient_id' => 4, 'low_stock' => 15, 'critical_stock' => 10], // Shrimp
            ['ingredient_id' => 13, 'low_stock' => 15, 'critical_stock' => 10], // Wagyu

            // VEGETABLES
            ['ingredient_id' => 5, 'low_stock' => 12, 'critical_stock' => 8], // Lettuce
            ['ingredient_id' => 6, 'low_stock' => 12, 'critical_stock' => 8], // Mushroom
            ['ingredient_id' => 7, 'low_stock' => 12, 'critical_stock' => 8], // Sweet Carrots
            ['ingredient_id' => 8, 'low_stock' => 12, 'critical_stock' => 8], // Potatoes
            ['ingredient_id' => 9, 'low_stock' => 12, 'critical_stock' => 8], // Cabbage

            // SOUPBASE
            ['ingredient_id' => 10, 'low_stock' => 10, 'critical_stock' => 6], // Hotpot Balls
            ['ingredient_id' => 11, 'low_stock' => 10, 'critical_stock' => 6], // Noodles

            // BEVERAGES
            ['ingredient_id' => 12, 'low_stock' => 30, 'critical_stock' => 20], // Coke
            ['ingredient_id' => 14, 'low_stock' => 30, 'critical_stock' => 20], // Coco Island
        ];

        $alerts = array_map(function ($alert) use ($now) {
            return array_merge($alert, [
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }, $alerts);

        DB::table('stock_level_alerts')->insert($alerts);
    }
}
