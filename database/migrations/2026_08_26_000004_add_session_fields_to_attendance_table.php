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
        \Illuminate\Support\Facades\DB::statement("UPDATE attendance SET created_at = NOW() WHERE CAST(created_at AS CHAR) = '0000-00-00 00:00:00' OR created_at IS NULL");
        \Illuminate\Support\Facades\DB::statement("UPDATE attendance SET updated_at = NOW() WHERE CAST(updated_at AS CHAR) = '0000-00-00 00:00:00' OR updated_at IS NULL");

        Schema::table('attendance', function (Blueprint $table) {
            if (!Schema::hasColumn('attendance', 'permission_start')) {
                $table->time('permission_start')->nullable()->after('status');
            }
            if (!Schema::hasColumn('attendance', 'permission_end')) {
                $table->time('permission_end')->nullable()->after('permission_start');
            }
            if (!Schema::hasColumn('attendance', 'second_check_in')) {
                $table->time('second_check_in')->nullable()->after('permission_end');
            }
            if (!Schema::hasColumn('attendance', 'second_check_out')) {
                $table->time('second_check_out')->nullable()->after('second_check_in');
            }
            if (!Schema::hasColumn('attendance', 'permission_id')) {
                $table->unsignedBigInteger('permission_id')->nullable()->after('second_check_out');
                $table->foreign('permission_id')->references('id')->on('permission_requests')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropForeign(['permission_id']);
            $table->dropColumn([
                'permission_start',
                'permission_end',
                'second_check_in',
                'second_check_out',
                'permission_id',
            ]);
        });
    }
};
