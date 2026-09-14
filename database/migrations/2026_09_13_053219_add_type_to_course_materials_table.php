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
        Schema::table('course_materials', function (Blueprint $table) {
            // syllabus, tos, exam_bank, teaching_material — matches the client's
            // "Master Course Folder" spec (Official Syllabus / Official TOS /
            // Standard Exam Bank / Core Teaching Materials)
            $table->string('type')->default('teaching_material')->after('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_materials', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
