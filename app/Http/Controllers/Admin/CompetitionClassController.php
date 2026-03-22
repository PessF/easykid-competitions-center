<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\CompetitionClass;
use App\Models\Category;
use App\Models\RobotModel; // เผื่อดึงไปให้ Dropdown
use App\Models\GameType; // เผื่อดึงไปให้ Dropdown
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompetitionClassController extends Controller
{
    private function uploadClassFile($file, $compName, $className, $type)
    {
        $safeCompName = $this->sanitizeFolderName($compName);
        $safeClassName = $this->sanitizeFolderName($className);
        
        $ext = $file->getClientOriginalExtension();
        $fileName = strtolower($type) . "_" . time() . "_" . uniqid() . "." . $ext;
        
        // Competitions/{ชื่อการแข่งขัน}/Competition_Class/{ชื่อรุ่น}/{Picture หรือ Rule}
        $fullPath = "Competitions/{$safeCompName}/Competition_Class/{$safeClassName}/{$type}/$fileName";
        
        Storage::disk('google')->put($fullPath, file_get_contents($file));
        return $fullPath;
    }

    private function sanitizeFolderName($name)
    {
        return trim(preg_replace('/[\\\\\/:\*\?"<>\|]/', '_', $name));
    }

    // 🏆 หน้าหลักรายการย่อย (ส่งข้อมูลไปแสดงผลและเตรียม Master Data)
    public function index(Competition $competition)
    {
        // ดึงรายการย่อยทั้งหมดที่อยู่ในงานแข่งนี้
        $classes = $competition->classes()->latest()->get();
        
        // ดึง Master Data เตรียมไว้ให้ Modal สร้าง/แก้ไข
        $categories = Category::all();
        $robotModels = RobotModel::all();
        $gameTypes = GameType::all();

        //สมมติเราจะสร้าง View ไว้ที่ resources/views/admin/competitions/classes/index.blade.php
        return view('admin.competitions.classes.index', compact('competition', 'classes', 'categories', 'robotModels', 'gameTypes'));
    }

  
    public function store(Request $request, Competition $competition)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'entry_fee' => 'required|numeric|min:0',
            'max_members' => 'required|integer|min:1',
            'max_teams' => 'nullable|integer|min:1',
            'rule_pdf' => 'nullable|mimes:pdf', 
            'game_type_name' => 'required|string|max:255',
            'robot_name' => 'required|string|max:255',
            'robot_standard_weight' => 'nullable|numeric|min:0',
            'robot_image' => 'nullable|image|mimes:jpeg,png,jpg',
            'master_robot_image_url' => 'nullable|string', 
            'allowed_category' => 'required|string',
        ], [
            'name.required' => 'กรุณากรอกชื่อรุ่นการแข่งขัน',
            'name.max' => 'ชื่อรุ่นการแข่งขันต้องไม่เกิน 255 ตัวอักษร',
            'entry_fee.required' => 'กรุณากรอกค่าสมัคร (หากฟรีให้กรอก 0)',
            'entry_fee.numeric' => 'ค่าสมัครต้องเป็นตัวเลขเท่านั้น',
            'entry_fee.min' => 'ค่าสมัครต้องไม่ติดลบ',
            'max_members.required' => 'กรุณากรอกจำนวนสมาชิกสูงสุดต่อทีม',
            'max_members.integer' => 'จำนวนสมาชิกต้องเป็นจำนวนเต็ม',
            'max_members.min' => 'จำนวนสมาชิกต้องมีอย่างน้อย 1 คน',
            'max_teams.integer' => 'จำนวนทีมที่รับต้องเป็นจำนวนเต็ม',
            'max_teams.min' => 'จำนวนทีมที่รับต้องมีอย่างน้อย 1 ทีม',
            'rule_pdf.mimes' => 'ไฟล์กติกาต้องเป็นนามสกุล PDF เท่านั้น',
            'game_type_name.required' => 'กรุณาเลือกประเภทเกม',
            'robot_name.required' => 'กรุณาเลือกหรือระบุชื่อหุ่นยนต์',
            'robot_standard_weight.numeric' => 'น้ำหนักหุ่นยนต์ต้องเป็นตัวเลขเท่านั้น',
            'robot_standard_weight.min' => 'น้ำหนักหุ่นยนต์ต้องไม่ติดลบ',
            'robot_image.image' => 'ไฟล์หุ่นยนต์ต้องเป็นรูปภาพเท่านั้น',
            'robot_image.mimes' => 'รูปภาพหุ่นยนต์ต้องเป็นนามสกุล jpeg, png หรือ jpg เท่านั้น',
            'allowed_category.required' => 'กรุณาเลือกหมวดหมู่อายุ 1 หมวดหมู่',
        ]);

        try {
            $rulePath = null;
            $robotImagePath = $request->master_robot_image_url; 

            if ($request->hasFile('rule_pdf')) {
                $rulePath = $this->uploadClassFile($request->file('rule_pdf'), $competition->name, $request->name, 'Rule');
            }

            if ($request->hasFile('robot_image')) {
                $robotImagePath = $this->uploadClassFile($request->file('robot_image'), $competition->name, $request->name, 'Picture');
            }

            $categoriesSnapshot = Category::where('name', $request->allowed_category)
                ->get(['name', 'min_age', 'max_age'])
                ->toArray();

            CompetitionClass::create([
                'competition_id' => $competition->id,
                'name' => $request->name,
                'entry_fee' => $request->entry_fee,
                'max_members' => $request->max_members,
                'max_teams' => $request->max_teams,
                
                'rules_url' => $rulePath, 

                'game_type_name' => $request->game_type_name,
                'robot_name' => $request->robot_name,
                
                'robot_weight' => $request->robot_standard_weight, 
                'robot_image_url' => $robotImagePath,
                
                'allowed_categories' => $categoriesSnapshot, 
            ]);

            return redirect()->back()->with('success', 'เพิ่มรุ่นการแข่งขันเรียบร้อยแล้ว!');

        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'ระบบขัดข้อง: ' . $e->getMessage()]);
        }
    }

    //อัปเดตข้อมูลรายการแข่งขันย่อย
    public function update(Request $request, Competition $competition, CompetitionClass $class)
    {
        // 1. แก้ Validation ให้เป็น allowed_category (เลือกอันเดียว) เหมือนตอน Store
        $request->validate([
            'name' => 'required|string|max:255',
            'entry_fee' => 'required|numeric|min:0',
            'max_members' => 'required|integer|min:1',
            'max_teams' => 'nullable|integer|min:1',
            'rule_pdf' => 'nullable|mimes:pdf', 
            'game_type_name' => 'required|string|max:255',
            'robot_name' => 'required|string|max:255',
            'robot_standard_weight' => 'nullable|numeric|min:0',
            'robot_image' => 'nullable|image|mimes:jpeg,png,jpg',
            'allowed_category' => 'required|string', 
        ]);

        try {
            // ดึงข้อมูลเดิมมาเตรียมอัปเดต
            $data = $request->only([
                'name', 'entry_fee', 'max_members', 'max_teams', 
                'game_type_name', 'robot_name'
            ]);
            
            // 2. จับคู่ชื่อให้ตรง Database
            $data['robot_weight'] = $request->robot_standard_weight; 

            // อัปเดตไฟล์ PDF (ถ้ามีส่งมาใหม่)
            if ($request->hasFile('rule_pdf')) {
                // 3. เปลี่ยน rule_file_path เป็น rules_url
                if ($class->rules_url) {
                    Storage::disk('google')->delete($class->rules_url);
                }
                $data['rules_url'] = $this->uploadClassFile($request->file('rule_pdf'), $competition->name, $request->name, 'Rule');
            }

            // อัปเดตรูปภาพหุ่นยนต์ (ถ้ามีส่งมาใหม่)
            if ($request->hasFile('robot_image')) {
                if ($class->robot_image_url && str_contains($class->robot_image_url, 'Competition_Class')) {
                    Storage::disk('google')->delete($class->robot_image_url);
                }
                $data['robot_image_url'] = $this->uploadClassFile($request->file('robot_image'), $competition->name, $request->name, 'Picture');
            }

            // 4. อัปเดต Snapshot อายุ (แก้ชื่อเป็น allowed_categories และดึงค่าแบบ String)
            $data['allowed_categories'] = Category::where('name', $request->allowed_category)
                ->get(['name', 'min_age', 'max_age'])
                ->toArray();

            $class->update($data);

            return redirect()->back()->with('success', 'อัปเดตข้อมูลรุ่นการแข่งขันเรียบร้อย!');

        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'ไม่สามารถแก้ไขได้: ' . $e->getMessage()]);
        }
    }

    //ลบรายการแข่งขันย่อย
    public function destroy(Competition $competition, CompetitionClass $class)
    {
        try {
            // ลบไฟล์ PDF
            if ($class->rules_url) {
                Storage::disk('google')->delete($class->rules_url);
            }
            // ลบรูปภาพ 
            if ($class->robot_image_url && str_contains($class->robot_image_url, 'Competition_Class')) {
                Storage::disk('google')->delete($class->robot_image_url);
            }

            $class->delete();
            return redirect()->back()->with('success', 'ลบรุ่นการแข่งขันและไฟล์ที่เกี่ยวข้องเรียบร้อยแล้ว!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'ไม่สามารถลบได้: ' . $e->getMessage()]);
        }
    }

    //สำหรับดึงไฟล์ PDF กติกา
    public function showRule(Competition $competition, CompetitionClass $class)
    {
        if (!$class->rules_url || !Storage::disk('google')->exists($class->rules_url)) {
            abort(404);
        }
        $file = Storage::disk('google')->get($class->rules_url);
        $mimeType = Storage::disk('google')->mimeType($class->rules_url);
        return response($file, 200)->header('Content-Type', $mimeType);
    }

    //สำหรับดึงรูปภาพหุ่นยนต์
    public function showPicture(Competition $competition, CompetitionClass $class)
    {
        if (!$class->robot_image_url || !Storage::disk('google')->exists($class->robot_image_url)) {
            return response()->file(public_path('images/default-robot.png')); 
        }
        $file = Storage::disk('google')->get($class->robot_image_url);
        $mimeType = Storage::disk('google')->mimeType($class->robot_image_url);
        return response($file, 200)->header('Content-Type', $mimeType);
    }
}