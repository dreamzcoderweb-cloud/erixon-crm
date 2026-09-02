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
        if (!Schema::hasTable('credit_request_custom_fields')) {
            Schema::create('credit_request_custom_fields', function (Blueprint $table) {
                $table->id();
                $table->string('field_label');
                $table->string('field_name')->unique();
                $table->string('field_type');
                $table->text('field_options')->nullable();
                $table->string('is_required')->default('No');
                $table->integer('sort_order')->default(0);
                $table->tinyInteger('status')->default(1);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (Schema::hasTable('credit_requests') && !Schema::hasColumn('credit_requests', 'custom_fields')) {
            Schema::table('credit_requests', function (Blueprint $table) {
                $table->json('custom_fields')->nullable()->after('requested_by');
            });
        }

        if (Schema::hasTable('referral_settings') && !Schema::hasColumn('referral_settings', 'credit_request_list_columns')) {
            Schema::table('referral_settings', function (Blueprint $table) {
                $table->json('credit_request_list_columns')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_request_custom_fields');

        if (Schema::hasTable('credit_requests') && Schema::hasColumn('credit_requests', 'custom_fields')) {
            Schema::table('credit_requests', function (Blueprint $table) {
                $table->dropColumn('custom_fields');
            });
        }

        if (Schema::hasTable('referral_settings') && Schema::hasColumn('referral_settings', 'credit_request_list_columns')) {
            Schema::table('referral_settings', function (Blueprint $table) {
                $table->dropColumn('credit_request_list_columns');
            });
        }
    }
};
