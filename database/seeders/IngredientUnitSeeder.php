<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IngredientUnitSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $units = [
            ['name' => 'Kilogram', 'abbreviation' => 'kg'],
            ['name' => 'Pieces', 'abbreviation' => 'pieces'],
        ];


        DB::table('ingredient_units')->insert($units);
    }
}