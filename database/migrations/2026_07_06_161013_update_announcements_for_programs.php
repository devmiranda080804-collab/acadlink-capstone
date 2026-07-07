<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tanggalin ang lumang 'tag' column, at ang 'visibility'/'program' na single
        Schema::table('announcements', function (Blueprint $table) {
            if (Schema::hasColumn('announcements', 'tag')) {
                $table->dropColumn('tag');
            }
            if (Schema::hasColumn('announcements', 'visibility')) {
                $table->dropColumn('visibility');
            }
            if (Schema::hasColumn('announcements', 'program')) {
                $table->dropColumn('program');
            }
        });

        // Bagong pivot table: isang announcement → maraming program
        Schema::create('announcement_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('announcement_id')->constrained()->onDelete('cascade');
            $table->string('program'); // FMAD, OFD, BAD
            $table->timestamps();
            $table->unique(['announcement_id', 'program']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcement_programs');

        Schema::table('announcements', function (Blueprint $table) {
            $table->string('tag')->nullable();
            $table->string('visibility')->default('all');
            $table->string('program')->nullable();
        });
    }
};