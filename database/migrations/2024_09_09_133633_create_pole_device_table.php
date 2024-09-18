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
            $table->timestamp('attached_at')->nullable();
            $table->double('x')->nullable();
            $table->double('y')->nullable();
            $table->double('z')->nullable();
            $table->double('anpha')->nullable();
            $table->double('beta')->nullable();
            $table->double('gama')->nullable();
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
