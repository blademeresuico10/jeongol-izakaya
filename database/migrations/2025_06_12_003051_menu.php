<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('menu', function (Blueprint $table) {
            $table->id();
            $table->string('menu_item')->unique();
            $table->decimal('regular_price', 10, 2);
            $table->string('image')->nullable();
            $table->foreignId('category_id')->constrained('menu_categories')->onDelete('restrict');
            $table->boolean('has_customer_discount')->default(false);
            $table->enum('status', ['Active', 'Blocked'])->default('Active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu');
    }
};
