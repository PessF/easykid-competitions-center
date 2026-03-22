<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RobotModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RobotModelController extends Controller
{
    public function index()
    {
        $robotModels = RobotModel::latest()->get();
        // ปรับให้ตรงกับโครงสร้างโฟลเดอร์ views/admin/robot-models/index.blade.php
        return view('admin.robot-models', compact('robotModels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'standard_weight' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg',
        ], [
            'name.required' => 'กรุณากรอกชื่อแม่แบบหุ่นยนต์',
            'image.max' => 'ขนาดไฟล์รูปภาพต้องไม่เกิน 2MB',
        ]);

        try {
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $this->uploadImage($request->file('image'));
            }

            RobotModel::create([
                'name' => $request->name,
                'standard_weight' => $request->standard_weight,
                'image_url' => $imagePath,
            ]);

            return redirect()->route('admin.robot-models.index')->with('success', 'เพิ่มแม่แบบหุ่นยนต์เรียบร้อยแล้ว!');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'การอัปโหลดล้มเหลว: ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'standard_weight' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            $robot = RobotModel::findOrFail($id);
            $data = $request->only(['name', 'standard_weight']);

            if ($request->hasFile('image')) {
                // 1. ลบรูปเก่าออกจาก Google Drive เพื่อประหยัดพื้นที่
                if ($robot->image_url) {
                    Storage::disk('google')->delete($robot->image_url);
                }
                // 2. อัปโหลดรูปใหม่
                $data['image_url'] = $this->uploadImage($request->file('image'));
            }

            $robot->update($data);
            return redirect()->route('admin.robot-models.index')->with('success', 'แก้ไขข้อมูลเรียบร้อยแล้ว!');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'ไม่สามารถแก้ไขได้: ' . $e->getMessage()]);
        }
    }

    public function destroy(string $id)
    {
        try {
            $robot = RobotModel::findOrFail($id);
            
            // ลบรูปออกจาก Google Drive ก่อนลบข้อมูลใน DB
            if ($robot->image_url) {
                Storage::disk('google')->delete($robot->image_url);
            }

            $robot->delete();
            return redirect()->route('admin.robot-models.index')->with('success', 'ลบแม่แบบหุ่นยนต์เรียบร้อยแล้ว!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'ไม่สามารถลบได้: ' . $e->getMessage()]);
        }
    }

    // ฟังก์ชันช่วยอัปโหลด (Helper) เพื่อลดโค้ดซ้ำซ้อน
    private function uploadImage($file)
    {
        $fileName = "robot_" . time() . "_" . uniqid() . "." . $file->getClientOriginalExtension();
        $fullPath = "Robots/Models/$fileName";
        Storage::disk('google')->put($fullPath, file_get_contents($file));
        return $fullPath;
    }
    
    public function showImage($id)
    {
        // 1. หาข้อมูลหุ่นยนต์
        $robot = RobotModel::findOrFail($id);

        // 2. เช็คว่ามี Path รูปภาพหรือไม่
        if (!$robot->image_url) {
            abort(404, 'ไม่มีรูปภาพสำหรับหุ่นยนต์ตัวนี้');
        }

        // 3. เช็คว่าไฟล์มีอยู่จริงใน Google Drive หรือไม่
        if (!Storage::disk('google')->exists($robot->image_url)) {
            abort(404, 'ไม่พบไฟล์รูปภาพใน Google Drive');
        }

        // 4. ดึงข้อมูลไฟล์ (Binary Data)
        $file = Storage::disk('google')->get($robot->image_url);

        // 5. ดึง MimeType (เช่น image/png, image/jpeg) เพื่อบอก Browser ว่านี่คือไฟล์ภาพนะ
        $mimeType = Storage::disk('google')->mimeType($robot->image_url);

        // 6. พ่นข้อมูลภาพกลับไปพร้อม Header ที่ถูกต้อง
        return response($file, 200)->header('Content-Type', $mimeType);
    }
}