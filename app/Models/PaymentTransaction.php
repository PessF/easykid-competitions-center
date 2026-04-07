<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    // 1 บิล มีหลายใบสมัคร
    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    // บิลนี้เป็นของ User คนไหน
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // บิลนี้จ่ายให้งานแข่งขันไหน
    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }

    // ใครเป็นแอดมินตรวจ
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}