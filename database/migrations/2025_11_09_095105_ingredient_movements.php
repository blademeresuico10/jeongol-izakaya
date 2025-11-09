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
        Schema::create('ingredient_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ingredient_id')->constrained('ingredients')->onDelete('cascade');
            $table->foreignId('ingredient_batch_id')->nullable()->constrained('ingredient_batches')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null');
            $table->enum('type', ['stock_in', 'stock_out', 'adjustment', 'expired', 'used']);
            $table->decimal('quantity', 8, 2);
            $table->decimal('stock_before', 8, 2);
            $table->decimal('stock_after', 8, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('ingredient_id');
            $table->index('ingredient_batch_id');
            $table->index('type');
            $table->index('created_at');
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
