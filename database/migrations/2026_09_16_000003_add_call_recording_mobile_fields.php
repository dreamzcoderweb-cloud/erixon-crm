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
        // Update call_recordings table: make lead_id nullable and add customer_id
        if (Schema::hasTable('call_recordings')) {
            Schema::table('call_recordings', function (Blueprint $table) {
                if (Schema::hasColumn('call_recordings', 'lead_id')) {
                    $table->unsignedBigInteger('lead_id')->nullable()->change();
                }
                if (!Schema::hasColumn('call_recordings', 'customer_id')) {
                    $table->unsignedBigInteger('customer_id')->nullable()->after('lead_id');
                }
            });
        }

        // Update call_logs table: add mobile call log fields
        if (Schema::hasTable('call_logs')) {
            Schema::table('call_logs', function (Blueprint $table) {
                if (!Schema::hasColumn('call_logs', 'customer_id')) {
                    $table->unsignedBigInteger('customer_id')->nullable()->after('lead_id');
                }
                if (!Schema::hasColumn('call_logs', 'customer_code')) {
                    $table->string('customer_code', 50)->nullable()->after('customer_id');
                }
                if (!Schema::hasColumn('call_logs', 'customer_name')) {
                    $table->string('customer_name', 255)->nullable()->after('customer_code');
                }
                if (!Schema::hasColumn('call_logs', 'followup_id')) {
                    $table->unsignedBigInteger('followup_id')->nullable()->after('customer_name');
                }
                if (!Schema::hasColumn('call_logs', 'followup_code')) {
                    $table->string('followup_code', 50)->nullable()->after('followup_id');
                }
                if (!Schema::hasColumn('call_logs', 'followup_date')) {
                    $table->date('followup_date')->nullable()->after('followup_code');
                }
                if (!Schema::hasColumn('call_logs', 'call_start_time')) {
                    $table->dateTime('call_start_time')->nullable()->after('duration');
                }
                if (!Schema::hasColumn('call_logs', 'call_end_time')) {
                    $table->dateTime('call_end_time')->nullable()->after('call_start_time');
                }
                if (!Schema::hasColumn('call_logs', 'notes')) {
                    $table->text('notes')->nullable()->after('call_status');
                }
                if (!Schema::hasColumn('call_logs', 'recording_file')) {
                    $table->string('recording_file', 255)->nullable()->after('recording_id');
                }
                if (Schema::hasColumn('call_logs', 'call_type')) {
                    $table->string('call_type', 50)->default('Outbound')->nullable()->change();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('call_logs')) {
            Schema::table('call_logs', function (Blueprint $table) {
                $columns = [
                    'customer_id',
                    'customer_code',
                    'customer_name',
                    'followup_id',
                    'followup_code',
                    'followup_date',
                    'call_start_time',
                    'call_end_time',
                    'notes',
                    'recording_file',
                ];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('call_logs', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::hasTable('call_recordings')) {
            Schema::table('call_recordings', function (Blueprint $table) {
                if (Schema::hasColumn('call_recordings', 'customer_id')) {
                    $table->dropColumn('customer_id');
                }
            });
        }
    }
};
