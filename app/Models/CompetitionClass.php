<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetitionClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'competition_id', 'name', 'entry_fee', 'max_members', 'max_teams', 
        'rules_url', 'game_type_name', 'robot_name', 'robot_weight', 
        'robot_image_url', 'allowed_categories'
    ];

    protected $casts = [
        'allowed_categories' => 'array',
        'robot_weight' => 'decimal:2',
        'entry_fee' => 'decimal:2',
    ];

    // ความสัมพันธ์: รุ่นการแข่งขันนี้ "เป็นของ" งานแข่งหลักงานไหน (belongsTo)
    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }
}