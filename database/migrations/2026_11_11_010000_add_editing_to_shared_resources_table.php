<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Faculty who teach the same course edit a shared resource together in the editor.
    public function up(): void
    {
        Schema::table('shared_resources', function (Blueprint $table) {
            $table->string('document_key', 128)->nullable()->after('file_size');
            $table->foreignId('last_edited_by')->nullable()->after('document_key')->constrained('users')->nullOnDelete();
            $table->timestamp('last_edited_at')->nullable()->after('last_edited_by');
        });
    }

    public function down(): void
    {
        Schema::table('shared_resources', function (Blueprint $table) {
            $table->dropConstrainedForeignId('last_edited_by');
            $table->dropColumn(['document_key', 'last_edited_at']);
        });
    }
};
