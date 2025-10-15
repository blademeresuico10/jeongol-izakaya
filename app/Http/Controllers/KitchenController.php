<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\orders;
use App\Models\ingredients;
use App\Models\MenuIngredient;
use App\Models\ingredientMovements;
use App\Models\table;
use App\Models\menu;
use App\Models\UnlimitedMeatLog;


class KitchenController extends Controller
{
    public function home()
    {
        return view('kitchen.home');
    }



}
