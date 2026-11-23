<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Pivot: branch_customer
        if (!Schema::hasTable('branch_customer')) {
            Schema::create('branch_customer', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('customer_id');
                $table->unsignedBigInteger('branch_id');
                $table->boolean('is_primary')->default(false);
                $table->timestamps();

                $table->unique(['customer_id', 'branch_id']);
                $table->foreign('customer_id')->references('customer_id')->on('customers')->onDelete('cascade');
                $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            });
        }

        // 2. Pivot: customer_team
        if (!Schema::hasTable('customer_team')) {
            Schema::create('customer_team', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('customer_id');
                $table->unsignedBigInteger('team_id');
                $table->boolean('is_primary')->default(false);
                $table->timestamps();

                $table->unique(['customer_id', 'team_id']);
                $table->foreign('customer_id')->references('customer_id')->on('customers')->onDelete('cascade');
                $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
            });
        }

        // 3. Pivot: branch_lead
        if (!Schema::hasTable('branch_lead')) {
            Schema::create('branch_lead', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('lead_id');
                $table->unsignedBigInteger('branch_id');
                $table->boolean('is_primary')->default(false);
                $table->timestamps();

                $table->unique(['lead_id', 'branch_id']);
                $table->foreign('lead_id')->references('lead_id')->on('leads')->onDelete('cascade');
                $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            });
        }

        // 4. Pivot: lead_team
        if (!Schema::hasTable('lead_team')) {
            Schema::create('lead_team', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('lead_id');
                $table->unsignedBigInteger('team_id');
                $table->boolean('is_primary')->default(false);
                $table->timestamps();

                $table->unique(['lead_id', 'team_id']);
                $table->foreign('lead_id')->references('lead_id')->on('leads')->onDelete('cascade');
                $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
            });
        }

        // Backfill existing single branch_id & team_id values
        try {
            DB::statement("INSERT IGNORE INTO branch_customer (customer_id, branch_id, is_primary, created_at, updated_at) 
                SELECT customer_id, branch_id, 1, NOW(), NOW() FROM customers WHERE branch_id IS NOT NULL AND branch_id > 0");

            DB::statement("INSERT IGNORE INTO customer_team (customer_id, team_id, is_primary, created_at, updated_at) 
                SELECT customer_id, team_id, 1, NOW(), NOW() FROM customers WHERE team_id IS NOT NULL AND team_id > 0");

            DB::statement("INSERT IGNORE INTO branch_lead (lead_id, branch_id, is_primary, created_at, updated_at) 
                SELECT lead_id, branch_id, 1, NOW(), NOW() FROM leads WHERE branch_id IS NOT NULL AND branch_id > 0");

            DB::statement("INSERT IGNORE INTO lead_team (lead_id, team_id, is_primary, created_at, updated_at) 
                SELECT lead_id, team_id, 1, NOW(), NOW() FROM leads WHERE team_id IS NOT NULL AND team_id > 0");
        } catch (\Exception $e) {
            // Ignore backfill errors if any row violates constraints
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_team');
        Schema::dropIfExists('branch_lead');
        Schema::dropIfExists('customer_team');
        Schema::dropIfExists('branch_customer');
    }
};
