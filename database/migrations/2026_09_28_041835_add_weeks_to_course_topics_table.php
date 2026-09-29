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
            // Matches the client's actual OBTL format (e.g. "Week 1-2") — display/reference
            // only, doesn't affect the TOS Generator's math which is driven by `hours`.
            $table->string('weeks')->nullable()->after('topic');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_topics', function (Blueprint $table) {
            $table->dropColumn('weeks');
        });
    }
};
