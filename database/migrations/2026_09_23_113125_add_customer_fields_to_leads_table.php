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
        Schema::table('leads', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id')->nullable()->change();
            if (!Schema::hasColumn('leads', 'customer_name')) {
                $table->string('customer_name')->nullable()->after('customer_id');
            }
            if (!Schema::hasColumn('leads', 'customer_type')) {
                $table->string('customer_type')->default('user')->after('customer_name');
            }
            if (!Schema::hasColumn('leads', 'mobile')) {
                $table->string('mobile', 20)->nullable()->after('customer_type');
            }
            if (!Schema::hasColumn('leads', 'email')) {
                $table->string('email')->nullable()->after('mobile');
            }
        });

        // Backfill existing leads with their customer data
        $leads = \DB::table('leads')->whereNotNull('customer_id')->get();
        foreach ($leads as $l) {
            $cust = \DB::table('customers')->where('customer_id', $l->customer_id)->first();
            if ($cust) {
                \DB::table('leads')->where('lead_id', $l->lead_id)->update([
                    'customer_name' => $cust->name ?? null,
                    'customer_type' => $cust->customer_type ?? 'user',
                    'mobile'        => $cust->mobile ?? null,
                    'email'         => $cust->email ?? null,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['customer_name', 'customer_type', 'mobile', 'email']);
        });
    }
};

