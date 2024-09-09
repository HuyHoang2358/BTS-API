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
        Schema::create('pole_device', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pole_id')->constrained('poles')->cascadeOnDelete();
            $table->foreignId('device_id')->constrained('devices')->cascadeOnDelete();
            $table->unique(['pole_id', 'device_id']);
            $table->timestamp('installed_at')->nullable();
            $table->double('virtual_env_x')->nullable();
            $table->double('virtual_env_y')->nullable();
            $table->double('virtual_env_z')->nullable();
            $table->double('virtual_env_anpha')->nullable();
            $table->double('virtual_env_beta')->nullable();
            $table->double('virtual_env_gama')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pole_device');
    }
};
