<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GameType;
use App\Models\Category;

class CategorySettingController extends Controller
{
    public function index()
    {
        $gameTypes = GameType::all();
        $categories = Category::all();

        // สั่งให้ไปที่ไฟล์ view ชื่อ category_settings
        return view('admin.category_settings', compact('gameTypes', 'categories'));
    }
}