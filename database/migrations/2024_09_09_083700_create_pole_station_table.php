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
        Schema::create('station_pole', function (Blueprint $table) {
            $table->string('station_code');
            $table->foreign('station_code')->references('code')->on('stations')->cascadeOnDelete();
            $table->foreignId('pole_id')->constrained()->on('poles')->cascadeOnDelete();
            $table->timestamp('built_on');
            $table->timestamps();
            $table->primary(['station_code', 'pole_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('station_pole');
    }
};
