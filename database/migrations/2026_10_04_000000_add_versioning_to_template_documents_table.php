<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('template_documents', function (Blueprint $table) {
            $table->unsignedInteger('version')->default(1)->after('type');
            // Every version of the same template (including the first) shares one
            // root_template_id — set to its own id right after creation. This makes
            // "every version of this template" a single flat query instead of
            // walking a linked list of previous_version pointers.
            $table->foreignId('root_template_id')->nullable()->after('version')
                ->constrained('template_documents')->nullOnDelete();
            // Set when a newer version replaces this row — it stays in the database
            // (version history), it just stops showing up as the "current" one in
            // the normal Admin/Secretary/Program Head/Faculty listings.
            $table->timestamp('superseded_at')->nullable()->after('forwarded_at');
        });
    }

    public function down(): void
    {
        Schema::table('template_documents', function (Blueprint $table) {
            $table->dropConstrainedForeignId('root_template_id');
            $table->dropColumn(['version', 'superseded_at']);
        });
    }
};
