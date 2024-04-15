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
        Schema::create('loodsenbar_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('image_route')->nullable(); // Not used for now
            $table->unsignedBigInteger('c_user_id')->nullable(); // Use unsignedBigInteger for foreign keys
            $table->unsignedBigInteger('u_user_id')->nullable(); // Use unsignedBigInteger for foreign keys
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loodsenbar_categories');
    }
};
