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
        if (!Schema::hasTable('customer_custom_fields')) {
            Schema::create('customer_custom_fields', function (Blueprint $table) {
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_custom_fields');
    }
};
