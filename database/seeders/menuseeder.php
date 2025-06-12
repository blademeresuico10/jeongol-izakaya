<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class menuseeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('menu')->insert([
            ['menu_item' => 'SL', 'price' => 548.00],
            ['menu_item' => 'SD', 'price' => 598.00],
            ['menu_item' => 'HPL', 'price' => 548.00],
            ['menu_item' => 'HPD', 'price' => 598.00],
            ['menu_item' => 'FLD', 'price' => 798.00],
            
        ]);
    }
}
