<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('content_modules', function (Blueprint $table) {
            // Identifies this version of the file to the document editor; replaced after each save.
            $table->string('document_key', 128)->nullable()->after('file_size');
            $table->foreignId('last_edited_by')->nullable()->after('document_key')->constrained('users')->nullOnDelete();
            $table->timestamp('last_edited_at')->nullable()->after('last_edited_by');
        });

        Schema::create('content_module_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_module_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('can_edit')->default(true);
            $table->timestamps();
            $table->unique(['content_module_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_module_shares');

        Schema::table('content_modules', function (Blueprint $table) {
            $table->dropConstrainedForeignId('last_edited_by');
            $table->dropColumn(['document_key', 'last_edited_at']);
        });
    }
};
