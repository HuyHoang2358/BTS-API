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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->text('detail');
            $table->foreignId('country_id')->nullable()->constrained()->on('countries')->nullOnDelete();
            $table->foreignId('province_id')->nullable()->constrained()->on('provinces')->nullOnDelete();
            $table->foreignId('district_id')->nullable()->constrained()->on('districts')->nullOnDelete();
            $table->foreignId('commune_id')->nullable()->constrained()->on('communes')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
