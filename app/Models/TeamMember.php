<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TeamMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id', 
        'prefix_th', 'first_name_th', 'last_name_th',
        'prefix_en', 'first_name_en', 'last_name_en',
        'birth_date', 'shirt_size'
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function team() {
        return $this->belongsTo(Team::class);
    }

    // Accessor เสริม: คำนวณอายุอัตโนมัติ (เผื่อเอาไปใช้หน้า Blade ง่ายๆ)
    public function getAgeAttribute() {
        return Carbon::parse($this->birth_date)->age;
    }
}