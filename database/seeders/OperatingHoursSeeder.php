<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OperatingHoursSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('operating_hours')->insert([
            'is_default' => true,
            'date' => null,                
            'open_time' => '11:30:00',      
            'close_time' => '20:00:00',     
            'is_closed' => false,
        ]);
    }
}
