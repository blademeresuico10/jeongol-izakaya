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
            Schema::create('ewallet_details', function (Blueprint $table) {
                $table->id();
                $table->enum('payment_method', ['gcash', 'maya'])->index();
                $table->string('wallet_name'); 
                $table->string('wallet_number'); 
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ewallet_details');
    }
};
