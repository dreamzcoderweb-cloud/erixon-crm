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
        if (!Schema::hasTable('coordination_joining_staff')) {
            Schema::create('coordination_joining_staff', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('coordination_id');
                $table->unsignedBigInteger('user_id');
                $table->enum('status', ['Pending', 'Joined'])->default('Pending');
                $table->timestamp('joined_at')->nullable();
                $table->timestamps();

                $table->unique(['coordination_id', 'user_id'], 'uniq_coord_user');
                $table->foreign('coordination_id')->references('coordination_id')->on('coordinations')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coordination_joining_staff');
    }
};
