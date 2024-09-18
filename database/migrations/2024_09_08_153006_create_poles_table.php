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
        Schema::create('poles', function (Blueprint $table) {
            $table->id();
            $table->text('name'); // tên cột
            $table->double('height')->nullable(); // chiều cao cột
            $table->boolean('is_roof')->default(false);     // có mái hay không
            $table->double('house_height')->nullable(); // chiều cao nhà
            $table->foreignId('pole_category_id')->constrained('pole_categories')->cascadeOnDelete();
            $table->text('size')->nullable(); // kích thước cột
            $table->double('diameter_body_tube')->nullable(); // Đường kính ống thân
            $table->double('diameter_strut_tube')->nullable(); // Đường kính ống thanh chống
            $table->double('diameter_top_tube')->nullable(); // Đường kính ống thân cột mép trên
            $table->double('diameter_bottom_tube')->nullable(); // Đường kính ống thân cột mép dưới
            $table->text('foot_size')->nullable(); // Kích thước chân cột
            $table->text('top_size')->nullable(); // Kích thước đỉnh cột
            $table->text('structure')->nullable(); // Cấu hình cột
            $table->text('description')->nullable();  // Mô tả thêm
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('poles');
    }
};
