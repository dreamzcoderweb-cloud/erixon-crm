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
        Schema::table('attendance', function (Blueprint $table) {
            if (!Schema::hasColumn('attendance', 'latitude')) {
                $table->decimal('latitude', 10, 8)->nullable()->after('status');
            }
            if (!Schema::hasColumn('attendance', 'longitude')) {
                $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            }
            if (!Schema::hasColumn('attendance', 'second_check_in_latitude')) {
                $table->decimal('second_check_in_latitude', 10, 8)->nullable()->after('longitude');
            }
            if (!Schema::hasColumn('attendance', 'second_check_in_longitude')) {
                $table->decimal('second_check_in_longitude', 11, 8)->nullable()->after('second_check_in_latitude');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropColumn([
                'latitude',
                'longitude',
                'second_check_in_latitude',
                'second_check_in_longitude',
            ]);
        });
    }
};
