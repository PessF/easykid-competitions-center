<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Competition extends Model
{
    use HasFactory;

protected $fillable = [
        'name', 
        'location',   
        'description',  
        'latitude',     
        'longitude',   
        'banner_url', 
        'regis_start_date', 
        'regis_end_date', 
        'event_start_date', 
        'event_end_date', 
        'status'
    ];

    // $casts: แปลงชนิดข้อมูลอัตโนมัติ (เช่น แปลงวันที่ที่เป็นตัวอักษร ให้กลายเป็น Object เวลาดึงไปใช้จะได้คำนวณง่ายๆ)
protected $casts = [
        'regis_start_date' => 'datetime',
        'regis_end_date' => 'datetime',
        'event_start_date' => 'date',
        'event_end_date' => 'date',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    // ความสัมพันธ์: 1 งานแข่งหลัก มีได้ "หลาย" รุ่นการแข่งขัน (hasMany)
    public function classes()
    {
        return $this->hasMany(CompetitionClass::class);
    }
}