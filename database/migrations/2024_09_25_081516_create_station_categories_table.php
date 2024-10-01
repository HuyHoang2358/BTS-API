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
        Schema::create('station_categories', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->foreignId('location_id')->nullable()->constrained()->on('locations')->nullOnDelete();
            $table->foreignId('address_id')->nullable()->constrained()->on('addresses')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('station_categories');
    }
};
