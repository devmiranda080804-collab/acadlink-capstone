<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_presence', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('collaborative_documents')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('last_seen_at');
            $table->unique(['document_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_presence');
    }
};