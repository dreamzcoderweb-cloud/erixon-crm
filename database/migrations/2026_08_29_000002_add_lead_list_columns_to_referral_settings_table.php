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
        if (Schema::hasTable('referral_settings') && !Schema::hasColumn('referral_settings', 'lead_list_columns')) {
            Schema::table('referral_settings', function (Blueprint $table) {
                $table->json('lead_list_columns')->nullable()->after('referral_points');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('referral_settings') && Schema::hasColumn('referral_settings', 'lead_list_columns')) {
            Schema::table('referral_settings', function (Blueprint $table) {
                $table->dropColumn('lead_list_columns');
            });
        }
    }
};
