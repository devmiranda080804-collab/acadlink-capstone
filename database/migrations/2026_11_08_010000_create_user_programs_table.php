<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Faculty can teach in more than one program. users.program keeps the first one for the
    // screens that show a single program; this table holds all of them.
    public function up(): void
    {
        Schema::create('user_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('program', 20);
            $table->timestamps();
            $table->unique(['user_id', 'program']);
            $table->index('program');
        });

        DB::table('users')->whereNotNull('program')->where('program', '!=', '')->orderBy('id')
            ->each(function ($user) {
                DB::table('user_programs')->insert([
                    'user_id'    => $user->id,
                    'program'    => $user->program,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_programs');
    }
};
