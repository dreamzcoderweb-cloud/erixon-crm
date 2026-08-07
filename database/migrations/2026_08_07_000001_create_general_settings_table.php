<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('general_settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->default('PowerGYM');
            $table->string('logo')->nullable();
            $table->string('whatsapp_no')->nullable();
            $table->string('theme_color')->default('#00b2a9');
            $table->timestamps();
        });

        // Insert initial default settings record
        DB::table('general_settings')->insert([
            'company_name' => 'PowerGYM',
            'logo' => null,
            'whatsapp_no' => '8610747034',
            'theme_color' => '#00b2a9',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general_settings');
    }
};
