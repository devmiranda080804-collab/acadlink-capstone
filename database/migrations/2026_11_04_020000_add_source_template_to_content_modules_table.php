<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('content_modules', function (Blueprint $table) {
            // The template this is a personal copy of, so Make Copy can reopen it.
            $table->foreignId('source_template_id')->nullable()->after('created_by')->constrained('template_documents')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('content_modules', function (Blueprint $table) {
            $table->dropConstrainedForeignId('source_template_id');
        });
    }
};
