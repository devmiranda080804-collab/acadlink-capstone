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
        Schema::create('exam_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_section_id')->constrained()->onDelete('cascade');
            // Case Analysis sub-questions point back at the parent scenario question
            $table->foreignId('parent_id')->nullable()->constrained('exam_questions')->onDelete('cascade');
            // Plain string (not enum) so new question types can be added later without a migration
            $table->string('type');
            // Faculty-entered topic label, used to tally the live TOS breakdown
            $table->string('topic')->nullable();
            // remembering, understanding, applying, analyzing, evaluating, creating — nullable until the
            // auto-fill classification logic is confirmed with the client
            $table->string('bloom_level')->nullable();
            $table->text('question_text');
            $table->unsignedInteger('points')->default(1);
            // Flexible per-type payload: MCQ choices + correct index, T/F answer, case analysis scenario
            // text, essay rubric criteria, etc. Keeps the schema extensible for future question types.
            $table->json('options')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_questions');
    }
};
