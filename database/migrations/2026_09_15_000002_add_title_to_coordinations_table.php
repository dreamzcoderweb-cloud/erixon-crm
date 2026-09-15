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
        Schema::table('coordinations', function (Blueprint $table) {
            if (!Schema::hasColumn('coordinations', 'title')) {
                $table->string('title')->nullable()->after('coordination_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coordinations', function (Blueprint $table) {
            if (Schema::hasColumn('coordinations', 'title')) {
                $table->dropColumn('title');
            }
        });
    }
};
