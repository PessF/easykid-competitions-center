<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('competition_classes', function (Blueprint $table) {
            // สั่งเปลี่ยน (change) คอลัมน์เดิม ให้ยอมรับค่า null ได้
            $table->string('robot_name')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('competition_classes', function (Blueprint $table) {
            // ถอยกลับ (ถ้าต้อง rollback) บังคับว่าห้ามเป็น null
            $table->string('robot_name')->nullable(false)->change();
        });
    }
};