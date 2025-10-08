<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_level_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ingredient_id')->constrained('ingredients')->onDelete('cascade');
            $table->decimal('low_stock', 8, 2);
            $table->decimal('critical_stock', 8, 2)->nullable();
            $table->timestamps();
            $table->unique('ingredient_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_level_alerts');
    }
};
