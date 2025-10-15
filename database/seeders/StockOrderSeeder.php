<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StockOrderSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $stockOrders = [
            ['ingredient_id' => 1, 'requested_quantity' => 30.00, 'status' => 'pending'],
            ['ingredient_id' => 5, 'requested_quantity' => 15.00, 'status' => 'pending'],
            ['ingredient_id' => 10, 'requested_quantity' => 25.00, 'status' => 'pending'],
            ['ingredient_id' => 13, 'requested_quantity' => 40.00, 'status' => 'pending'],
            ['ingredient_id' => 17, 'requested_quantity' => 20.00, 'status' => 'pending'],
        ];

        $stockOrders = array_map(function ($item) use ($now) {
            return array_merge($item, [
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }, $stockOrders);

        DB::table('stock_orders')->insert($stockOrders);
    }
}
