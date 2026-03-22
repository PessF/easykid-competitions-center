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
        Schema::create('robot_models', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // ชื่อแม่แบบ เช่น "หุ่นยนต์ซูโม่ 3 Kg." หรือ "หุ่นยนต์เดินตามเส้น"
            $table->decimal('standard_weight', 8, 2)->nullable(); // น้ำหนักมาตรฐาน (กก.)
            $table->string('image_url')->nullable(); // ลิงก์รูปภาพจาก Google Drive
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('robot_models');
    }
};
