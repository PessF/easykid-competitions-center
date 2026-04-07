<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('competitions', function (Blueprint $table) {
            // เพิ่มฟิลด์ google_sheet_id ไว้ต่อจากชื่องานแข่งขัน (หรือฟิลด์ไหนก็ได้ที่คุณต้องการ)
            $table->string('google_sheet_id')->nullable()->after('name');
        });
    }

    public function down()
    {
        Schema::table('competitions', function (Blueprint $table) {
            $table->dropColumn('google_sheet_id');
        });
    }
};