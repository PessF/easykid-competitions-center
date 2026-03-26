<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'school_name'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function members() {
        return $this->hasMany(TeamMember::class);
    }

    public function registrations() {
        return $this->hasMany(Registration::class);
    }
}