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
        Schema::table('template_documents', function (Blueprint $table) {
            // Set when Admin creates the template directly as a Google Doc
            // instead of uploading a static file — the "master" copy that
            // stays view-only once distributed
            $table->string('google_doc_id')->nullable()->after('file_size');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('template_documents', function (Blueprint $table) {
            $table->dropColumn('google_doc_id');
        });
    }
};
