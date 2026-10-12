<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Hides a question from the Item Bank without touching the exam it belongs to.
    public function up(): void
    {
        Schema::table('exam_questions', function (Blueprint $table) {
            $table->timestamp('bank_archived_at')->nullable()->after('order');
        });
    }

    public function down(): void
    {
        Schema::table('exam_questions', function (Blueprint $table) {
            $table->dropColumn('bank_archived_at');
        });
    }
};
