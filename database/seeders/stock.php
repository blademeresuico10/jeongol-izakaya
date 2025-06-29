<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class stock extends Seeder
{
   
    public function run(): void {
        DB::table('stock')->insert([
            ['stock_name' => 'Beef', 'stock_quantity' => 100.5],
            ['stock_name' => 'Pork', 'stock_quantity' => 100.5],
            ['stock_name' => 'Chicken', 'stock_quantity' => 100.5],
            ['stock_name' => 'Shrimp', 'stock_quantity' => 100.5],
            ['stock_name' => 'Vegetables', 'stock_quantity' => 100.5],
            ['stock_name' => 'Fish', 'stock_quantity' => 100.5],
        ]);
    }
    }
