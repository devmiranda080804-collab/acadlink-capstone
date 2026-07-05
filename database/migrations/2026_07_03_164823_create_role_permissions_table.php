<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->enum('role', ['admin', 'program_head', 'secretary', 'faculty']);
            $table->string('module');
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
            $table->unique(['role', 'module']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
    }
};