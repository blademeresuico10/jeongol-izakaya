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
        Schema::create('menu_discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained('menu')->onDelete('cascade');
            $table->decimal('discount_percentage', 5, 2); 
            $table->enum('discount_type', ['Student', 'Government Employee', 'Senior Citizen', 'PWD']);
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
