<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Dropdown lists the Admin manages in the System CMS (programs, academic years, semesters).
// "key" is what records store and never changes; "label" is what people see. Hidden items
// stop showing in new choices but keep working on the records that already use them.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_options', function (Blueprint $table) {
            $table->id();
            $table->string('list', 40);
            $table->string('key', 100);
            $table->string('label');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['list', 'key']);
        });

        $now = now();
        $rows = [];

        // The lists the system had in its code until now.
        $programs = [
            'BSA'      => 'Bachelor of Science in Accountancy',
            'BSMA'     => 'Bachelor of Science in Management Accounting',
            'BSOA'     => 'Bachelor of Science in Office Administration',
            'BSBA-HRM' => 'Bachelor of Science in Business Administration — Major in Human Resource Management',
            'BSBA-FM'  => 'Bachelor of Science in Business Administration — Major in Financial Management',
            'BSBA-MM'  => 'Bachelor of Science in Business Administration — Major in Marketing Management',
        ];
        $order = 0;
        foreach ($programs as $code => $name) {
            $rows[] = ['list' => 'programs', 'key' => $code, 'label' => $name, 'sort_order' => ++$order];
        }

        $order = 0;
        foreach (['First Semester', 'Second Semester', 'Summer'] as $semester) {
            $rows[] = ['list' => 'semesters', 'key' => $semester, 'label' => $semester, 'sort_order' => ++$order];
        }

        // Academic years: this one and the next (what the dropdowns offered), plus every
        // year already saved on a record, so nothing in use goes missing.
        $start = $now->month >= 8 ? $now->year : $now->year - 1;
        $years = [$start . '-' . ($start + 1), ($start + 1) . '-' . ($start + 2)];
        foreach ([['users', 'academic_year'], ['program_assignments', 'school_year'], ['submission_requirements', 'school_year']] as [$table, $column]) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, $column)) {
                $years = array_merge($years, DB::table($table)->whereNotNull($column)->distinct()->pluck($column)->all());
            }
        }
        $years = array_values(array_unique(array_filter($years, fn ($y) => preg_match('/^(\d{4})-(\d{4})$/', (string) $y, $m) && (int) $m[2] === (int) $m[1] + 1)));
        sort($years);
        foreach ($years as $i => $year) {
            $rows[] = ['list' => 'school_years', 'key' => $year, 'label' => $year, 'sort_order' => $i + 1];
        }

        DB::table('cms_options')->insert(array_map(fn ($row) => $row + ['is_active' => true, 'created_at' => $now, 'updated_at' => $now], $rows));
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_options');
    }
};
