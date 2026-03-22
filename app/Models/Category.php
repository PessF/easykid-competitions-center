<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'min_age', 'max_age']; // อนุญาตให้บันทึกฟิลด์เหล่านี้
}