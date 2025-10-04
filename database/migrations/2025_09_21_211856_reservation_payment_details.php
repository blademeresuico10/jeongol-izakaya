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
        Schema::create('reservation_payment_details', function (Blueprint $table) {
            $table->id();
            $table->string('registered_name')->nullable();
            $table->string('registered_number')->nullable();
            $table->decimal('advance_payment', 8, 2);
            $table->enum('payment_method', ['gcash', 'maya', 'cash'])->nullable();
            $table->string('payment_proof')->nullable();
            $table->foreignId('reservation_id')->constrained('reservations')->onDelete('cascade');
            $table->foreignId('ewallet_id')->nullable()->constrained('ewallet_details')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservation_payment_details');
    }
};
