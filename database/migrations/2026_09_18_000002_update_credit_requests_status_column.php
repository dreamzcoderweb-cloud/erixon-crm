<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('credit_requests')) {
            // Modify status column to VARCHAR(100) to safely allow 'Forwarded to Product Manager'
            DB::statement("ALTER TABLE `credit_requests` MODIFY COLUMN `status` VARCHAR(100) NOT NULL DEFAULT 'Pending Admin Approval'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('credit_requests')) {
            DB::statement("ALTER TABLE `credit_requests` MODIFY COLUMN `status` ENUM('Pending Admin Approval','Approved by Admin','Forwarded to Support','Credit Added','Rejected') NOT NULL DEFAULT 'Pending Admin Approval'");
        }
    }
};
