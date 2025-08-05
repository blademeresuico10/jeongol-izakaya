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
        [
            'menu_item' => 'Samgyup Lunch',
            'price' => 548.00,
            'image' => 'samgyup.jpeg',
            'category' => 'main',
        ],
        [
            'menu_item' => 'Samgyup Dinner',
            'price' => 598.00,
            'image' => 'samgyup.jpeg',
            'category' => 'main'
        ],
        [
            'menu_item' => 'HotPot',
            'price' => 598.00,
            'image' => 'hotpot.png',
            'category' => 'main'
        ],
        [
            'menu_item' => 'Fusion',
            'price' => 798.00,
            'image' => 'fusion.png',
            'category' => 'main'
        ],

        [
            'menu_item' => 'Wagyu Alacarte',
            'price' => 350.00,
            'image' => 'wagyu_alacarte.jpg',
            'category' => 'add_ons'
        ],

        [
            'menu_item' => 'Unlimited Wagyu',
            'price' => 388.00,
            'image' => 'wagyu.jpeg',
            'category' => 'add_ons'
        ],

        [
            'menu_item' => 'Coco Island',
            'price'=> 120.00,
            'image'=> 'coco_island.png',
            'category'=> 'add_ons'
        ],

        [
            'menu_item' => 'Coke',
            'price'=> 60.00,
            'image'=> 'coca_cola.png',
            'category'=> 'add_ons'
        ]
        
    ]);

    }
}
