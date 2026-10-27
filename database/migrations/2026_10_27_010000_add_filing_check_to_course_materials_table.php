<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Course Filing: the Secretary marks each course file "Checked and Validated", or returns
// it to the uploader with a reason. No status means it's still waiting to be checked.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_materials', function (Blueprint $table) {
            $table->string('filing_status')->nullable()->after('file_size'); // validated, returned
            $table->foreignId('filing_checked_by')->nullable()->after('filing_status')->constrained('users')->nullOnDelete();
            $table->timestamp('filing_checked_at')->nullable()->after('filing_checked_by');
            $table->text('filing_return_reason')->nullable()->after('filing_checked_at');
        });
    }

    public function down(): void
    {
        Schema::table('course_materials', function (Blueprint $table) {
            $table->dropConstrainedForeignId('filing_checked_by');
            $table->dropColumn(['filing_status', 'filing_checked_at', 'filing_return_reason']);
        });
    }
};
