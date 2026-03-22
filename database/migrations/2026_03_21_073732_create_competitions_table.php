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
        Schema::create('competitions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location')->nullable(); // ชื่อสถานที่ (คนอ่าน)
            $table->text('description')->nullable(); // รายละเอียดงาน
            $table->decimal('latitude', 10, 8)->nullable(); // พิกัดแนวตั้ง
            $table->decimal('longitude', 11, 8)->nullable(); // พิกัดแนวนอน
            $table->string('banner_url')->nullable();
            $table->dateTime('regis_start_date')->nullable();
            $table->dateTime('regis_end_date')->nullable();
            $table->date('event_start_date')->nullable();
            $table->date('event_end_date')->nullable();
            $table->enum('status', ['draft', 'registration', 'ongoing', 'completed'])->default('draft');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competitions');
    }
};
