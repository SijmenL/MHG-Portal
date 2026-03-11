<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('files', function (Blueprint $table) {
            // Make the column nullable so it accepts 'null'
            $table->unsignedBigInteger('lesson_id')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('files', function (Blueprint $table) {
            $table->unsignedBigInteger('lesson_id')->nullable(false)->change();
        });
    }
};
