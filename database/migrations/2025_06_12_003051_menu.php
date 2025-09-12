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
            $table->string('menu_item');
            $table->decimal('regular_price', 10, 2); 
            $table->decimal('student_price', 10, 2)->nullable(); 
            $table->decimal('govt_employee_price', 10, 2)->nullable(); 
            $table->string('image')->nullable();
            $table->string('category');
            $table->boolean('has_customer_discount')->default(false); 
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu');
    }
};
