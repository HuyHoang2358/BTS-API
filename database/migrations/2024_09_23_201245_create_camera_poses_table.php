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
        Schema::create('camera_poses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('image_id')->constrained()->on('images')->cascadeOnDelete();
            $table->text('w2c')->nullable();
            $table->text('tvec')->nullable();
            $table->text('qvec')->nullable();
            $table->text('cent_point')->nullable();
            $table->text('euler_angle')->nullable();
            $table->text('intrinsic_mtx')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('camera_poses');
    }
};
