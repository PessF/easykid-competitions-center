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
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete(); // ลบทีม = ลบลูกทีมทิ้งด้วย
            
            // ข้อมูลภาษาไทย
            $table->string('prefix_th')->nullable(); // ด.ช., ด.ญ., นาย, นางสาว
            $table->string('first_name_th');
            $table->string('last_name_th');
            
            // ข้อมูลภาษาอังกฤษ
            $table->string('prefix_en')->nullable(); // Mr., Miss
            $table->string('first_name_en');
            $table->string('last_name_en');
            
            // ข้อมูลอื่นๆ
            $table->date('birth_date'); // วันเกิด (เอาไว้อ้างอิงเช็คอายุกับ allowed_categories)
            $table->string('shirt_size')->nullable(); // ไซส์เสื้อ (เผื่อไว้)
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_members');
    }
};
