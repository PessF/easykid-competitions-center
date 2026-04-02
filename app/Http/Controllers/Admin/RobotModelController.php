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
        $robotModels = RobotModel::latest()->paginate(20);
        return view('admin.robot-models', compact('robotModels'));
    }

    public function store(Request $request)
    {
        if (empty($request->all()) && $request->server('CONTENT_LENGTH') > 0) {
            return back()->withInput()->withErrors(['image' => 'ไฟล์มีขนาดใหญ่เกินไป (ระบบรองรับสูงสุด 12MB)']);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'standard_weight' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:12288',
        ], [
            'name.required' => 'กรุณากรอกชื่อแม่แบบหุ่นยนต์',
            'image.max' => 'ขนาดไฟล์รูปภาพต้องไม่เกิน 12MB',
            'image.image' => 'ไฟล์ต้องเป็นรูปภาพเท่านั้น',
        ]);

        try {
            $imagePath = null;
            if ($request->hasFile('image')) {
                if (!$request->file('image')->isValid()) {
                    return back()->withInput()->withErrors(['image' => 'ไฟล์รูปภาพไม่สมบูรณ์ หรือมีขนาดใหญ่เกินกำหนดของเซิร์ฟเวอร์']);
                }
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
        if (empty($request->all()) && $request->server('CONTENT_LENGTH') > 0) {
            return back()->withInput()->withErrors(['image' => 'ไฟล์มีขนาดใหญ่เกินไป (ระบบรองรับสูงสุด 12MB)']);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'standard_weight' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:12288',
        ], [
            'image.max' => 'ขนาดไฟล์รูปภาพต้องไม่เกิน 12MB',
        ]);

        try {
            $robot = RobotModel::findOrFail($id);
            $data = $request->only(['name', 'standard_weight']);

            if ($request->hasFile('image')) {
                if (!$request->file('image')->isValid()) {
                    return back()->withInput()->withErrors(['image' => 'ไฟล์รูปภาพไม่สมบูรณ์ หรือมีขนาดใหญ่เกินกำหนดของเซิร์ฟเวอร์']);
                }

                if ($robot->image_url) {
                    Storage::disk('public')->delete($robot->image_url);
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
                Storage::disk('public')->delete($robot->image_url);
            }

            $robot->delete();
            return redirect()->route('admin.robot-models.index')->with('success', 'ลบแม่แบบหุ่นยนต์เรียบร้อยแล้ว!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'ไม่สามารถลบได้: ' . $e->getMessage()]);
        }
    }

    private function uploadImage($file)
    {
        $fileName = "robot_" . time() . "_" . uniqid() . "." . $file->getClientOriginalExtension();
        $folderPath = "Robots/Models";
        
        return $file->storeAs($folderPath, $fileName, 'public');
    }
    
    public function showImage($id)
    {
        $robot = RobotModel::findOrFail($id);
        $disk = Storage::disk('public');
        $path = $robot->image_url;

        if (!$path || !$disk->exists($path)) {
            abort(404, 'ไม่พบไฟล์รูปภาพ');
        }

        return response()->file($disk->path($path));
    }
}