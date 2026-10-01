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
        Schema::create('tos_topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tos_id')->constrained('tos')->onDelete('cascade');
            // Kept even if the source topic is later edited/deleted — this row is a snapshot.
            $table->foreignId('course_topic_id')->nullable()->constrained()->onDelete('set null');
            $table->string('topic');
            $table->unsignedInteger('hours')->default(0);
            $table->decimal('weight_percent', 5, 1)->default(0);
            $table->unsignedInteger('target_items')->default(0);
            $table->unsignedInteger('topic_points')->default(0);
            // The 6 Bloom's-level cells for this topic: count/range/points/points_per_item each.
            $table->json('levels');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tos_topics');
    }
};
