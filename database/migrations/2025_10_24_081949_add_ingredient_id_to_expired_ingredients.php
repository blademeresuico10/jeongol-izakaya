<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('expired_ingredients', function (Blueprint $table) {
            $table->foreignId('ingredient_id')->after('id')->constrained('ingredients')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('expired_ingredients', function (Blueprint $table) {
            $table->dropForeign(['ingredient_id']);
            $table->dropColumn('ingredient_id');
        });
    }
};
