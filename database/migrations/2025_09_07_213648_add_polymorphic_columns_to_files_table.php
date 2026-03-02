<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('files', function (Blueprint $table) {
            $table->string('location')->nullable()->after('id');
            $table->unsignedBigInteger('location_id')->nullable()->after('location');
        });

        DB::table('files')->whereNotNull('lesson_id')->update([
            'location_id' => DB::raw('lesson_id'),
            'location' => 'Lesson',
        ]);

//        if (Schema::hasColumn('files', 'lesson_id')) {
//            Schema::table('files', function (Blueprint $table) {
//                // Verwijder de foreign key constraint expliciet
//                $table->dropForeign('files_lesson_id_foreign');
//
//                // Daarna mag de kolom weg
//                $table->dropColumn('lesson_id');
//            });
//        }


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eerst de kolom weer aanmaken
        Schema::table('files', function (Blueprint $table) {
            $table->unsignedBigInteger('lesson_id')->nullable()->after('id');
        });

        // Daarna de data terugzetten
        DB::table('files')->where('fileable_type', 'App\Models\Lesson')->update([
            'lesson_id' => DB::raw('fileable_id'),
        ]);

        // Daarna de foreign key en de polymorphic kolommen verwijderen
        Schema::table('files', function (Blueprint $table) {
            $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('cascade');

            $table->dropColumn('location');
            $table->dropColumn('location_id');
        });
    }

};
