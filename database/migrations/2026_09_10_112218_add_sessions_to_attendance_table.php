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
        if (Schema::hasTable('attendance') && !Schema::hasColumn('attendance', 'sessions')) {
            Schema::table('attendance', function (Blueprint $table) {
                $table->longText('sessions')->nullable()->after('second_check_out');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('attendance') && Schema::hasColumn('attendance', 'sessions')) {
            Schema::table('attendance', function (Blueprint $table) {
                $table->dropColumn('sessions');
            });
        }
    }
};
