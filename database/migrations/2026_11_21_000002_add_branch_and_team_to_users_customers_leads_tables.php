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
        // 1. Add branch_id and team_id to users
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'branch_id')) {
                $table->unsignedBigInteger('branch_id')->nullable()->after('status');
            }
            if (!Schema::hasColumn('users', 'team_id')) {
                $table->unsignedBigInteger('team_id')->nullable()->after('branch_id');
            }
        });

        // 2. Add branch_id and team_id to customers
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'branch_id')) {
                $table->unsignedBigInteger('branch_id')->nullable()->after('customer_type');
            }
            if (!Schema::hasColumn('customers', 'team_id')) {
                $table->unsignedBigInteger('team_id')->nullable()->after('branch_id');
            }
        });

        // 3. Add branch_id and team_id to leads
        Schema::table('leads', function (Blueprint $table) {
            if (!Schema::hasColumn('leads', 'branch_id')) {
                $table->unsignedBigInteger('branch_id')->nullable()->after('customer_id');
            }
            if (!Schema::hasColumn('leads', 'team_id')) {
                $table->unsignedBigInteger('team_id')->nullable()->after('branch_id');
            }
        });

        // 4. Data Migration: Seed default Main Branch and Main Sales Team if not present
        $now = now();
        $defaultBranch = DB::table('branches')->where('code', 'BR-MAIN')->first();
        if (!$defaultBranch) {
            $branchId = DB::table('branches')->insertGetId([
                'name'       => 'Main Branch',
                'code'       => 'BR-MAIN',
                'phone'      => '9876543210',
                'email'      => 'info@erixon.com',
                'city'       => 'Head Office',
                'status'     => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        } else {
            $branchId = $defaultBranch->id;
        }

        $defaultTeam = DB::table('teams')->where('code', 'TM-MAIN')->first();
        if (!$defaultTeam) {
            $teamId = DB::table('teams')->insertGetId([
                'branch_id'   => $branchId,
                'name'        => 'Main Sales Team',
                'code'        => 'TM-MAIN',
                'description' => 'Default system sales team',
                'status'      => 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
        } else {
            $teamId = $defaultTeam->id;
        }

        // 5. Migrate existing users, customers, and leads without branch/team
        DB::table('users')->whereNull('branch_id')->update([
            'branch_id' => $branchId,
            'team_id'   => $teamId,
        ]);

        DB::table('customers')->whereNull('branch_id')->update([
            'branch_id' => $branchId,
            'team_id'   => $teamId,
        ]);

        DB::table('leads')->whereNull('branch_id')->update([
            'branch_id' => $branchId,
            'team_id'   => $teamId,
        ]);

        // Populate pivot tables for existing users
        $users = DB::table('users')->get(['id', 'branch_id', 'team_id']);
        foreach ($users as $u) {
            if ($u->branch_id) {
                DB::table('branch_user')->updateOrInsert(
                    ['user_id' => $u->id, 'branch_id' => $u->branch_id],
                    ['is_primary' => true, 'created_at' => $now, 'updated_at' => $now]
                );
            }
            if ($u->team_id) {
                DB::table('team_user')->updateOrInsert(
                    ['user_id' => $u->id, 'team_id' => $u->team_id],
                    ['role_in_team' => 'member', 'is_primary' => true, 'created_at' => $now, 'updated_at' => $now]
                );
            }
        }

        // 6. Add foreign key constraints safely
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('branch_id')->references('id')->on('branches')->nullOnDelete();
            $table->foreign('team_id')->references('id')->on('teams')->nullOnDelete();
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->foreign('branch_id')->references('id')->on('branches')->nullOnDelete();
            $table->foreign('team_id')->references('id')->on('teams')->nullOnDelete();
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->foreign('branch_id')->references('id')->on('branches')->nullOnDelete();
            $table->foreign('team_id')->references('id')->on('teams')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropForeign(['team_id']);
            $table->dropColumn(['branch_id', 'team_id']);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropForeign(['team_id']);
            $table->dropColumn(['branch_id', 'team_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropForeign(['team_id']);
            $table->dropColumn(['branch_id', 'team_id']);
        });
    }
};
