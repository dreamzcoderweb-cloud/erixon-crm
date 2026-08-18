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
        Schema::create('followup_reassignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('followup_id');
            $table->unsignedBigInteger('previous_staff_id');
            $table->unsignedBigInteger('new_staff_id');
            $table->unsignedBigInteger('reassigned_by');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('followup_id')->references('followups_id')->on('followups')->onDelete('cascade');
            $table->foreign('previous_staff_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('new_staff_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('reassigned_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('followup_reassignments');
    }
};
