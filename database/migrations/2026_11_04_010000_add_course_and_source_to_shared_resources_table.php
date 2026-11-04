<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shared_resources', function (Blueprint $table) {
            // Empty course: the resource is for the whole program.
            $table->foreignId('course_id')->nullable()->after('program')->constrained()->nullOnDelete();
            // The CMS document it was shared from, so sharing it again updates this entry.
            $table->foreignId('source_module_id')->nullable()->after('course_id')->constrained('content_modules')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('shared_resources', function (Blueprint $table) {
            $table->dropConstrainedForeignId('source_module_id');
            $table->dropConstrainedForeignId('course_id');
        });
    }
};
