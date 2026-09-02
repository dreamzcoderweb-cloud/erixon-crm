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
        if (Schema::hasTable('credit_requests') && !Schema::hasColumn('credit_requests', 'lead_id')) {
            Schema::table('credit_requests', function (Blueprint $table) {
                $table->unsignedBigInteger('lead_id')->nullable()->after('credit_request_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('credit_requests') && Schema::hasColumn('credit_requests', 'lead_id')) {
            Schema::table('credit_requests', function (Blueprint $table) {
                $table->dropColumn('lead_id');
            });
        }
    }
};
