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
        Schema::create('co_po_mapping', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_outcome_id')->constrained('course_outcomes')->onDelete('cascade');
            $table->foreignId('program_outcome_id')->constrained('program_outcomes')->onDelete('cascade');
            // Optional strength rating (e.g. I/E/D or Low/Medium/High) — left free-form, nullable
            $table->string('level')->nullable();
            $table->timestamps();
            $table->unique(['course_outcome_id', 'program_outcome_id'], 'co_po_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('co_po_mapping');
    }
};
