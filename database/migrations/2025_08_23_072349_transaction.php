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
            $table->string('transaction_no', 50)->unique();
            $table->decimal('subtotal', 10, 2);          
            $table->decimal('discount_total', 10, 2)->default(0); 
            $table->decimal('total', 10, 2);      
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
