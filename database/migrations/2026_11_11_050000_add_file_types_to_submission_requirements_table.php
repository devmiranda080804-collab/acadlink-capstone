<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// The file types the Program Head accepts for a requirement, e.g. ["pdf", "word"].
// Empty means the default (PDF or Word), which is what every older requirement accepted.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submission_requirements', function (Blueprint $table) {
            $table->json('file_types')->nullable()->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('submission_requirements', function (Blueprint $table) {
            $table->dropColumn('file_types');
        });
    }
};
