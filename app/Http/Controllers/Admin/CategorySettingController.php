<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GameType;
use App\Models\Category;

class CategorySettingController extends Controller
{
    public function index()
    {
        // Octane-safe: explicitly limit queries instead of .all()
        $gameTypes = GameType::orderBy('id')->limit(500)->get();
        $categories = Category::orderBy('id')->limit(500)->get();

        // สั่งให้ไปที่ไฟล์ view ชื่อ category_settings
        return view('admin.category_settings', compact('gameTypes', 'categories'));
    }
}