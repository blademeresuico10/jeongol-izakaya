<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('firstname');
            $table->string('lastname');
            $table->enum('role', ['Admin', 'Receptionist', 'Cashier', 'Kitchen Staff']);
            $table->string('contact_number');
            $table->string('email')->nullable()->unique(); 
            $table->string('username')->unique();
            $table->timestamp('email_verified_at')->nullable(); 
            $table->string('password');
            $table->enum('status', ['Active', 'Inactive', 'Deleted'])->default('Active');
            $table->string('profile_picture')->nullable();
            $table->rememberToken(); 
            $table->timestamps();
            $table->softDeletes();

            $table->index(['email', 'role']); 
            $table->index('status'); 
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
