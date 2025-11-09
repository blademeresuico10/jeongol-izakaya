<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MenuCategorySeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $categories = [
            ['name' => 'Main Course', 'is_active' => true],
            ['name' => 'Appetizers', 'is_active' => true],
            ['name' => 'Beverages', 'is_active' => true],
            ['name' => 'Desserts', 'is_active' => true],
        ];

        DB::table('menu_categories')->insert($categories);
    }
}