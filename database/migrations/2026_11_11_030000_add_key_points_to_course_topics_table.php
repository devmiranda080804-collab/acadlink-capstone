<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Key points of a topic, one per line. The AI suggests them from the uploaded
// module and the instructor reviews them before saving.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_topics', function (Blueprint $table) {
            $table->text('key_points')->nullable()->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('course_topics', function (Blueprint $table) {
            $table->dropColumn('key_points');
        });
    }
};
