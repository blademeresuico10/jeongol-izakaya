<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class menuseeder extends Seeder
{
    
    public function run(): void
    {
        DB::table('menu')->insert([
            ['menu_item' => 'Samgyup Lunch', 'price' => 548.00],
            ['menu_item' => 'Samgyup Dinner', 'price' => 598.00],
            ['menu_item' => 'HotPot Lunch', 'price' => 548.00],
            ['menu_item' => 'HotPot Dinner', 'price' => 598.00],
            ['menu_item' => 'Fusion', 'price' => 798.00],
            
        ]);
    }
}
