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
        Schema::create('template_document_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_document_id')->constrained()->onDelete('cascade');
            $table->string('program'); // BSA, BSMA, BSOA
            // Set independently by each program's own Program Head — one program
            // being distributed doesn't affect the others
            $table->foreignId('distributed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('distributed_at')->nullable();
            $table->timestamps();
            $table->unique(['template_document_id', 'program']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_document_programs');
    }
};
