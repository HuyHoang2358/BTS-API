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
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->text('slug');
            $table->text('images')->nullable();
            $table->text('model_url')->nullable();
            $table->integer('length')->nullable();
            $table->integer('width')->nullable();
            $table->integer('depth')->nullable();
            $table->double('weight')->nullable();
            $table->double('diameter')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('device_category_id')->constrained('device_categories')->cascadeOnDelete();
            $table->foreignId('vendor_id')->nullable()->constrained()->on('vendors')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
