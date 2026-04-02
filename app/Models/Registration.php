<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    use HasFactory;

protected $fillable = [
        'regis_no', 
        'user_id', 
        'team_id', 
        'competition_id', 
        'competition_class_id', 
        'status', 
        'payment_slip_path',
        'verified_by',      
        'verified_at',     
        'reject_reason',    
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function team() {
        return $this->belongsTo(Team::class);
    }

    public function competition() {
        return $this->belongsTo(Competition::class);
    }

    public function competitionClass() {
        return $this->belongsTo(CompetitionClass::class);
    }

}