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
        // 1. Pending Works Table
        Schema::create('pending_works', function (Blueprint $table) {
            $table->bigIncrements('pending_id');
            $table->unsignedBigInteger('user_id');
            $table->date('date');
            $table->text('notes')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0: Pending, 1: Process, 2: Finished');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'date']);
            $table->index('status');
        });

        // 2. Daily Learnings Table
        Schema::create('daily_learnings', function (Blueprint $table) {
            $table->bigIncrements('daily_learning_id');
            $table->unsignedBigInteger('user_id');
            $table->date('date');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'date']);
        });

        // 3. Commitments Table
        Schema::create('commitments', function (Blueprint $table) {
            $table->bigIncrements('commitment_id');
            $table->unsignedBigInteger('user_id');
            $table->date('date');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commitments');
        Schema::dropIfExists('daily_learnings');
        Schema::dropIfExists('pending_works');
    }
};
