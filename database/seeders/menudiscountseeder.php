<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuDiscountSeeder extends Seeder
{
    public function run(): void
    {
        // Get IDs for main menu items
        $samgyupsalId = DB::table('menu')->where('menu_item', 'Samgyupsal')->value('id');
        $hotpotId     = DB::table('menu')->where('menu_item', 'HotPot')->value('id');
        $fusionId     = DB::table('menu')->where('menu_item', 'Fusion')->value('id');

        $menu_discounts = [
            // Samgyupsal
            ['menu_id' => $samgyupsalId, 'discount_type' => 'Government Employee', 'discount_percentage' => 5.02],
            ['menu_id' => $samgyupsalId, 'discount_type' => 'Student', 'discount_percentage' => 8.36],
            ['menu_id' => $samgyupsalId, 'discount_type' => 'Senior Citizen', 'discount_percentage' => 20.00],
            ['menu_id' => $samgyupsalId, 'discount_type' => 'PWD', 'discount_percentage' => 20.00],

            // Hotpot
            ['menu_id' => $hotpotId, 'discount_type' => 'Senior Citizen', 'discount_percentage' => 20.00],
            ['menu_id' => $hotpotId, 'discount_type' => 'PWD', 'discount_percentage' => 20.00],

            // Fusion
            ['menu_id' => $fusionId, 'discount_type' => 'Student', 'discount_percentage' => 6.27],
            ['menu_id' => $fusionId, 'discount_type' => 'Senior Citizen', 'discount_percentage' => 20.00],
            ['menu_id' => $fusionId, 'discount_type' => 'PWD', 'discount_percentage' => 20.00],
        ];

        DB::table('menu_discounts')->insert($menu_discounts);
    }
}
