<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            if (! Schema::hasColumn('templates', 'distributed_at')) {
                $table->timestamp('distributed_at')->nullable();
            }
            if (! Schema::hasColumn('templates', 'distributed_by')) {
                $table->foreignId('distributed_by')->nullable()->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            if (Schema::hasColumn('templates', 'distributed_by')) {
                $table->dropConstrainedForeignId('distributed_by');
            }
            if (Schema::hasColumn('templates', 'distributed_at')) {
                $table->dropColumn('distributed_at');
            }
        });
    }
};