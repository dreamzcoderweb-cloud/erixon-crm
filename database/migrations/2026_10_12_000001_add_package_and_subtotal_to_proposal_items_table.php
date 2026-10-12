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
        Schema::table('proposal_items', function (Blueprint $table) {
            if (!Schema::hasColumn('proposal_items', 'package')) {
                $table->decimal('package', 16, 2)->default(0.00)->after('proposal_id');
            }
            if (!Schema::hasColumn('proposal_items', 'subtotal')) {
                $table->decimal('subtotal', 14, 2)->default(0.00)->after('price');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposal_items', function (Blueprint $table) {
            if (Schema::hasColumn('proposal_items', 'subtotal')) {
                $table->dropColumn('subtotal');
            }
            if (Schema::hasColumn('proposal_items', 'package')) {
                $table->dropColumn('package');
            }
        });
    }
};
