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
        Schema::table('credit_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('credit_requests', 'lead_requirement_id')) {
                $table->unsignedBigInteger('lead_requirement_id')->nullable()->after('lead_source_id');
                $table->foreign('lead_requirement_id')->references('lead_requirements_id')->on('lead_requirements')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('credit_requests', function (Blueprint $table) {
            if (Schema::hasColumn('credit_requests', 'lead_requirement_id')) {
                $table->dropForeign(['lead_requirement_id']);
                $table->dropColumn('lead_requirement_id');
            }
        });
    }
};
