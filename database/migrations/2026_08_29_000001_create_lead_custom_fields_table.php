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
        Schema::create('lead_custom_fields', function (Blueprint $table) {
            $table->id();
            $table->string('field_label');
            $table->string('field_name');
            $table->string('field_type');
            $table->text('field_options')->nullable();
            $table->string('is_required')->default('No');
            $table->integer('sort_order')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        if (Schema::hasTable('leads') && !Schema::hasColumn('leads', 'custom_fields')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->json('custom_fields')->nullable()->after('description');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('leads') && Schema::hasColumn('leads', 'custom_fields')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->dropColumn('custom_fields');
            });
        }

        Schema::dropIfExists('lead_custom_fields');
    }
};
