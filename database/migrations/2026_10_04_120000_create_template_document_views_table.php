<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One row per (template, faculty) once that faculty member has opened
        // it — absence of a row is what makes a newly-distributed template
        // show as "New" for them. Mirrors CollaborativeDocumentViewer's
        // per-user tracking pattern already used elsewhere in this app.
        Schema::create('template_document_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_document_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('viewed_at');
            $table->unique(['template_document_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_document_views');
    }
};
