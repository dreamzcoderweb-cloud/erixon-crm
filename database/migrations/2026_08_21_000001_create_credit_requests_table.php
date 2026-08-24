<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('credit_requests', function (Blueprint $table) {
            $table->id('credit_request_id');
            $table->unsignedBigInteger('lead_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('username')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();
            $table->decimal('credit_amount', 12, 2)->default(0.00);
            $table->boolean('is_estimate')->default(false);
            $table->enum('status', [
                'Pending Admin Approval',
                'Approved by Admin',
                'Forwarded to Support',
                'Credit Added',
                'Rejected'
            ])->default('Pending Admin Approval');
            $table->unsignedBigInteger('admin_approved_by')->nullable();
            $table->timestamp('admin_approved_at')->nullable();
            $table->text('admin_remarks')->nullable();
            $table->unsignedBigInteger('support_approved_by')->nullable();
            $table->timestamp('support_approved_at')->nullable();
            $table->text('support_remarks')->nullable();
            $table->unsignedBigInteger('requested_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('lead_id')->references('lead_id')->on('leads')->onDelete('set null');
            $table->foreign('customer_id')->references('customer_id')->on('customers')->onDelete('cascade');
            $table->foreign('admin_approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('support_approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('requested_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_requests');
    }
};
