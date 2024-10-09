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
        Schema::create('pole_params', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pole_id')->constrained('poles')->cascadeOnDelete();
            $table->double('height')->nullable(); // chiều cao cột
            $table->boolean('is_roof')->default(false);     // có mái hay không
            $table->double('house_height')->nullable(); // chiều cao nhà

            $table->text('diameter_body_tube')->nullable(); // Đường kính ống thân
            $table->text('diameter_strut_tube')->nullable(); // Đường kính ống thanh chống
            $table->text('diameter_top_tube')->nullable(); // Đường kính ống thân cột mép trên
            $table->text('diameter_bottom_tube')->nullable(); // Đường kính ống thân cột mép dưới
            $table->double('tilt_angle')->nullable(); // Góc nghiêng cột
            $table->integer('is_shielded')->nullable(); // Có che chắn hay không

            $table->text('size')->nullable(); // kích thước cột
            $table->text('foot_size')->nullable(); // Kích thước chân cột
            $table->text('top_size')->nullable(); // Kích thước đỉnh cột
            $table->integer('is_active')->nullable()->default(0); // Cột hoạt động hay không
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->text('description')->nullable();  // Mô tả thêm

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pole_params');
    }
};
