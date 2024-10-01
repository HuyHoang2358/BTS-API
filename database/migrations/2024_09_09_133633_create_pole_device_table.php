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
            $table->timestamp('attached_at')->nullable();
            $table->double('x')->nullable();
            $table->double('y')->nullable();
            $table->double('z')->nullable();
            $table->double('alpha')->nullable();
            $table->double('beta')->nullable();
            $table->double('gama')->nullable();
            $table->longText('rotation')->nullable();
            $table->longText('translation')->nullable();
            $table->longText('vertices')->nullable();
            $table->double('tilt')->nullable();
            $table->double('azimuth')->nullable();
            $table->longText('suggested_devices')->nullable();
            $table->longText('suggested_img')->nullable();
            $table->longText('description')->nullable();
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
