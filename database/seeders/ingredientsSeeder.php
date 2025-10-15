<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ingredientsSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $ingredients = [
            ['name' => 'Beef',      'category' => 'meat',     'unit' => 'kg',  'stocks' => 50],
            ['name' => 'Pork',      'category' => 'meat',     'unit' => 'kg',  'stocks' => 50],
            ['name' => 'Chicken',   'category' => 'meat',     'unit' => 'kg',  'stocks' => 50],
            ['name' => 'Shrimp',    'category' => 'meat',     'unit' => 'kg',  'stocks' => 50],
            ['name' => 'Lettuce',       'category' => 'vegetables',  'unit' => 'kg',  'stocks' => 20],
            ['name' => 'Mushroom',      'category' => 'vegetables',  'unit' => 'kg',  'stocks' => 20],
            ['name' => 'Sweet Carrots', 'category' => 'vegetables',  'unit' => 'kg',  'stocks' => 20],
            ['name' => 'Potatoes',      'category' => 'vegetables',  'unit' => 'kg',  'stocks' => 20],
            ['name' => 'Cabbage',       'category' => 'vegetables',  'unit' => 'kg',  'stocks' => 20],
            ['name' => 'Hotpot Balls',  'category' => 'soupbase', 'unit' => 'kg',  'stocks' => 20],
            ['name' => 'Noodles',       'category' => 'soupbase', 'unit' => 'kg',  'stocks' => 20],
            ['name' => 'Coke',      'category' => 'beverage', 'unit' => 'pieces', 'stocks' => 100],
            ['name' => 'Coke Zero', 'category' => 'beverage', 'unit' => 'pieces', 'stocks' => 100],
            ['name' => 'Water',     'category' => 'beverage', 'unit' => 'pieces', 'stocks' => 100],
            ['name' => 'Soju',      'category' => 'beverage', 'unit' => 'pieces', 'stocks' => 100],
            ['name' => 'Ice Talk',  'category' => 'beverage', 'unit' => 'pieces', 'stocks' => 100],
            ['name' => 'Wagyu',     'category' => 'meat',    'unit' => 'kg',     'stocks' => 50], 
            ['name' => 'Coco Island', 'category' => 'beverage', 'unit' => 'pieces', 'stocks' => 100],  
        ];

        $ingredients = array_map(function ($item) use ($now) {
            return array_merge($item, [
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }, $ingredients);

        DB::table('ingredients')->insert($ingredients);
    }
}
