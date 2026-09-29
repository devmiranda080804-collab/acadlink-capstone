<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
        });

        // One-time best-effort backfill for accounts created before this column
        // split existed — first word becomes first_name, the rest becomes
        // last_name. Anyone whose real first name has a space in it may need a
        // one-time manual correction after this; going forward the bug this
        // migration fixes (guessing the split from a single combined string)
        // can't happen again since the columns are now stored separately.
        DB::table('users')->orderBy('id')->chunkById(100, function ($users) {
            foreach ($users as $user) {
                $trimmed = trim($user->name);
                $spacePos = strpos($trimmed, ' ');

                DB::table('users')->where('id', $user->id)->update([
                    'first_name' => $spacePos === false ? $trimmed : substr($trimmed, 0, $spacePos),
                    'last_name'  => $spacePos === false ? '' : substr($trimmed, $spacePos + 1),
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name']);
        });
    }
};
