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
        if (!Schema::hasTable('demo_processes')) {
            Schema::create('demo_processes', function (Blueprint $table) {
                $table->bigIncrements('demo_process_id');
                $table->string('customer_name');
                $table->string('customer_phone', 30)->nullable();
                $table->unsignedBigInteger('lead_source_id')->nullable();
                $table->json('product_names')->nullable();
                $table->date('demo_date')->nullable();
                $table->string('demo_time', 20)->nullable();
                $table->string('customer_type', 100)->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('assigned_by')->nullable();
                $table->unsignedBigInteger('sub_assigned_by')->nullable();
                $table->enum('status', ['Pending', 'Finished'])->default('Pending');
                $table->text('remarks')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('lead_source_id')->references('lead_sources_id')->on('lead_sources')->onDelete('set null');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
                $table->foreign('assigned_by')->references('id')->on('users')->onDelete('set null');
                $table->foreign('sub_assigned_by')->references('id')->on('users')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demo_processes');
    }
};
