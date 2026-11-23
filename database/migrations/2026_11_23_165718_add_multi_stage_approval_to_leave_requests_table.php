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
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->string('status', 50)->default('Pending')->change();
            $table->string('approval_stage', 30)->nullable()->default('tl')->after('status');
            $table->unsignedBigInteger('tl_approved_by')->nullable()->after('approved_by');
            $table->timestamp('tl_approved_at')->nullable()->after('tl_approved_by');
            $table->text('tl_remarks')->nullable()->after('tl_approved_at');
            $table->unsignedBigInteger('hr_approved_by')->nullable()->after('tl_remarks');
            $table->timestamp('hr_approved_at')->nullable()->after('hr_approved_by');
            $table->text('hr_remarks')->nullable()->after('hr_approved_at');
            $table->unsignedBigInteger('md_approved_by')->nullable()->after('hr_remarks');
            $table->timestamp('md_approved_at')->nullable()->after('md_approved_by');
            $table->text('md_remarks')->nullable()->after('md_approved_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropColumn([
                'approval_stage',
                'tl_approved_by',
                'tl_approved_at',
                'tl_remarks',
                'hr_approved_by',
                'hr_approved_at',
                'hr_remarks',
                'md_approved_by',
                'md_approved_at',
                'md_remarks',
            ]);
        });
    }
};
