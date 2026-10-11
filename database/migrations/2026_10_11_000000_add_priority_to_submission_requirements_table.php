<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submission_requirements', function (Blueprint $table) {
            $table->string('priority')->default('standard')->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('submission_requirements', function (Blueprint $table) {
            $table->dropColumn('priority');
        });
    }
};
