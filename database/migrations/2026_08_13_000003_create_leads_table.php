<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id('lead_id');
            $table->unsignedBigInteger('customer_id');
            $table->string('lead_title');
            $table->unsignedBigInteger('lead_source_id')->nullable();
            $table->unsignedBigInteger('lead_stage_id')->nullable();
            $table->unsignedBigInteger('lead_requirement_id')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->decimal('expected_amount', 12, 2)->nullable();
            $table->text('description')->nullable();
            $table->date('next_followup_date')->nullable();
            $table->tinyInteger('status')->default(1)->comment('1: New/Active, 0: Closed/Inactive');
            $table->unsignedBigInteger('lost_reason_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('customer_id')->on('customers')->onDelete('cascade');
            $table->foreign('lead_source_id')->references('lead_sources_id')->on('lead_sources')->onDelete('set null');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
