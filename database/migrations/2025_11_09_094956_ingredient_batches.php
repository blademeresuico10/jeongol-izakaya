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
        Schema::create('ingredient_batches', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['active', 'expired'])->default('active');
            $table->foreignId('ingredient_id')->constrained('ingredients')->onDelete('cascade');
            $table->date('arrived_at');
            $table->date('expiration_date');
            $table->decimal('quantity', 8, 2);
            $table->timestamps();
            $table->index('status');
            $table->index('ingredient_id');
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
