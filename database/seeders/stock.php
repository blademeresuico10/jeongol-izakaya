<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class stock extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('stock')->insert([
            ['stock_name' => 'Beef', 'stock_quantity' => 100.5],
            ['stock_name' => 'Pork', 'stock_quantity' => 100.5],
            ['stock_name' => 'Chiken', 'stock_quantity' => 100.5],
            ['stock_name' => 'Shirmp', 'stock_quantity' => 100.5],
            ['stock_name' => 'Vegetables', 'stock_quantity' => 100.5],
        ]);
        
        
    }
}
