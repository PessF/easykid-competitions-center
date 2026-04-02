<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('competition_classes', function (Blueprint $table) {
                $table->id();
                
                // 1. ความสัมพันธ์ (Foreign Key)
                // เชื่อมกับตาราง competitions ถ้างานหลักโดนลบ รายการย่อยนี้จะบินตามไปด้วย (cascade)
                $table->foreignId('competition_id')->constrained('competitions')->onDelete('cascade');
                
                // 2. ข้อมูลพื้นฐานของการแข่งรุ่นนี้
                $table->string('name'); // ชื่อรุ่น เช่น "Mega Sumo 3 Kg. Autonomous"
                $table->decimal('entry_fee', 8, 2)->default(0); // ค่าสมัคร
                $table->integer('max_members')->default(1); // จำนวนคนสูงสุดต่อ 1 ทีม
                $table->integer('max_teams')->nullable(); // รับจำนวนกี่ทีม (ว่างไว้แปลว่ารับไม่อั้น)
                $table->string('rules_url')->nullable(); // ลิงก์เก็บไฟล์กติกา PDF (เผื่อมี)
                
                // 3. โซน DATA SNAPSHOT (ปั๊มข้อมูลมาจาก Master Data)
                $table->string('game_type_name'); 
                $table->string('robot_name'); 
                $table->decimal('robot_weight', 8, 2)->nullable(); 
                $table->string('robot_image_url')->nullable();
                
                // 4. เงื่อนไขหมวดหมู่อายุ (JSON)
                $table->json('allowed_categories'); 

                $table->index('game_type_name'); 

                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competition_classes');
    }
};
