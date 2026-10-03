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
        Schema::table('followups', function (Blueprint $table) {
            if (!Schema::hasColumn('followups', 'customer_id')) {
                $table->unsignedBigInteger('customer_id')->nullable()->after('followups_id');
                $table->foreign('customer_id')->references('customer_id')->on('customers')->onDelete('cascade');
            }
            $table->unsignedBigInteger('lead_id')->nullable()->change();
        });

        // 1. Backfill customer_id for existing followups from their lead
        try {
            DB::statement("UPDATE followups f INNER JOIN leads l ON f.lead_id = l.lead_id SET f.customer_id = l.customer_id WHERE f.customer_id IS NULL AND l.customer_id IS NOT NULL");
        } catch (\Exception $e) {
            // ignore if any error during update
        }

        // 2. Backfill existing customers who have followup_date in custom_fields
        try {
            $customers = \App\Models\Customer::all();
            foreach ($customers as $c) {
                $fDate = $c->custom_fields['followup_date'] ?? null;
                if (!empty($fDate)) {
                    try {
                        $parsed = \Carbon\Carbon::parse($fDate)->format('Y-m-d H:i:s');
                        $exists = \App\Models\Followup::where('customer_id', $c->customer_id)
                            ->where('followup_status', 'Pending')
                            ->exists();
                        if (!$exists) {
                            \DB::table('followups')->insert([
                                'customer_id'        => $c->customer_id,
                                'lead_id'            => $c->latestLead?->lead_id,
                                'followup_type'      => 'Call',
                                'duration'           => '5 minutes',
                                'remarks'            => 'Follow-up for customer ' . $c->name,
                                'next_followup_date' => $parsed,
                                'followup_status'    => 'Pending',
                                'forward_to'         => $c->assign_by ?? $c->owner_by,
                                'created_by'         => $c->created_by,
                                'created_at'         => now(),
                                'updated_at'         => now(),
                            ]);
                        }
                    } catch (\Exception $ex) {
                        // skip invalid dates
                    }
                }
            }
        } catch (\Exception $e) {
            // ignore
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('followups', function (Blueprint $table) {
            if (Schema::hasColumn('followups', 'customer_id')) {
                $table->dropForeign(['customer_id']);
                $table->dropColumn('customer_id');
            }
            $table->unsignedBigInteger('lead_id')->nullable(false)->change();
        });
    }
};
