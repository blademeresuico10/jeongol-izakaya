<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('pax');
            $table->decimal('advance_payment', 8, 2)->nullable(); 
            $table->dateTime('reservation_time');
            $table->unsignedInteger('table_number');
            $table->text('notes')->nullable();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');

            $table->timestamps(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
