<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('stock', function (Blueprint $stock) {
            $stock->id();
            $stock->string('stock_name')->unique();
            $stock->decimal('stock_quantity');
            $stock->timestamps();
            $stock->softDeletes();
        });
    }


    public function down(): void
    {
        //
    }
};
