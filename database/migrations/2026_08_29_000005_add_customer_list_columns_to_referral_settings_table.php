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
        if (Schema::hasTable('referral_settings') && !Schema::hasColumn('referral_settings', 'customer_list_columns')) {
            Schema::table('referral_settings', function (Blueprint $table) {
                $table->json('customer_list_columns')->nullable()->after('lead_list_columns');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('referral_settings') && Schema::hasColumn('referral_settings', 'customer_list_columns')) {
            Schema::table('referral_settings', function (Blueprint $table) {
                $table->dropColumn('customer_list_columns');
            });
        }
    }
};
