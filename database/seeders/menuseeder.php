<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('menu')->insert([
            [
                'menu_item' => 'Samgyupsal',
                'regular_price' => 598.00,
                'image' => 'samgyup.jpeg',
                'category' => 'main',
                'has_customer_discount' => true,
            ],
            
            [
                'menu_item' => 'HotPot',
                'regular_price' => 598.00,
                'image' => 'hotpot.png',
                'category' => 'main',
                'has_customer_discount' => true,
            ],
            
            [
                'menu_item' => 'Fusion',
                'regular_price' => 798.00,
                'image' => 'fusion.png',
                'category' => 'main',
                'has_customer_discount' => true,
            ],

            [
                'menu_item' => 'Wagyu',
                'regular_price' => 350.00,
                'image' => 'wagyu_alacarte.jpg',
                'category' => 'add_ons',
                'has_customer_discount' => false,
            ],

            [
                'menu_item' => 'Coco Island',
                'regular_price' => 120.00,
                'image' => 'coco_island.png',
                'category' => 'add_ons',
                'has_customer_discount' => false,
            ],

            [
                'menu_item' => 'Coke',
                'regular_price' => 60.00,
                'image' => 'coca_cola.png',
                'category' => 'add_ons',
                'has_customer_discount' => false,
            ]
        ]);
    }
}