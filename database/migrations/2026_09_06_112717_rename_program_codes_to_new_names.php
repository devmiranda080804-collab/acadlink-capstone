<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected array $mapping = [
        'BSA' => 'BSA',
        'BSMA'  => 'BSMA',
        'BSOA'  => 'BSOA',
    ];

    public function up(): void
    {
        foreach ($this->mapping as $old => $new) {
            // Users (Program Head at Faculty accounts)
            DB::table('users')->where('program', $old)->update(['program' => $new]);

            // Courses
            DB::table('courses')->where('program', $old)->update(['program' => $new]);

            // Templates
            DB::table('templates')->where('program', $old)->update(['program' => $new]);

            // Announcement targeting
            DB::table('announcement_programs')->where('program', $old)->update(['program' => $new]);

            // Repository documents (Secretary uploads)
            if (DB::getSchemaBuilder()->hasTable('repository_documents')) {
                DB::table('repository_documents')->where('program', $old)->update(['program' => $new]);
            }

            // Shared resources (Faculty uploads)
            if (DB::getSchemaBuilder()->hasTable('shared_resources')) {
                DB::table('shared_resources')->where('program', $old)->update(['program' => $new]);
            }

            // Submission requirements (Program Head)
            if (DB::getSchemaBuilder()->hasTable('submission_requirements')) {
                DB::table('submission_requirements')->where('program', $old)->update(['program' => $new]);
            }
        }
    }

    public function down(): void
    {
        // Reverse mapping kung kailangan i-undo
        $reverse = array_flip($this->mapping);
        foreach ($reverse as $new => $old) {
            DB::table('users')->where('program', $new)->update(['program' => $old]);
            DB::table('courses')->where('program', $new)->update(['program' => $old]);
            DB::table('templates')->where('program', $new)->update(['program' => $old]);
            DB::table('announcement_programs')->where('program', $new)->update(['program' => $old]);
        }
    }
};