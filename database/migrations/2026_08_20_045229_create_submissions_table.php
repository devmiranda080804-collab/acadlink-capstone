<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requirement_id')->constrained('submission_requirements')->onDelete('cascade');
            $table->foreignId('faculty_id')->constrained('users')->onDelete('cascade');
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type');
            $table->unsignedBigInteger('file_size')->default(0);
            $table->string('status')->default('submitted'); // submitted, late
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            $table->unique(['requirement_id', 'faculty_id']); // isang pasa per requirement per faculty
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};