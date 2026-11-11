<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Questions taken out of an exam without deleting them. They don't count toward
// the TOS or print, and can be restored into any test of the same exam.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->json('archived_questions')->nullable()->after('target_items');
        });
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn('archived_questions');
        });
    }
};
