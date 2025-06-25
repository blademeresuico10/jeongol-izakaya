<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('menu', function (Blueprint $menu) {
            $menu->id();
            $menu->string('menu_item');
            $menu->decimal('price', 10, 2);
            $menu->timestamps();
            
        });
    }

   
    public function down(): void
    {
        Schema::dropIfExists('menu');
    }
};
