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
        Schema::table('shared_resources', function (Blueprint $table) {
            // lecture_slides, case_study, activity_guide, assessment_sample —
            // matches the client's "Shared Instructional Materials Library" spec
            $table->string('type')->default('lecture_slides')->after('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shared_resources', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
