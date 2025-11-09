<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ingredientsSeeder extends Seeder
{
    public function run(): void
    {
        $meatId = DB::table('ingredient_categories')->where('slug', 'meat')->value('id');
        $vegetablesId = DB::table('ingredient_categories')->where('slug', 'vegetables')->value('id');
        $soupbaseId = DB::table('ingredient_categories')->where('slug', 'soupbase')->value('id');
        $beverageId = DB::table('ingredient_categories')->where('slug', 'beverage')->value('id');

        $kgId = DB::table('ingredient_units')->where('abbreviation', 'kg')->value('id');
        $piecesId = DB::table('ingredient_units')->where('abbreviation', 'pieces')->value('id');

        $ingredients = [
            ['name' => 'Beef',           'category_id' => $meatId,        'unit_id' => $kgId,     'stocks' => 52.00,  'created_at' => '2025-10-21 01:06:31', 'updated_at' => '2025-11-08 10:51:31'],
            ['name' => 'Pork',           'category_id' => $meatId,        'unit_id' => $kgId,     'stocks' => 34.38,  'created_at' => '2025-10-21 01:06:31', 'updated_at' => '2025-11-08 10:51:31'],
            ['name' => 'Chicken',        'category_id' => $meatId,        'unit_id' => $kgId,     'stocks' => 39.20,  'created_at' => '2025-10-21 01:06:31', 'updated_at' => '2025-11-08 10:51:30'],
            ['name' => 'Shrimp',         'category_id' => $meatId,        'unit_id' => $kgId,     'stocks' => 38.30,  'created_at' => '2025-10-21 01:06:31', 'updated_at' => '2025-11-08 10:51:30'],
            ['name' => 'Lettuce',        'category_id' => $vegetablesId,  'unit_id' => $kgId,     'stocks' => 7.57,   'created_at' => '2025-10-21 01:06:31', 'updated_at' => '2025-11-08 10:51:30'],
            ['name' => 'Mushroom',       'category_id' => $vegetablesId,  'unit_id' => $kgId,     'stocks' => 15.14,  'created_at' => '2025-10-21 01:06:31', 'updated_at' => '2025-11-08 10:51:31'],
            ['name' => 'Sweet Carrots',  'category_id' => $vegetablesId,  'unit_id' => $kgId,     'stocks' => 7.85,   'created_at' => '2025-10-21 01:06:31', 'updated_at' => '2025-11-08 09:57:19'],
            ['name' => 'Potatoes',       'category_id' => $vegetablesId,  'unit_id' => $kgId,     'stocks' => 20.00,  'created_at' => '2025-10-21 01:06:31', 'updated_at' => '2025-10-21 01:06:31'],
            ['name' => 'Cabbage',        'category_id' => $vegetablesId,  'unit_id' => $kgId,     'stocks' => 18.16,  'created_at' => '2025-10-21 01:06:31', 'updated_at' => '2025-11-08 10:51:30'],
            ['name' => 'Hotpot Balls',   'category_id' => $soupbaseId,    'unit_id' => $kgId,     'stocks' => 15.95,  'created_at' => '2025-10-21 01:06:31', 'updated_at' => '2025-11-08 10:51:31'],
            ['name' => 'Noodles',        'category_id' => $soupbaseId,    'unit_id' => $kgId,     'stocks' => 15.93,  'created_at' => '2025-10-21 01:06:31', 'updated_at' => '2025-11-08 10:51:31'],
            ['name' => 'Coke',           'category_id' => $beverageId,    'unit_id' => $piecesId, 'stocks' => 61.00,  'created_at' => '2025-10-21 01:06:31', 'updated_at' => '2025-11-09 12:25:52'],
            ['name' => 'Wagyu',          'category_id' => $meatId,        'unit_id' => $kgId,     'stocks' => 49.10,  'created_at' => '2025-10-21 01:06:31', 'updated_at' => '2025-11-08 07:39:59'],
            ['name' => 'Coco Island',    'category_id' => $beverageId,    'unit_id' => $piecesId, 'stocks' => 72.00,  'created_at' => '2025-10-21 01:06:31', 'updated_at' => '2025-11-08 10:10:56'],
            ['name' => 'Watermelon',     'category_id' => $vegetablesId,  'unit_id' => $kgId,     'stocks' => 20.00,  'created_at' => '2025-10-23 22:15:07', 'updated_at' => '2025-11-07 19:18:43'],
            ['name' => 'Water',          'category_id' => $beverageId,    'unit_id' => $piecesId, 'stocks' => 30.00,  'created_at' => '2025-10-24 00:34:05', 'updated_at' => '2025-11-09 12:25:52'],
            ['name' => 'Fish',           'category_id' => $meatId,        'unit_id' => $kgId,     'stocks' => 4.98,   'created_at' => '2025-11-08 09:14:12', 'updated_at' => '2025-11-08 09:57:19'],
        ];

        DB::table('ingredients')->insert($ingredients);
    }
}