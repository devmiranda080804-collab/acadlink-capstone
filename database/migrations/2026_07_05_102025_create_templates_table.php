<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faculty_id')->constrained('users')->onDelete('cascade');
            $table->string('program'); // FMAD, OFD, BAD — para sa PH routing
            $table->string('title');
            $table->enum('type', ['syllabus', 'lesson_plan', 'course_guide', 'module']);
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type'); // pdf, docx
            $table->unsignedBigInteger('file_size')->default(0);
            $table->enum('status', [
                'pending_review',
                'needs_revision',
                'pending_approval',
                'approved',
                'rejected',
            ])->default('pending_review');
            $table->text('review_note')->nullable(); // feedback ni PH/Admin
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('templates');
    }
};