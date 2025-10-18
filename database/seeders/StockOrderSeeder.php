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
            ['ingredient_id' => 1, 'reorder_point' => 30.00, 'reorder_quantity' =>20, 'status' => 'pending'],
            ['ingredient_id' => 5, 'reorder_point' => 15.00, 'reorder_quantity' =>20, 'status' => 'pending'],
            ['ingredient_id' => 10, 'reorder_point' => 25.00, 'reorder_quantity' =>20, 'status' => 'pending'],
            ['ingredient_id' => 13, 'reorder_point' => 40.00, 'reorder_quantity' =>20,'status' => 'pending'],
            ['ingredient_id' => 17, 'reorder_point' => 20.00, 'reorder_quantity' =>20,'status' => 'pending'],
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
