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
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'lead_requirement_id')) {
                $table->unsignedBigInteger('lead_requirement_id')->nullable()->after('assign_by');
                $table->foreign('lead_requirement_id')->references('lead_requirements_id')->on('lead_requirements')->onDelete('set null');
            }
            if (!Schema::hasColumn('customers', 'lead_stage_id')) {
                $table->unsignedBigInteger('lead_stage_id')->nullable()->after('lead_requirement_id');
                $table->foreign('lead_stage_id')->references('lead_stage_id')->on('lead_stages')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasColumn('customers', 'lead_stage_id')) {
                $table->dropForeign(['lead_stage_id']);
                $table->dropColumn('lead_stage_id');
            }
            if (Schema::hasColumn('customers', 'lead_requirement_id')) {
                $table->dropForeign(['lead_requirement_id']);
                $table->dropColumn('lead_requirement_id');
            }
        });
    }
};
