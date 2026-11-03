<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Earlier copies of a file edited in the document editor, so a change can be undone.
        Schema::create('file_versions', function (Blueprint $table) {
            $table->id();
            $table->morphs('versionable');
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type', 10);
            $table->unsignedBigInteger('file_size');
            $table->foreignId('edited_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('saved_at')->nullable(); // when this content was made
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('file_versions');
    }
};
