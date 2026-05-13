<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\CompetitionClass;
use App\Models\Category;
use App\Models\GameType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CompetitionClassController extends Controller
{
    private function uploadClassFile($file, $compName, $className, $type, $disk = 'public')
    {
        $safeCompName = $this->sanitizeFolderName($compName);
        $safeClassName = $this->sanitizeFolderName($className);
        
        $ext = $file->getClientOriginalExtension();
        $fileName = strtolower($type) . "_" . time() . "_" . uniqid() . "." . $ext;
        
        // Path: competitions/ชื่อการแข่งขัน/classes/ชื่อรุ่น/ประเภทไฟล์
        $folderPath = "competitions/{$safeCompName}/classes/{$safeClassName}/{$type}";
        
        return $file->storeAs($folderPath, $fileName, $disk);
    }

    private function sanitizeFolderName($name)
    {
        return trim(preg_replace('/[\\\\\/:\*\?"<>\|]/', '_', $name));
    }

public function index(Request $request, Competition $competition) // อย่าลืมเติม Request $request เข้ามาในวงเล็บด้วยนะครับ
    {
        // 1. เริ่มสร้าง Query พื้นฐาน
        $query = $competition->classes()->latest();

        // 2. ถ้ามีการค้นหาด้วยข้อความ (ชื่อรุ่น)
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // 3. ถ้ามีการเลือกกรองประเภทเกม
        if ($request->filled('game_type')) {
            $query->where('game_type_name', $request->game_type);
        }

        // 4. ทำ Pagination และใช้ appends() เพื่อให้จำค่าค้นหาเวลาเปลี่ยนหน้า (Page 2, 3...)
        $classes = $query->paginate(20)->appends($request->query());

        // ดึงข้อมูลสำหรับ Dropdown ใน Modal และตัวกรอง
        $categories = Category::limit(100)->get();
        $gameTypes = GameType::limit(100)->get();

        return view('admin.competitions.classes.index', compact('competition', 'classes', 'categories', 'gameTypes'));
    }

    public function store(Request $request, Competition $competition)
    {
        if (empty($request->all()) && $request->server('CONTENT_LENGTH') > 0) {
            return back()->withInput()->withErrors(['error' => 'ไฟล์มีขนาดใหญ่เกินไป (ระบบรองรับสูงสุด 200MB)']);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'entry_fee' => 'required|numeric|min:0',
            'min_members' => 'required|integer|min:1',
            'max_members' => 'required|integer|min:1|gte:min_members',
            'max_teams' => 'nullable|integer|min:1',
            'rule_pdf' => 'nullable|mimes:pdf|max:204800',
            'game_type_name' => 'required|string|max:255',
            'robot_weight' => 'nullable|numeric|min:0',
            'allowed_category' => 'required|string',
        ]);

        try {
            $rulePath = null;

            if ($request->hasFile('rule_pdf')) {
                if ($request->file('rule_pdf')->isValid()) {
                    $rulePath = $this->uploadClassFile($request->file('rule_pdf'), $competition->name, $request->name, 'rules', 'google');
                }
            }

            $categoriesSnapshot = Category::where('name', $request->allowed_category)
                ->get(['name', 'min_age', 'max_age'])
                ->toArray();

            CompetitionClass::create([
                'competition_id' => $competition->id,
                'name' => $request->name,
                'entry_fee' => $request->entry_fee,
                'min_members' => $request->min_members,
                'max_members' => $request->max_members,
                'max_teams' => $request->max_teams,
                'rules_url' => $rulePath, 
                'game_type_name' => $request->game_type_name,
                'robot_weight' => ($request->robot_weight > 0) ? $request->robot_weight : null, 
                
                // Set unused robot fields to null
                'robot_name' => '',
                'robot_image_url' => null,
                
                'allowed_categories' => $categoriesSnapshot, 
            ]);

            return redirect()->back()->with('success', 'เพิ่มรุ่นการแข่งขันเรียบร้อยแล้ว!');

        } catch (\Exception $e) {
    
			//dd('เจอตัวการแล้ว! สาเหตุคือ: ', $e->getMessage());
            Log::error("Store CompetitionClass Error: " . $e->getMessage());
            
            return back()->withInput()->withErrors(['error' => 'ไม่สามารถบันทึกข้อมูลได้ กรุณาตรวจสอบความถูกต้องหรือติดต่อผู้ดูแลระบบ']);
        }
    }

   
    public function update(Request $request, Competition $competition, CompetitionClass $class)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'entry_fee' => 'required|numeric|min:0',
            'min_members' => 'required|integer|min:1',
            'max_members' => 'required|integer|min:1|gte:min_members',
            'game_type_name' => 'required|string|max:255',
            'allowed_category' => 'required|string', 
            'rule_pdf' => 'nullable|mimes:pdf|max:204800',
        ]);

        try {
            $data = $request->only(['name', 'entry_fee', 'min_members', 'max_members', 'max_teams', 'game_type_name']);
            $data['robot_weight'] = ($request->robot_weight > 0) ? $request->robot_weight : null;

            if ($request->hasFile('rule_pdf') && $request->file('rule_pdf')->isValid()) {
                if ($class->rules_url) {
                    Storage::disk('google')->delete($class->rules_url);
                }
                $data['rules_url'] = $this->uploadClassFile($request->file('rule_pdf'), $competition->name, $request->name, 'rules', 'google');
            }

            $data['allowed_categories'] = Category::where('name', $request->allowed_category)
                ->get(['name', 'min_age', 'max_age'])
                ->toArray();

            $class->update($data);
            
            return redirect()->back()->with('success', 'อัปเดตข้อมูลเรียบร้อยแล้ว!');

        } catch (\Exception $e) {
            Log::error("Update CompetitionClass Error: " . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'ไม่สามารถแก้ไขข้อมูลได้ กรุณาลองใหม่อีกครั้ง']);
        }
    }

    public function showRule(Competition $competition, CompetitionClass $class)
    {
        $disk = Storage::disk('google');
        $path = $class->rules_url;

        if (!$path || !$disk->exists($path)) {
            abort(404, 'ไม่พบไฟล์กติกาการแข่งขัน');
        }

        $mimeType = $disk->mimeType($path) ?? 'application/pdf';

        return response()->stream(function () use ($disk, $path) {
            if (ob_get_level() > 0) ob_end_clean();
            $stream = $disk->readStream($path);
            if ($stream) {
                fpassthru($stream);
                fclose($stream);
            }
        }, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="rules.pdf"',
            'Cache-Control' => 'public, max-age=3600'
        ]);
    }


