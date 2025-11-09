<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IngredientCategorySeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $categories = [
            ['name' => 'Meat', 'slug' => 'meat'],
            ['name' => 'Vegetables', 'slug' => 'vegetables'],
            ['name' => 'Soup Base', 'slug' => 'soupbase'],
            ['name' => 'Beverage', 'slug' => 'beverage'],
        ];

       

        DB::table('ingredient_categories')->insert($categories);
    }
}