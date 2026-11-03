<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Official documents of the college, kept apart from each faculty member's own content.
        Schema::create('system_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users');
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('is_published')->default(false);
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type', 10);
            $table->unsignedBigInteger('file_size');
            $table->string('document_key', 128)->nullable();
            $table->foreignId('last_edited_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('last_edited_at')->nullable();
            $table->timestamps();
        });

        // Lets the Secretary edit a template inside the system before sending it to the Dean.
        Schema::table('template_documents', function (Blueprint $table) {
            $table->string('document_key', 128)->nullable();
            $table->foreignId('last_edited_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('last_edited_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('template_documents', function (Blueprint $table) {
            $table->dropConstrainedForeignId('last_edited_by');
            $table->dropColumn(['document_key', 'last_edited_at']);
        });

        Schema::dropIfExists('system_documents');
    }
};
