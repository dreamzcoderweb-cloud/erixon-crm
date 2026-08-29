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
        if (Schema::hasTable('customers') && !Schema::hasColumn('customers', 'custom_fields')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->json('custom_fields')->nullable()->after('reference_code');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('customers') && Schema::hasColumn('customers', 'custom_fields')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->dropColumn('custom_fields');
            });
        }
    }
};
