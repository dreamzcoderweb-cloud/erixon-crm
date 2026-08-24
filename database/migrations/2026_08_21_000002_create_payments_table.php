<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('lead_id')->nullable();
            $table->decimal('amount', 12, 2)->default(0.00);
            $table->decimal('tax_percentage', 5, 2)->default(0.00);
            $table->decimal('tax_amount', 12, 2)->default(0.00);
            $table->decimal('total_amount', 12, 2)->default(0.00);
            $table->string('payment_method', 50)->default('Bank Transfer');
            $table->date('payment_date')->nullable();
            $table->string('payment_screenshot')->nullable();
            $table->string('tax_number', 100)->nullable();
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('customer_id')->references('customer_id')->on('customers')->onDelete('cascade');
            $table->foreign('lead_id')->references('lead_id')->on('leads')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
