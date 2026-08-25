<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignId('faculty_id')->constrained('users')->onDelete('cascade');
            $table->string('school_year'); // e.g. "2025-2026"
            $table->string('semester'); // First Semester, Second Semester, Summer
            $table->foreignId('assigned_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['course_id', 'faculty_id', 'school_year', 'semester'], 'course_assign_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_assignments');
    }
};