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
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'owner_by')) {
                $table->unsignedBigInteger('owner_by')->nullable()->after('created_by');
                $table->foreign('owner_by')->references('id')->on('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('customers', 'assign_by')) {
                $table->unsignedBigInteger('assign_by')->nullable()->after('owner_by');
                $table->foreign('assign_by')->references('id')->on('users')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasColumn('customers', 'assign_by')) {
                $table->dropForeign(['assign_by']);
                $table->dropColumn('assign_by');
            }
            if (Schema::hasColumn('customers', 'owner_by')) {
                $table->dropForeign(['owner_by']);
                $table->dropColumn('owner_by');
            }
        });
    }
};
