<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RobotModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RobotModelController extends Controller
{
    /**
     * หน้าหลักรายการแม่แบบหุ่นยนต์ (เพิ่ม Pagination เพื่อความปลอดภัย)
     */
    public function index()
    {
        // เปลี่ยนจาก get() เป็น paginate(20) เพื่อรองรับข้อมูลจำนวนมาก
        $robotModels = RobotModel::latest()->paginate(20);
        
        // ปรับให้ตรงกับชื่อไฟล์ view ของคุณภูมิ
        return view('admin.robot-models', compact('robotModels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'standard_weight' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // เพิ่มลิมิต 2MB
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
                if ($robot->image_url) {
                    Storage::disk('google')->delete($robot->image_url);
                }
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
            
            if ($robot->image_url) {
                Storage::disk('google')->delete($robot->image_url);
            }

            $robot->delete();
            return redirect()->route('admin.robot-models.index')->with('success', 'ลบแม่แบบหุ่นยนต์เรียบร้อยแล้ว!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'ไม่สามารถลบได้: ' . $e->getMessage()]);
        }
    }

    /**
     * ฟังก์ชันช่วยอัปโหลด (Helper) - ใช้ Stream เพื่อประหยัด RAM
     */
    private function uploadImage($file)
    {
        $fileName = "robot_" . time() . "_" . uniqid() . "." . $file->getClientOriginalExtension();
        $fullPath = "Robots/Models/$fileName";
        
        $stream = fopen($file->getRealPath(), 'r');
        Storage::disk('google')->put($fullPath, $stream);
        if (is_resource($stream)) {
            fclose($stream);
        }
        return $fullPath;
    }
    
    /**
     * สำหรับแสดงรูปภาพหุ่นยนต์ (กู้คืนให้รูปกลับมาแสดงผลได้)
     */
    public function showImage($id)
    {
        $robot = RobotModel::findOrFail($id);

        $disk = Storage::disk('google');
        $path = $robot->image_url;

        // เช็คว่ามี Path และไฟล์มีอยู่จริงหรือไม่
        if (!$path || !$disk->exists($path)) {
            abort(404, 'ไม่พบไฟล์รูปภาพ');
        }

        // 🚀 กู้คืนท่ามาตรฐาน: ดึงไฟล์ไบนารีและยัด Header Content-Type
        $file = $disk->get($path);
        $mimeType = $disk->mimeType($path) ?? 'image/jpeg';

        return response($file, 200)->header('Content-Type', $mimeType);
    }
}