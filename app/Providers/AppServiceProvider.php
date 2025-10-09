<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use App\Http\Livewire\TableLayout; // Import the TableLayout class
use Livewire\Livewire; // Import the Livewire facade


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Fortify::loginView(function () {
            return view('staff_login');
        });
    
    }
}
