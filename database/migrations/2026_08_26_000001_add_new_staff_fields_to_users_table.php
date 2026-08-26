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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'staff_type')) {
                $table->string('staff_type', 50)->default('Temporary')->nullable()->after('designation');
            }
            if (!Schema::hasColumn('users', 'allow_check_in_time')) {
                $table->time('allow_check_in_time')->nullable()->after('check_in_time');
            }
            if (!Schema::hasColumn('users', 'late_attendance_count')) {
                $table->integer('late_attendance_count')->default(0)->nullable()->after('allow_check_in_time');
            }
            if (!Schema::hasColumn('users', 'increment_amount')) {
                $table->decimal('increment_amount', 12, 2)->default(0.00)->nullable()->after('late_attendance_count');
            }
            if (!Schema::hasColumn('users', 'increment_date')) {
                $table->date('increment_date')->nullable()->after('increment_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columnsToDrop = [];
            foreach (['staff_type', 'allow_check_in_time', 'late_attendance_count', 'increment_amount', 'increment_date'] as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $columnsToDrop[] = $col;
                }
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
