<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        // 1. ตรวจสอบข้อมูล (Validation)
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'min_age' => 'required|integer|min:1',
            'max_age' => 'required|integer|gt:min_age', // gt = greater than (ต้องมากกว่า min_age)
        ], [
            'name.required' => 'กรุณากรอกชื่อรุ่น',
            'name.unique' => 'ชื่อรุ่นนี้มีอยู่ในระบบแล้ว',
            'min_age.required' => 'กรุณากรอกอายุขั้นต่ำ',
            'max_age.required' => 'กรุณากรอกอายุสูงสุด',
            'max_age.gt' => 'อายุสูงสุด ต้องมากกว่าอายุขั้นต่ำ',
        ]);

        // 2. บันทึกลงฐานข้อมูล
        Category::create([
            'name' => $request->name,
            'min_age' => $request->min_age,
            'max_age' => $request->max_age,
        ]);

        // 3. เด้งกลับไปหน้าเดิม พร้อมส่งข้อความแจ้งเตือนความสำเร็จ
        return redirect()->route('admin.category-settings')->with('success', 'เพิ่มรุ่นอายุเรียบร้อยแล้ว!');
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
        // 1. ตรวจสอบข้อมูล (เพิ่มรหัส $id ต่อท้าย unique เพื่อละเว้นการเช็คซ้ำกับข้อมูลตัวเอง)
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
            'min_age' => 'required|integer|min:1',
            'max_age' => 'required|integer|gt:min_age',
        ], [
            'name.required' => 'กรุณากรอกชื่อรุ่น',
            'name.unique' => 'ชื่อรุ่นนี้มีอยู่ในระบบแล้ว',
            'min_age.required' => 'กรุณากรอกอายุขั้นต่ำ',
            'max_age.required' => 'กรุณากรอกอายุสูงสุด',
            'max_age.gt' => 'อายุสูงสุด ต้องมากกว่าอายุขั้นต่ำ',
        ]);

        // 2. ค้นหาข้อมูลเดิม และอัปเดต
        $category = Category::findOrFail($id);
        $category->update([
            'name' => $request->name,
            'min_age' => $request->min_age,
            'max_age' => $request->max_age,
        ]);

        // 3. เด้งกลับพร้อมแจ้งเตือน
        return redirect()->route('admin.category-settings')->with('success', 'แก้ไขรุ่นอายุเรียบร้อยแล้ว!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // 1. ค้นหาข้อมูล และสั่งลบ
        $category = Category::findOrFail($id);
        $category->delete();

        // 2. เด้งกลับพร้อมแจ้งเตือน
        return redirect()->route('admin.category-settings')->with('success', 'ลบรุ่นอายุเรียบร้อยแล้ว!');
    }
}
