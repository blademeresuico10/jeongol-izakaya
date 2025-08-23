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
        Schema::create('transaction_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade');
            $table->foreignId('order_detail_id')->constrained('order_details')->onDelete('cascade');
            $table->string('item_name', 255);
            $table->integer('quantity');
            $table->decimal('unit_price', 8, 2)->default(0);
            $table->decimal('item_subtotal', 10, 2)->default(0);
            $table->integer('discounted_persons')->default(0)->nullable();
            $table->decimal('discount_per_person', 8, 2)->default(0)->nullable();
            $table->decimal('item_discount_total', 8, 2)->default(0)->nullable();
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_details');
    }
};
