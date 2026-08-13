<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'customer_type')) {
                $table->enum('customer_type', ['user', 'reseller'])->default('user')->after('customer_id');
            }
            if (!Schema::hasColumn('customers', 'company_name')) {
                $table->string('company_name')->nullable()->after('name');
            }
            if (!Schema::hasColumn('customers', 'email')) {
                $table->string('email')->nullable()->after('mobile');
            }
            if (!Schema::hasColumn('customers', 'alternate_mobile')) {
                $table->string('alternate_mobile', 20)->nullable()->after('email');
            }
            if (!Schema::hasColumn('customers', 'address')) {
                $table->text('address')->nullable()->after('alternate_mobile');
            }
            if (!Schema::hasColumn('customers', 'city')) {
                $table->string('city', 100)->nullable()->after('address');
            }
            if (!Schema::hasColumn('customers', 'state')) {
                $table->string('state', 100)->nullable()->after('city');
            }
            if (!Schema::hasColumn('customers', 'country')) {
                $table->string('country', 100)->nullable()->after('state');
            }
            if (!Schema::hasColumn('customers', 'pincode')) {
                $table->string('pincode', 20)->nullable()->after('country');
            }
            if (!Schema::hasColumn('customers', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('pincode');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('customers', 'status')) {
                $table->tinyInteger('status')->default(1)->comment('1: Active, 0: Inactive')->after('created_by');
            }
            if (Schema::hasColumn('customers', 'password')) {
                $table->string('password')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasColumn('customers', 'created_by')) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            }
            $table->dropColumn([
                'customer_type',
                'company_name',
                'email',
                'alternate_mobile',
                'address',
                'city',
                'state',
                'country',
                'pincode',
                'status'
            ]);
        });
    }
};
