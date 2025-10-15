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
            ['ingredient_id' => 1, 'low_stock' => 15, 'critical_stock' => 10],
            ['ingredient_id' => 2, 'low_stock' => 15, 'critical_stock' => 10],
            ['ingredient_id' => 3, 'low_stock' => 15, 'critical_stock' => 10],
            ['ingredient_id' => 4, 'low_stock' => 15, 'critical_stock' => 10000],
            ['ingredient_id' => 17, 'low_stock' => 15, 'critical_stock' => 10],

            // VEGETABLES
            ['ingredient_id' => 5, 'low_stock' => 12, 'critical_stock' => 8],
            ['ingredient_id' => 6, 'low_stock' => 12, 'critical_stock' => 8],
            ['ingredient_id' => 7, 'low_stock' => 12, 'critical_stock' => 8],
            ['ingredient_id' => 8, 'low_stock' => 12, 'critical_stock' => 8],
            ['ingredient_id' => 9, 'low_stock' => 12, 'critical_stock' => 8],

            // SOUPBASE
            ['ingredient_id' => 10, 'low_stock' => 10, 'critical_stock' => 15],
            ['ingredient_id' => 11, 'low_stock' => 10, 'critical_stock' => 15],

            // BEVERAGES
            ['ingredient_id' => 12, 'low_stock' => 12, 'critical_stock' => 16],
            ['ingredient_id' => 13, 'low_stock' => 30, 'critical_stock' => 10],
            ['ingredient_id' => 14, 'low_stock' => 30, 'critical_stock' => 10],
            ['ingredient_id' => 15, 'low_stock' => 30, 'critical_stock' => 10],
            ['ingredient_id' => 16, 'low_stock' => 30, 'critical_stock' => 10],
            ['ingredient_id' => 18, 'low_stock' => 30, 'critical_stock' => 10],
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
