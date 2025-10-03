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
            $reservations->dateTime('started_at');
            $reservations->dateTime('ended_at');
            $reservations->enum('status', ['Pending', 'Active', 'Rejected', 'Completed'])->default('Pending');
            $reservations->foreignId('table_id')->constrained('tables')->onDelete('cascade');
            $reservations->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('cascade');
            $reservations->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $reservations->timestamps();
            $reservations->index(['table_id', 'started_at']);
            $reservations->index(['status', 'started_at']);
            $reservations->unique(['table_id', 'started_at', 'status'], 'unique_table_time_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
