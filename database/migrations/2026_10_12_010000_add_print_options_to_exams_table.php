<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->string('print_font')->nullable()->after('target_items');
            $table->unsignedTinyInteger('print_font_size')->nullable()->after('print_font');
            $table->string('print_paper')->nullable()->after('print_font_size');
        });
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn(['print_font', 'print_font_size', 'print_paper']);
        });
    }
};
