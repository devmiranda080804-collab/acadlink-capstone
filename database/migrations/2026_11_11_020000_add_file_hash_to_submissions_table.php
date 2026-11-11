<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    // A fingerprint of each submitted file, so the same file can't be submitted twice.
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->string('file_hash', 64)->nullable()->after('file_size')->index();
        });

        DB::table('submissions')->orderBy('id')->each(function ($row) {
            if ($row->file_path && Storage::disk('public')->exists($row->file_path)) {
                DB::table('submissions')->where('id', $row->id)->update([
                    'file_hash' => hash_file('sha256', Storage::disk('public')->path($row->file_path)),
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn('file_hash');
        });
    }
};
