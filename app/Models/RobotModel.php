<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RobotModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'standard_weight',
        'image_url'
    ];
}