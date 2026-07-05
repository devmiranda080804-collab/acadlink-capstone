<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->string('title');           // e.g. Official Syllabus
            $table->string('file_path');       // storage path
            $table->string('file_name');       // original filename
            $table->string('file_type');       // pdf, docx, xlsx, zip
            $table->string('version')->default('v1.0');
            $table->unsignedBigInteger('file_size')->default(0); // bytes
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_materials');
    }
};