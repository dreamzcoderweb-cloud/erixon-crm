<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('call_logs', function (Blueprint $table) {
            $table->id('call_id');
            $table->unsignedBigInteger('lead_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('phone', 30);
            $table->string('call_type', 50);
            $table->string('duration', 100)->nullable();
            $table->string('call_status', 100);
            $table->unsignedBigInteger('recording_id')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('lead_id')->references('lead_id')->on('leads')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('recording_id')->references('call_id')->on('call_recordings')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('call_logs');
    }
};
