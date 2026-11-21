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
        // 1. Branches Table
        if (!Schema::hasTable('branches')) {
            Schema::create('branches', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code', 50)->unique();
                $table->unsignedBigInteger('manager_id')->nullable();
                $table->string('phone', 30)->nullable();
                $table->string('email', 191)->nullable();
                $table->text('address')->nullable();
                $table->string('city', 100)->nullable();
                $table->string('state', 100)->nullable();
                $table->string('pincode', 20)->nullable();
                $table->tinyInteger('status')->default(1)->comment('1: Active, 0: Inactive');
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 2. Teams Table
        if (!Schema::hasTable('teams')) {
            Schema::create('teams', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('branch_id');
                $table->string('name');
                $table->string('code', 50)->unique();
                $table->unsignedBigInteger('leader_id')->nullable();
                $table->text('description')->nullable();
                $table->tinyInteger('status')->default(1)->comment('1: Active, 0: Inactive');
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            });
        }

        // 3. Pivot: branch_user (For future multi-branch assignment)
        if (!Schema::hasTable('branch_user')) {
            Schema::create('branch_user', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('branch_id');
                $table->boolean('is_primary')->default(false);
                $table->timestamps();

                $table->unique(['user_id', 'branch_id']);
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            });
        }

        // 4. Pivot: team_user (For future multi-team assignment)
        if (!Schema::hasTable('team_user')) {
            Schema::create('team_user', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('team_id');
                $table->string('role_in_team', 50)->default('member')->comment('member, leader');
                $table->boolean('is_primary')->default(false);
                $table->timestamps();

                $table->unique(['user_id', 'team_id']);
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_user');
        Schema::dropIfExists('branch_user');
        Schema::dropIfExists('teams');
        Schema::dropIfExists('branches');
    }
};
