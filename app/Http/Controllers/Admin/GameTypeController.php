<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GameType;
use Illuminate\Http\Request;

class GameTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $gameTypes = GameType::orderBy('id', 'asc')->paginate(10);

        return view('admin.game_types.index', compact('gameTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. ตรวจสอบข้อมูล (Validation) ว่าห้ามว่าง และห้ามชื่อซ้ำ
        $request->validate([
            'name' => 'required|string|max:255|unique:game_types,name',
        ], [
            'name.required' => 'กรุณากรอกชื่อประเภทการแข่งขัน',
            'name.unique' => 'ชื่อประเภทนี้มีอยู่ในระบบแล้ว'
        ]);

        // 2. บันทึกลงฐานข้อมูล
        GameType::create([
            'name' => $request->name,
        ]);

        // 3. เด้งกลับไปหน้าเดิม พร้อมส่งข้อความแจ้งเตือนความสำเร็จ
        return redirect()->route('admin.category-settings')->with('success', 'เพิ่มประเภทการแข่งขันเรียบร้อยแล้ว!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // 1. ตรวจสอบข้อมูล (ละเว้นเช็คชื่อซ้ำกับ ID ตัวเอง)
        $request->validate([
            'name' => 'required|string|max:255|unique:game_types,name,' . $id,
        ], [
            'name.required' => 'กรุณากรอกชื่อประเภทการแข่งขัน',
            'name.unique' => 'ชื่อประเภทนี้มีอยู่ในระบบแล้ว'
        ]);

        // 2. ค้นหาและอัปเดตข้อมูล
        $gameType = GameType::findOrFail($id);
        $gameType->update([
            'name' => $request->name,
        ]);

        // 3. เด้งกลับพร้อมแจ้งเตือน
        return redirect()->route('admin.category-settings')->with('success', 'แก้ไขประเภทการแข่งขันเรียบร้อยแล้ว!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // 1. ค้นหาข้อมูล และสั่งลบ
        $gameType = GameType::findOrFail($id);
        $gameType->delete();

        // 2. เด้งกลับพร้อมแจ้งเตือน
        return redirect()->route('admin.category-settings')->with('success', 'ลบประเภทการแข่งขันเรียบร้อยแล้ว!');
    }
}
