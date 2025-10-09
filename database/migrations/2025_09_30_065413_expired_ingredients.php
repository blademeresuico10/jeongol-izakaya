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
        Schema::create('expired_ingredients', function (Blueprint $table) {
            $table->id();
            $table->decimal('quantity', 8, 2);
            $table->date('expired_at');
            $table->foreignId('ingredient_batch_id')->constrained('ingredient_batches')->onDelete('cascade');
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
