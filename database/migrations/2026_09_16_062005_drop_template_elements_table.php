<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('template_elements');
    }

    public function down(): void
    {
        Schema::create('template_elements', function (Blueprint $table) {
            $table->id();
            $table->string('template_type');
            $table->string('label');
            $table->text('instructions')->nullable();
            $table->string('field_type')->default('rich_text');
            $table->boolean('is_required')->default(true);
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
};
