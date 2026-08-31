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
        if (!Schema::hasTable('followup_custom_fields')) {
            Schema::create('followup_custom_fields', function (Blueprint $table) {
                $table->id();
                $table->string('field_label');
                $table->string('field_name')->unique();
                $table->string('field_type'); // Text, Number, Dropdown, Textarea, Date, Checkbox
                $table->text('field_options')->nullable(); // Comma-separated options for Dropdown
                $table->string('is_required')->default('No'); // Yes, No
                $table->tinyInteger('status')->default(1); // 1 = Active, 0 = Inactive
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (Schema::hasTable('followups') && !Schema::hasColumn('followups', 'custom_fields')) {
            Schema::table('followups', function (Blueprint $table) {
                $table->json('custom_fields')->nullable()->after('remarks');
            });
        }

        if (Schema::hasTable('referral_settings') && !Schema::hasColumn('referral_settings', 'followup_list_columns')) {
            Schema::table('referral_settings', function (Blueprint $table) {
                $table->json('followup_list_columns')->nullable()->after('customer_list_columns');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('referral_settings') && Schema::hasColumn('referral_settings', 'followup_list_columns')) {
            Schema::table('referral_settings', function (Blueprint $table) {
                $table->dropColumn('followup_list_columns');
            });
        }

        if (Schema::hasTable('followups') && Schema::hasColumn('followups', 'custom_fields')) {
            Schema::table('followups', function (Blueprint $table) {
                $table->dropColumn('custom_fields');
            });
        }

        Schema::dropIfExists('followup_custom_fields');
    }
};
