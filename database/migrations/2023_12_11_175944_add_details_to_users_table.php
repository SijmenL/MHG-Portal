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
        Schema::table('users', function (Blueprint $table) {
            $table->string('sex')->nullable();
            $table->string('name');
            $table->string('infix')->nullable();
            $table->string('last_name')->nullable();
            $table->date('birth_date')->nullable();;
            $table->string('street')->nullable();;
            $table->string('postal_code')->nullable();;
            $table->string('city')->nullable();;
            $table->string('phone')->nullable();;
            $table->boolean('avg')->nullable();;
            $table->string('profile_picture')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
