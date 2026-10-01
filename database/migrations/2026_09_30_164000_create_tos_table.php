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
        Schema::create('tos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_assignment_id')->constrained()->onDelete('cascade');
            $table->foreignId('exam_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('grading_period', ['Prelim', 'Midterm', 'Final']);
            $table->unsignedInteger('total_hours')->default(0);
            $table->unsignedInteger('total_items')->default(0);
            $table->unsignedInteger('total_points')->default(0);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['program_assignment_id', 'grading_period']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tos');
    }
};
