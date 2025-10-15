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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->nullable()->constrained('reservations')->onDelete('cascade');
            $table->foreignId('walk_in_id')->nullable()->constrained('walk_ins')->onDelete('cascade');
            $table->foreignId('menu_id')->constrained('menu')->onDelete('cascade');
            $table->unsignedInteger('quantity');
            $table->decimal('price', 8, 2);
            $table->text('notes')->nullable();
            $table->enum('status', ['Pending','Ready', 'Served', 'Cancelled'])->default('Pending');
            $table->timestamps();
        });
    }

    
    public function down(): void
    {
        //
    }
};
