<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// The academic year and semester a template, shared resource or course material was
// uploaded in, so the document pages can be filtered by term. Older rows get the term
// their upload date falls in (August–December first semester, January–May second, June–July summer).
return new class extends Migration
{
    const TABLES = ['template_documents', 'shared_resources', 'course_materials'];

    public function up(): void
    {
        foreach (self::TABLES as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->string('school_year', 9)->nullable()->after('id');
                $t->string('semester', 30)->nullable()->after('school_year');
                $t->index(['school_year', 'semester']);
            });

            DB::table($table)->whereNull('school_year')->orderBy('id')->select(['id', 'created_at'])->chunkById(500, function ($rows) use ($table) {
                foreach ($rows as $row) {
                    $date = $row->created_at ? Carbon::parse($row->created_at) : now();
                    $start = $date->month >= 8 ? $date->year : $date->year - 1;
                    $semester = $date->month >= 8 ? 'First Semester' : ($date->month <= 5 ? 'Second Semester' : 'Summer');
                    DB::table($table)->where('id', $row->id)->update(['school_year' => $start . '-' . ($start + 1), 'semester' => $semester]);
                }
            });
        }
    }

    public function down(): void
    {
        foreach (self::TABLES as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropIndex(['school_year', 'semester']);
                $t->dropColumn(['school_year', 'semester']);
            });
        }
    }
};
