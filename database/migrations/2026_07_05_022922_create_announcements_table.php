<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('body');
            $table->enum('tag', ['general', 'priority', 'faculty', 'urgent'])->default('general');
            // 'all' = visible sa lahat, 'program' = visible sa specific program lang
            $table->enum('visibility', ['all', 'program'])->default('all');
            $table->string('program')->nullable(); // para sa PH posts
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};