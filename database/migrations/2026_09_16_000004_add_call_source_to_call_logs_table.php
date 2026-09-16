<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('call_logs') && !Schema::hasColumn('call_logs', 'call_source')) {
            Schema::table('call_logs', function (Blueprint $table) {
                $table->string('call_source', 50)->nullable()->default('direct')->after('call_type');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('call_logs') && Schema::hasColumn('call_logs', 'call_source')) {
            Schema::table('call_logs', function (Blueprint $table) {
                $table->dropColumn('call_source');
            });
        }
    }
};
