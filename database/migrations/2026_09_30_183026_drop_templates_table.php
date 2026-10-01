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
        // Superseded by the template_documents / template_document_programs /
        // template_copies pipeline (Admin creates -> Secretary forwards ->
        // Program Head distributes -> Faculty copies). Nothing in the app
        // writes to this table anymore.
        Schema::dropIfExists('templates');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faculty_id')->constrained('users')->onDelete('cascade');
            $table->string('program');
            $table->string('title');
            $table->enum('type', ['syllabus', 'lesson_plan', 'course_guide', 'module']);
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type');
            $table->unsignedBigInteger('file_size')->default(0);
            $table->enum('status', [
                'pending_review',
                'needs_revision',
                'pending_approval',
                'approved',
                'rejected',
            ])->default('pending_review');
            $table->text('review_note')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('distributed_at')->nullable();
            $table->foreignId('distributed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('submission_date')->nullable();
            $table->timestamps();
        });
    }
};
