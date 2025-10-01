<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_no', 50)->nullable()->unique();
            $table->decimal('discount_total', 10, 2)->default(0);
            $table->decimal('advance_payment', 10, 2)->default(0.00);
            $table->decimal('to_pay', 10, 2);
            $table->decimal('cash_received', 10, 2)->nullable();
            $table->decimal('orders_total', 10, 2);
            $table->decimal('grand_total', 10, 2);
            $table->decimal('change', 10, 2)->nullable();
            $table->enum('payment_method', ['Cash', 'GCash', 'PayMaya'])->default('Cash');
            $table->enum('status', ['Pending', 'Completed', 'Refunded'])->default('Completed');
            $table->foreignId('reservation_id')->constrained('reservations')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('cashier_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
