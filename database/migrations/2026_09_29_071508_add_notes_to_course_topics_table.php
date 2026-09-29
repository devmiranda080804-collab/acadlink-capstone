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
        Schema::table('course_topics', function (Blueprint $table) {
            // What the faculty will actually teach for this topic — the grounding
            // content AI question generation reads from, so drafts aren't hallucinated.
            $table->text('notes')->nullable()->after('weeks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_topics', function (Blueprint $table) {
            $table->dropColumn('notes');
        });
    }
};
