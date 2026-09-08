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
        if (Schema::hasTable('demo_processes') && !Schema::hasColumn('demo_processes', 'lead_requirement_id')) {
            Schema::table('demo_processes', function (Blueprint $table) {
                $table->unsignedBigInteger('lead_requirement_id')->nullable()->after('lead_source_id');
                $table->foreign('lead_requirement_id')->references('lead_requirements_id')->on('lead_requirements')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('demo_processes') && Schema::hasColumn('demo_processes', 'lead_requirement_id')) {
            Schema::table('demo_processes', function (Blueprint $table) {
                $table->dropForeign(['lead_requirement_id']);
                $table->dropColumn('lead_requirement_id');
            });
        }
    }
};
