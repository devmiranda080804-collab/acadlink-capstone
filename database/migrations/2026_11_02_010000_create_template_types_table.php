<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('template_types', function (Blueprint $table) {
            $table->id();
            $table->string('key', 80)->unique();
            $table->string('label', 60);
            $table->string('icon', 16)->default('📄');
            $table->text('description')->nullable(); // what the upload content check looks for
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // The types that used to be fixed in the code, so existing templates keep their folders.
        $now = now();
        $types = [
            ['syllabus', 'Syllabus', '📘', 'an official course Syllabus — normally includes a course description, course outcomes, grading system, and a course outline/schedule of topics'],
            ['course_guide', 'Course Guide', '📙', 'a Course Guide — normally includes course objectives, an overview of the subject matter, required readings/materials, and how the course is structured across the term'],
            ['module', 'Module', '📝', 'an instructional Module / Learning Module — normally contains lesson content meant to teach students: explanations, examples, and activities or exercises for a specific topic'],
            ['research_template', 'Research Template', '🔬', 'a Research Template — a standardized outline/format for a research or capstone-style paper (e.g. sections for title, background, methodology, references)'],
            ['memorandum', 'Memorandum', '📰', 'an official Memorandum — a formal internal communication with a header (To/From/Date/Subject) and a body conveying an announcement, directive, or information'],
            ['request_letter', 'Request Letter', '✉️', 'a formal Request Letter — addressed to a specific recipient, stating a clear request/purpose and closing with a signature'],
            ['consultation_form', 'Consultation Form', '🗒️', 'a Consultation Form — a structured form for recording a consultation session (names, date, concern/topic discussed, notes or action items)'],
            ['activity_proposal', 'Activity Proposal', '📋', 'an Activity Proposal — proposes an event or activity, normally including objectives, schedule, and budget/resources'],
            ['monitoring_sheet', 'Monitoring Sheet', '📊', 'a Monitoring Sheet — a tabular form for tracking the status or progress of tasks, submissions, or activities over time'],
        ];

        DB::table('template_types')->insert(array_map(fn ($type) => [
            'key'         => $type[0],
            'label'       => $type[1],
            'icon'        => $type[2],
            'description' => $type[3],
            'is_active'   => true,
            'created_at'  => $now,
            'updated_at'  => $now,
        ], $types));
    }

    public function down(): void
    {
        Schema::dropIfExists('template_types');
    }
};
