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
        // The Template Approval/Distribution pipeline is now upload-only
        // (view + download) — confirmed zero rows in either table ever used
        // Google Docs before dropping them.
        Schema::dropIfExists('template_copies');

        Schema::table('template_documents', function (Blueprint $table) {
            $table->dropColumn('google_doc_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('template_documents', function (Blueprint $table) {
            $table->string('google_doc_id')->nullable()->after('file_size');
        });

        Schema::create('template_copies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_document_id')->constrained()->onDelete('cascade');
            $table->foreignId('faculty_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->string('google_doc_id');
            $table->timestamps();
        });
    }
};
