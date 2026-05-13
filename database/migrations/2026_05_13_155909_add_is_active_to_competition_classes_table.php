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
       Schema::table('competition_classes', function (Blueprint $table) {
           // เพิ่มคอลัมน์ is_active กำหนดค่าเริ่มต้นเป็น true (เปิดรับสมัคร)
           $table->boolean('is_active')->default(true)->after('max_teams');
       });
   }

   public function down()
   {
       Schema::table('competition_classes', function (Blueprint $table) {
           $table->dropColumn('is_active');
       });
   }
};
