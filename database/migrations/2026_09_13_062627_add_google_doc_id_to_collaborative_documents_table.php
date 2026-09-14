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
        Schema::table('collaborative_documents', function (Blueprint $table) {
            // The Google Docs file ID once the document is created via the Docs API —
            // editing now happens live in Google Docs itself, not in our own textarea
            $table->string('google_doc_id')->nullable()->after('content');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('collaborative_documents', function (Blueprint $table) {
            $table->dropColumn('google_doc_id');
        });
    }
};
