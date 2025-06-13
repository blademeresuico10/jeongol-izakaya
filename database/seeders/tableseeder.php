<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class tableseeder extends Seeder
{
    
    public function run(): void
    {
        DB::table('tables')->insert([
            ['table_number' => 1, 'capacity' => 4],
            ['table_number' => 2, 'capacity' => 6],
            ['table_number' => 3, 'capacity' => 2],
            ['table_number' => 4, 'capacity' => 8],
            ['table_number' => 5, 'capacity' => 4],
            ['table_number' => 6, 'capacity' => 2],
            ['table_number' => 7, 'capacity' => 6],
            ['table_number' => 8, 'capacity' => 4],
            ['table_number' => 9, 'capacity' => 8],
            ['table_number' => 10, 'capacity' => 2],
            ['table_number' => 11, 'capacity' => 4],
            ['table_number' => 12, 'capacity' => 6],
            ['table_number' => 13, 'capacity' => 2],
            ['table_number' => 14, 'capacity' => 8],
            ['table_number' => 15, 'capacity' => 4],
            ['table_number' => 16, 'capacity' => 6],
            ['table_number' => 17, 'capacity' => 2],
           
        ]);
    }
}
