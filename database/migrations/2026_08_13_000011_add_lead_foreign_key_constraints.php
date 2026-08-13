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
        Schema::table('leads', function (Blueprint $table) {
            $table->foreign('lead_stage_id')->references('lead_stage_id')->on('lead_stages')->onDelete('set null');
            $table->foreign('lead_requirement_id')->references('lead_requirements_id')->on('lead_requirements')->onDelete('set null');
            $table->foreign('lost_reason_id')->references('lost_reason_id')->on('lost_reasons')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['lead_stage_id']);
            $table->dropForeign(['lead_requirement_id']);
            $table->dropForeign(['lost_reason_id']);
        });
    }
};
