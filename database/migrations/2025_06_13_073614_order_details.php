<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_details', function (Blueprint $order_details) {
            $order_details->id();
            $order_details->decimal('order_price', 8, 2)->nullable();
            $order_details->unsignedInteger('quantity')->nullable();
            $order_details->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('cascade');
            $order_details->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $order_details->foreignId('menu_id')->constrained('menu')->onDelete('cascade');
            $order_details->foreignId('reservation_id')->constrained('reservations')->onDelete('cascade');
            $order_details->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
