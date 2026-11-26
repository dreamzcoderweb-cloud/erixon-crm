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
        Schema::create('lead_requirement_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_requirement_id');
            $table->string('customer_type', 20)->default('all'); // 'all', 'user', 'reseller'
            $table->string('package_name', 255)->nullable();
            $table->decimal('quantity', 15, 2)->default(0);
            $table->decimal('unit_price_paisa', 10, 4)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_percentage', 5, 2)->default(18.00);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('lead_requirement_id')
                  ->references('lead_requirements_id')
                  ->on('lead_requirements')
                  ->onDelete('cascade');

            $table->index(['lead_requirement_id', 'customer_type', 'status'], 'lr_prices_req_type_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_requirement_prices');
    }
};
