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
        Schema::table('course_topics', function (Blueprint $table) {
            // The actual lesson/module content for this topic (PDF only — fed directly
            // to Claude's document input for AI question generation, no text conversion
            // needed). Separate from the whole-course "Official OBTL Document" upload.
            $table->string('module_path')->nullable()->after('notes');
            $table->string('module_file_name')->nullable()->after('module_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_topics', function (Blueprint $table) {
            $table->dropColumn(['module_path', 'module_file_name']);
        });
    }
};
