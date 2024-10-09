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
        Schema::create('geometry_cones', function (Blueprint $table) {
            $table->id();
            $table->double('radius');
            $table->double('height');
            $table->double('radial_segments');
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
        Schema::dropIfExists('geometry_cones');
    }
};
