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
        Schema::create('gimbals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('image_id')->constrained()->on('images')->cascadeOnDelete();
            $table->string('yaw_degree')->nullable();
            $table->string('pitch_degree')->nullable();
            $table->string('roll_degree')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gimbals');
    }
};
