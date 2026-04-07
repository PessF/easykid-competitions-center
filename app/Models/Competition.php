<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

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
        'status',
        'google_sheet_id',
    ];

    // $casts: แปลงชนิดข้อมูลอัตโนมัติ 
    protected $casts = [
        'regis_start_date' => 'datetime',
        'regis_end_date'   => 'datetime',
        'event_start_date' => 'date',
        'event_end_date'   => 'date',
        'latitude'         => 'decimal:8',
        'longitude'        => 'decimal:8',
    ];

    protected function dynamicStatus(): Attribute
    {
        return Attribute::make(
            get: function () {
                // 1. ถ้าแอดมินเซ็ตสถานะใน Database เป็นอย่างอื่น เช่น draft, cancelled ให้ยึดตามคำสั่งแอดมินก่อน
                if (in_array($this->status, ['draft', 'cancelled', 'hidden'])) {
                    return $this->status;
                }

                $now = now(); // เวลา ณ วินาทีนี้ที่เปิดหน้าเว็บ

                // 2. เช็คเวลาตาม Timeline ของงานแข่งขัน
                if ($now->isBefore($this->regis_start_date)) {
                    return 'coming_soon'; // ยังไม่ถึงวันเปิดรับสมัคร
                } 
                elseif ($now->between($this->regis_start_date, $this->regis_end_date)) {
                    return 'open'; // เปิดรับสมัครอยู่ (ปุ่มสมัครจะกดได้แค่ตอนที่สถานะนี้ทำงาน)
                } 
                elseif ($now->isAfter($this->regis_end_date) && $now->startOfDay()->isBefore($this->event_start_date)) {
                    return 'registration_closed'; // ปิดรับสมัครแล้ว (รอกำหนดการแข่ง)
                } 
                elseif ($now->startOfDay()->between($this->event_start_date, $this->event_end_date)) {
                    return 'ongoing'; // วันนี้กำลังแข่งขันอยู่!
                } 
                else {
                    return 'ended'; // เลยวันแข่งมาแล้ว จบงาน
                }
            }
        );
    }

    // ความสัมพันธ์: 1 งานแข่งหลัก มีได้ "หลาย" รุ่นการแข่งขัน (hasMany)
    public function classes(): HasMany
    {
        return $this->hasMany(CompetitionClass::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }
}