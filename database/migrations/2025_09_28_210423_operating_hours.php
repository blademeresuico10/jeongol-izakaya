<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('operating_hours', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_default')->default(false);   
            $table->date('date')->nullable();               
            $table->time('open_time')->nullable();         
            $table->time('close_time')->nullable();        
            $table->boolean('is_closed')->default(false);   
        });
    }

    public function down(): void {}
};
