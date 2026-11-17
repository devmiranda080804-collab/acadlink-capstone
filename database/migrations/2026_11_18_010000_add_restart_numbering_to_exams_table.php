<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Exam Builder: numbers run on across the tests (Test II starts at 31) unless the
// instructor ticks "Start each test at number 1".
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            if (!Schema::hasColumn('exams', 'restart_numbering')) {
                $table->boolean('restart_numbering')->default(false)->after('target_items');
            }
        });
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            if (Schema::hasColumn('exams', 'restart_numbering')) {
                $table->dropColumn('restart_numbering');
            }
        });
    }
};
