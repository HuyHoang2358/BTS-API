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
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('station_id')->constrained()->on('stations')->cascadeOnDelete();
            $table->text('image_url')->nullable();
            $table->text('preview_image_url')->nullable();
            $table->text('filename');
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->text('take_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
