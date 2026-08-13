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
        Schema::create('lead_stages', function (Blueprint $table) {
            $table->id('lead_stage_id');
            $table->string('name');
            $table->integer('sort_order')->default(0);
            $table->tinyInteger('status')->default(1)->comment('1: Active, 0: Inactive');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_stages');
    }
};
