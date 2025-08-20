<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('reservation_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained('reservations')->onDelete('cascade');
            $table->string('registered_name')->nullable();   
            $table->string('number')->nullable();           
            $table->decimal('amount', 8, 2)->default(0.00); 
            $table->string('method')->nullable();           
            $table->string('ref_no')->nullable();          
            $table->string('proof_path')->nullable();     
            $table->enum('status', ['pending', 'approved', 'confirmed', 'rejected'])->default('pending');
            $table->timestamps();
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
