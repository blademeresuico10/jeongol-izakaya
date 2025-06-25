<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $reservations) {
            $reservations->id();
            $reservations->unsignedInteger('pax');
            $reservations->dateTime('reservation_time');
            $reservations->decimal('advance_payment', 8, 2)->nullable()->default(0.00);
            $reservations->unsignedInteger('table_number');
            $reservations->text('notes')->nullable();
            $reservations->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('cascade');
            $reservations->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $reservations->timestamps(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
