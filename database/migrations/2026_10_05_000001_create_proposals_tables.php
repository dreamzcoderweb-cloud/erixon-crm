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
        Schema::create('proposals', function (Blueprint $table) {
            $table->bigIncrements('proposal_id');
            $table->string('proposal_number', 50)->unique();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name');
            $table->string('customer_mobile')->nullable();
            $table->unsignedBigInteger('lead_requirement_id')->nullable();
            $table->string('lead_requirement_name')->nullable();
            $table->unsignedBigInteger('sales_manager_id')->nullable();
            $table->string('sales_manager_name')->nullable();
            $table->string('sales_manager_mobile')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('subtotal', 14, 2)->default(0.00);
            $table->decimal('total_tax', 14, 2)->default(0.00);
            $table->decimal('total_amount', 14, 2)->default(0.00);
            $table->string('status', 30)->default('Active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('customer_id')->references('customer_id')->on('customers')->nullOnDelete();
            $table->foreign('lead_requirement_id')->references('lead_requirements_id')->on('lead_requirements')->nullOnDelete();
            $table->foreign('sales_manager_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('proposal_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('proposal_id');
            $table->string('product_package');
            $table->decimal('price', 14, 2)->default(0.00); // Paisa
            $table->decimal('tax_percentage', 8, 2)->default(0.00); // Tax %
            $table->decimal('tax_amount', 14, 2)->default(0.00); // Tax Amount
            $table->decimal('amount', 14, 2)->default(0.00); // Total Amount
            $table->timestamps();

            $table->foreign('proposal_id')->references('proposal_id')->on('proposals')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposal_items');
        Schema::dropIfExists('proposals');
    }
};
