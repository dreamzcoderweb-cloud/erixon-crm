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
            $table->string('gender', 20)->nullable()->after('address');
            $table->date('date_of_birth')->nullable()->after('gender');
            $table->date('date_of_joining')->nullable()->after('date_of_birth');
            $table->string('designation', 100)->nullable()->after('date_of_joining');
            $table->decimal('base_salary', 12, 2)->default(0.00)->after('designation');
            $table->decimal('available_leave_count', 5, 2)->default(0.00)->after('base_salary');
            $table->time('check_in_time')->nullable()->after('available_leave_count');
            $table->time('check_out_time')->nullable()->after('check_in_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'gender',
                'date_of_birth',
                'date_of_joining',
                'designation',
                'base_salary',
                'available_leave_count',
                'check_in_time',
                'check_out_time',
            ]);
        });
    }
};
