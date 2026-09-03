<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('credit_requests')) {
            // Drop wrong foreign key constraint from MySQL database
            try {
                DB::statement("ALTER TABLE credit_requests DROP FOREIGN KEY credit_requests_lead_id_foreign");
            } catch (\Throwable $e) {
                // Ignore if constraint does not exist
            }

            try {
                DB::statement("ALTER TABLE credit_requests DROP FOREIGN KEY credit_requests_lead_source_id_foreign");
            } catch (\Throwable $e) {
                // Ignore
            }

            // Re-add correct foreign keys
            Schema::table('credit_requests', function (Blueprint $table) {
                if (Schema::hasColumn('credit_requests', 'lead_id') && Schema::hasTable('leads')) {
                    $table->foreign('lead_id', 'credit_requests_lead_id_foreign')
                          ->references('lead_id')
                          ->on('leads')
                          ->onDelete('set null');
                }

                if (Schema::hasColumn('credit_requests', 'lead_source_id') && Schema::hasTable('lead_sources')) {
                    $table->foreign('lead_source_id', 'credit_requests_lead_source_id_foreign')
                          ->references('lead_sources_id')
                          ->on('lead_sources')
                          ->onDelete('set null');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('credit_requests')) {
            try {
                DB::statement("ALTER TABLE credit_requests DROP FOREIGN KEY credit_requests_lead_id_foreign");
                DB::statement("ALTER TABLE credit_requests DROP FOREIGN KEY credit_requests_lead_source_id_foreign");
            } catch (\Throwable $e) {
                // Ignore
            }
        }
    }
};
