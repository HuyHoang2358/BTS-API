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
        Schema::create('process_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('process_id')->constrained('processing_data_processes','id')->cascadeOnDelete();
            $table->integer('step_number');
            $table->text('step_name');
            $table->integer('current_progress')->default(0);
            $table->integer('total_progress')->default(100);
            $table->text('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('process_steps');
    }
};
