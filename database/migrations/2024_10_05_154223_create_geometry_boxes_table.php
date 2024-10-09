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
        Schema::create('geometry_boxes', function (Blueprint $table) {
            $table->id();
            $table->double('depth');
            $table->double('width');
            $table->double('height');
            $table->double('pos_x');
            $table->double('pos_y');
            $table->double('pos_z');
            $table->double('rotate_x');
            $table->double('rotate_y');
            $table->double('rotate_z');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('geometry_boxes');
    }
};
