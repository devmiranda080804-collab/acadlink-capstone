<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// New template workflow: the Secretary uploads and submits to the Dean, the Dean
// approves (or returns with a reason) and sends to programs. Sending reuses
// template_document_programs.distributed_at, so faculty-side queries are unchanged.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('template_documents', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('file_size');
            $table->foreignId('reviewed_by')->nullable()->after('status')->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            $table->text('return_reason')->nullable()->after('reviewed_at');
        });

        // Everything uploaded under the old flow came from the Dean, so it is
        // already approved; keep its forward date as the review date.
        DB::table('template_documents')->update([
            'status'      => 'approved',
            'reviewed_at' => DB::raw('COALESCE(forwarded_at, created_at)'),
        ]);
    }

    public function down(): void
    {
        Schema::table('template_documents', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reviewed_by');
            $table->dropColumn(['status', 'reviewed_at', 'return_reason']);
        });
    }
};
