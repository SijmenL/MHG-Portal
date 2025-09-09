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
        // Add the new polymorphic columns, `fileable_id` and `fileable_type`.
        Schema::table('files', function (Blueprint $table) {
            $table->unsignedBigInteger('fileable_id')->nullable()->after('id');
            $table->string('fileable_type')->nullable()->after('fileable_id');
        });

        // We will move the existing `lesson_id` data into the new columns.
        DB::table('files')->whereNotNull('lesson_id')->update([
            'fileable_id' => DB::raw('lesson_id'),
            'fileable_type' => 'App\Models\Lesson',
        ]);

        if (Schema::hasColumn('files', 'lesson_id')) {
            Schema::table('files', function (Blueprint $table) {
                // Verwijder de foreign key constraint expliciet
                $table->dropForeign('lesson_files_lesson_id_foreign');

                // Daarna mag de kolom weg
                $table->dropColumn('lesson_id');
            });
        }


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('files', function (Blueprint $table) {
            // Restore the lesson_id column
            $table->unsignedBigInteger('lesson_id')->nullable()->after('id');
            $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('cascade');

            // Move the data back to the old column
            DB::table('files')->where('fileable_type', 'App\Models\Lesson')->update([
                'lesson_id' => DB::raw('fileable_id'),
            ]);

            // Drop the polymorphic columns
            $table->dropColumn('fileable_id');
            $table->dropColumn('fileable_type');
        });
    }
};
