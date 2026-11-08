<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Course Filing now counts what faculty submit for each course they teach, instead of
    // the Program Head's reference files in Course Oversight.
    public function up(): void
    {
        Schema::table('submission_requirements', function (Blueprint $table) {
            // One file per course taught in the term, or one per faculty member.
            $table->boolean('per_course')->default(true)->after('type');
            $table->string('school_year', 20)->nullable()->after('per_course');
            $table->string('semester', 30)->nullable()->after('school_year');
        });

        // Requirements made before this were collected once per faculty member.
        DB::table('submission_requirements')->update(['per_course' => false]);

        Schema::table('submissions', function (Blueprint $table) {
            $table->foreignId('course_id')->nullable()->after('faculty_id')->constrained()->nullOnDelete();
            $table->string('filing_status', 20)->nullable()->after('submitted_at');
            $table->foreignId('filing_checked_by')->nullable()->after('filing_status')->constrained('users')->nullOnDelete();
            $table->timestamp('filing_checked_at')->nullable()->after('filing_checked_by');
            $table->text('filing_return_reason')->nullable()->after('filing_checked_at');

            // Several files per faculty member now (one per course); still one per course.
            $table->index(['requirement_id', 'faculty_id']);
        });

        Schema::table('submissions', function (Blueprint $table) {
            $table->dropUnique(['requirement_id', 'faculty_id']);
            $table->unique(['requirement_id', 'course_id']);
        });
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropUnique(['requirement_id', 'course_id']);
            $table->unique(['requirement_id', 'faculty_id']);
        });

        Schema::table('submissions', function (Blueprint $table) {
            $table->dropIndex(['requirement_id', 'faculty_id']);
            $table->dropConstrainedForeignId('filing_checked_by');
            $table->dropConstrainedForeignId('course_id');
            $table->dropColumn(['filing_status', 'filing_checked_at', 'filing_return_reason']);
        });

        Schema::table('submission_requirements', function (Blueprint $table) {
            $table->dropColumn(['per_course', 'school_year', 'semester']);
        });
    }
};
