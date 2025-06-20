<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $customers) {
            $customers->id();
            $customers->string('name');
            $customers->string('contact_number')->nullable(); 
            $customers->integer('id_type')->nullable();        
            $customers->timestamps(); 
        });
    }

   
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};