public function destroy(Competition $competition, CompetitionClass $class)
    {
        try {
            if ($class->rules_url) {
                // เช็คว่ามี Record อื่นในตารางที่ใช้ไฟล์นี้อยู่หรือไม่ (ไม่นับตัวมันเอง)
                $isSharedFile = CompetitionClass::where('rules_url', $class->rules_url)
                    ->where('id', '!=', $class->id)
                    ->exists();

                // ถ้าไม่มีใครใช้ไฟล์นี้แล้ว ถึงจะสั่งลบไฟล์จริงๆ
                if (!$isSharedFile) {
                    Storage::disk('google')->delete($class->rules_url);
                }
            }

            // ลบข้อมูลรุ่นการแข่งขันออกจากฐานข้อมูลตามปกติ
            $class->delete();

            return redirect()->back()->with('success', 'ลบรุ่นการแข่งขันเรียบร้อยแล้ว!');

        } catch (\Exception $e) {
            Log::error("Delete CompetitionClass Error: " . $e->getMessage());
            return back()->withErrors(['error' => 'ไม่สามารถลบข้อมูลได้ กรุณาลองใหม่อีกครั้ง']);
        }
    }


    public function toggleStatus(Competition $competition, CompetitionClass $class)
    {
        try {
            // สลับสถานะ (ถ้า true ให้เป็น false, ถ้า false ให้เป็น true)
            $class->update(['is_active' => !$class->is_active]);

            $statusMessage = $class->is_active ? 'เปิดรับสมัคร' : 'ปิดรับสมัคร';
            return redirect()->back()->with('success', "เปลี่ยนสถานะเป็น {$statusMessage} เรียบร้อยแล้ว!");

        } catch (\Exception $e) {
            Log::error("Toggle Class Status Error: " . $e->getMessage());
            return back()->withErrors(['error' => 'ไม่สามารถเปลี่ยนสถานะได้ กรุณาลองใหม่อีกครั้ง']);
        }
    }
}