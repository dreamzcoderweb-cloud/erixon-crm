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
        Schema::table('proposals', function (Blueprint $table) {
            if (!Schema::hasColumn('proposals', 'customer_type')) {
                $table->string('customer_type', 50)->default('user')->nullable()->after('customer_name');
            }
            if (!Schema::hasColumn('proposals', 'customer_email')) {
                $table->string('customer_email', 191)->nullable()->after('customer_mobile');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('proposals', 'customer_type')) {
                $columnsToDrop[] = 'customer_type';
            }
            if (Schema::hasColumn('proposals', 'customer_email')) {
                $columnsToDrop[] = 'customer_email';
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
