<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WaitingStaffController extends Controller
{
    public function home(){
        return view('waiting_staff.home');
    }
}
