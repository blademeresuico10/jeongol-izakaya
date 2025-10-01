<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('ingredients', function (Blueprint $stock) {
            $stock->id();
            $stock->string('name')->unique();
            $stock->enum('category', ['meat', 'vegetables', 'soupbase', 'beverage']);
            $stock->enum('unit', ['grams', 'pieces']);
            $stock->decimal('stocks', 8, 2);
            $stock->timestamps();
        });
    }


    public function down(): void {}
};
