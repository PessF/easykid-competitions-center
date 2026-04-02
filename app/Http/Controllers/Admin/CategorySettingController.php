<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GameType;
use App\Models\Category;

class CategorySettingController extends Controller
{
    public function index()
    {
        // Paginate for better performance with table display management
        $gameTypes = GameType::orderBy('id')->paginate(25, ['*'], 'game_types_page');
        $categories = Category::orderBy('id')->paginate(25, ['*'], 'categories_page');

        // สั่งให้ไปที่ไฟล์ view ชื่อ category_settings
        return view('admin.category_settings', compact('gameTypes', 'categories'));
    }
}