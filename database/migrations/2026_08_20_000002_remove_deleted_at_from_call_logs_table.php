<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('call_logs', 'deleted_at')) {
            Schema::table('call_logs', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('call_logs', 'deleted_at')) {
            Schema::table('call_logs', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }
};
