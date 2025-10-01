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
            $table->boolean('is_logged_in')->default(false);
            $table->timestamp('last_login_at')->nullable();
            $table->string('session_token')->nullable();
            $table->rememberToken(); 
            $table->timestamps();
            $table->softDeletes();
            $table->index(['email', 'role']); 
            $table->index('status');
            $table->index(['role', 'is_logged_in']); 
            $table->index('session_token'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};