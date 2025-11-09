<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class menuseeder extends Seeder
{
    public function run(): void
    {
        $mainCourseId = DB::table('menu_categories')->value('id');
        $beveragesId = DB::table('menu_categories')->value('id');
        $appetizersId = DB::table('menu_categories')->value('id');

        DB::table('menu')->insert([
            [
                'menu_item' => 'Samgyupsal',
                'regular_price' => 598.00,
                'image' => 'samgyup.jpeg',
                'category_id' => $mainCourseId,
                'has_customer_discount' => true,
                'status' => 'Active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            [
                'menu_item' => 'HotPot',
                'regular_price' => 598.00,
                'image' => 'hotpot.png',
                'category_id' => $mainCourseId,
                'has_customer_discount' => true,
                'status' => 'Active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            [
                'menu_item' => 'Fusion',
                'regular_price' => 798.00,
                'image' => 'fusion.png',
                'category_id' => $mainCourseId,
                'has_customer_discount' => true,
                'status' => 'Active',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'menu_item' => 'Wagyu',
                'regular_price' => 350.00,
                'image' => 'wagyu_alacarte.jpg',
                'category_id' => $appetizersId, 
                'has_customer_discount' => false,
                'status' => 'Active',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'menu_item' => 'Coco Island',
                'regular_price' => 120.00,
                'image' => 'coco_island.png',
                'category_id' => $beveragesId,
                'has_customer_discount' => false,
                'status' => 'Active',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'menu_item' => 'Coke',
                'regular_price' => 60.00,
                'image' => 'coca_cola.png',
                'category_id' => $beveragesId,
                'has_customer_discount' => false,
                'status' => 'Active',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}