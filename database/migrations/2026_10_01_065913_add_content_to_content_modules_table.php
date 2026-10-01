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
        Schema::table('content_modules', function (Blueprint $table) {
            // In-app WYSIWYG content (Quill HTML output) — the "write directly
            // in AcadLink" alternative to google_doc_id/file_path, per the
            // adviser's clarification that CMS content should be editable
            // inside the web app itself, not just linked out to Google Docs.
            $table->longText('content')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('content_modules', function (Blueprint $table) {
            $table->dropColumn('content');
        });
    }
};
