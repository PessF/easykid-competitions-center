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
            'category_name',
            'payment_transaction_id', 
            'status', 
            'checked_in_at',
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

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function competitionClass() {
        return $this->belongsTo(CompetitionClass::class);
    }

    public function paymentTransaction()
    {
        return $this->belongsTo(PaymentTransaction::class);
    }

}