<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('template_elements', function (Blueprint $table) {
            $table->id();
            // Free-text label, not tied to templates.type's enum — lets Admin define
            // structure for new template categories without a schema change
            $table->string('template_type');
            $table->string('label');
            $table->text('instructions')->nullable();
            $table->string('field_type')->default('rich_text'); // text, rich_text, table
            $table->boolean('is_required')->default(true);
            $table->unsignedInteger('order')->default(0);
            // Soft toggle instead of delete, so past guidance stays intact if reused later
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_elements');
    }
};
