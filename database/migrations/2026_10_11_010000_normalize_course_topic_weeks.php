<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// Topic weeks become numbers only ("1-2" instead of "Week 1-2"). Values with no
// numbers are left as they are; the faculty is asked for numbers on their next edit.
return new class extends Migration
{
    public function up(): void
    {
        DB::table('course_topics')->whereNotNull('weeks')->orderBy('id')->each(function ($topic) {
            if (preg_match('/(\d+)\s*(?:-|–|to)\s*(\d+)/i', $topic->weeks, $m)) {
                $value = (int) $m[1] === (int) $m[2] ? (string) (int) $m[1] : (int) $m[1] . '-' . (int) $m[2];
            } elseif (preg_match('/(\d+)/', $topic->weeks, $m)) {
                $value = (string) (int) $m[1];
            } else {
                return;
            }

            DB::table('course_topics')->where('id', $topic->id)->update(['weeks' => $value]);
        });
    }

    public function down(): void
    {
        // Numeric weeks still read correctly; nothing to restore.
    }
};
