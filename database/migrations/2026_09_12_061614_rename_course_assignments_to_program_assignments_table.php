<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::rename('course_assignments', 'program_assignments');

        DB::table('role_permissions')
            ->where('module', 'course-assignment')
            ->update(['module' => 'program-assignment']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('role_permissions')
            ->where('module', 'program-assignment')
            ->update(['module' => 'course-assignment']);

        Schema::rename('program_assignments', 'course_assignments');
    }
};
