<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\CompetitionClass;
use App\Models\Category;
use App\Models\RobotModel;
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

    public function index(Competition $competition)
    {
        $classes = $competition->classes()->latest()->paginate(20);
        $categories = Category::limit(100)->get();
        $robotModels = RobotModel::limit(100)->get();
        $gameTypes = GameType::limit(100)->get();

        return view('admin.competitions.classes.index', compact('competition', 'classes', 'categories', 'robotModels', 'gameTypes'));
    }

    public function store(Request $request, Competition $competition)
    {
        if (empty($request->all()) && $request->server('CONTENT_LENGTH') > 0) {
            return back()->withInput()->withErrors(['error' => 'ไฟล์มีขนาดใหญ่เกินไป (ระบบรองรับสูงสุด 50MB)']);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'entry_fee' => 'required|numeric|min:0',
            // 🚀 เพิ่ม min_members และปรับ max_members ให้ต้องมากกว่าหรือเท่ากับ min
            'min_members' => 'required|integer|min:1',
            'max_members' => 'required|integer|min:1|gte:min_members',
            'max_teams' => 'nullable|integer|min:1',
            'rule_pdf' => 'nullable|mimes:pdf|max:51200',
            'game_type_name' => 'required|string|max:255',
            'robot_name' => 'required|string|max:255',
            'robot_weight' => 'nullable|numeric|min:0',
            'robot_image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'robot_model_id' => 'nullable|exists:robot_models,id',
            'master_robot_image_url' => 'nullable|string', 
            'allowed_category' => 'required|string',
        ]);

        try {
            $rulePath = null;
            // ใช้รูปจากแม่แบบเป็นค่าตั้งต้น
            $robotImagePath = $request->master_robot_image_url; 

            if ($request->hasFile('rule_pdf')) {
                if ($request->file('rule_pdf')->isValid()) {
                    $rulePath = $this->uploadClassFile($request->file('rule_pdf'), $competition->name, $request->name, 'rules', 'google');
                }
            }

            if ($request->hasFile('robot_image')) {
                if ($request->file('robot_image')->isValid()) {
                    $robotImagePath = $this->uploadClassFile($request->file('robot_image'), $competition->name, $request->name, 'images', 'public');
                }
            }

            $categoriesSnapshot = Category::where('name', $request->allowed_category)
                ->get(['name', 'min_age', 'max_age'])
                ->toArray();

            CompetitionClass::create([
                'competition_id' => $competition->id,
                'name' => $request->name,
                'entry_fee' => $request->entry_fee,
                // 🚀 เซฟข้อมูล min_members
                'min_members' => $request->min_members,
                'max_members' => $request->max_members,
                'max_teams' => $request->max_teams,
                'rules_url' => $rulePath, 
                'game_type_name' => $request->game_type_name,
                'robot_name' => $request->robot_name,
                'robot_model_id' => $request->robot_model_id,
                'robot_weight' => ($request->robot_weight > 0) ? $request->robot_weight : null, 
                'robot_image_url' => $robotImagePath,
                'allowed_categories' => $categoriesSnapshot, 
            ]);

            return redirect()->back()->with('success', 'เพิ่มรุ่นการแข่งขันเรียบร้อยแล้ว!');

        } catch (\Exception $e) {
            Log::error("Store CompetitionClass Error: " . $e->getMessage());
            $safeError = str_replace(["'", '"'], "", $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'ระบบขัดข้อง: ' . $safeError]);
        }
    }

    public function update(Request $request, Competition $competition, CompetitionClass $class)
    {
        if (empty($request->all()) && $request->server('CONTENT_LENGTH') > 0) {
            return back()->withInput()->withErrors(['error' => 'ไฟล์มีขนาดใหญ่เกินไป']);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'entry_fee' => 'required|numeric|min:0',
            'min_members' => 'required|integer|min:1',
            'max_members' => 'required|integer|min:1|gte:min_members',
            'max_teams' => 'nullable|integer|min:1',
            'rule_pdf' => 'nullable|mimes:pdf|max:51200', 
            'game_type_name' => 'required|string|max:255',
            'robot_name' => 'required|string|max:255',
            'robot_weight' => 'nullable|numeric|min:0',
            'robot_image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'robot_model_id' => 'nullable|exists:robot_models,id',
            'allowed_category' => 'required|string', 
        ]);

        try {
            // 🚀 ดึง min_members ออกมาอัปเดตด้วย
            $data = $request->only(['name', 'entry_fee', 'min_members', 'max_members', 'max_teams', 'game_type_name', 'robot_name', 'robot_model_id']);
            
            $data['robot_weight'] = ($request->robot_weight > 0) ? $request->robot_weight : null;
            
            // จัดการไฟล์ PDF กติกา
            if ($request->hasFile('rule_pdf')) {
                if ($request->file('rule_pdf')->isValid()) {
                    if ($class->rules_url) {
                        Storage::disk('google')->delete($class->rules_url);
                    }
                    $data['rules_url'] = $this->uploadClassFile($request->file('rule_pdf'), $competition->name, $request->name, 'rules', 'google');
                }
            }

            // จัดการรูปภาพหุ่นยนต์
            if ($request->hasFile('robot_image')) {
                if ($request->file('robot_image')->isValid()) {
                    // ลบรูปเก่าทิ้ง (ยกเว้นรูปจาก URL ภายนอก)
                    if ($class->robot_image_url && !str_starts_with($class->robot_image_url, 'http')) {
                        Storage::disk('public')->delete($class->robot_image_url);
                    }
                    $data['robot_image_url'] = $this->uploadClassFile($request->file('robot_image'), $competition->name, $request->name, 'images', 'public');
                }
            }

            $data['allowed_categories'] = Category::where('name', $request->allowed_category)
                ->get(['name', 'min_age', 'max_age'])
                ->toArray();

            $class->update($data);
            return redirect()->back()->with('success', 'อัปเดตข้อมูลเรียบร้อยแล้ว!');

        } catch (\Exception $e) {
            Log::error("Update CompetitionClass Error: " . $e->getMessage());
            $safeError = str_replace(["'", '"'], "", $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'ไม่สามารถแก้ไขได้: ' . $safeError]);
        }
    }

    public function destroy(Competition $competition, CompetitionClass $class)
    {
        try {
            if ($class->rules_url) {
                Storage::disk('google')->delete($class->rules_url);
            }
            
            if ($class->robot_image_url && !str_starts_with($class->robot_image_url, 'http')) {
                Storage::disk('public')->delete($class->robot_image_url);
            }

            $class->delete();
            return redirect()->back()->with('success', 'ลบรุ่นการแข่งขันเรียบร้อยแล้ว!');
        } catch (\Exception $e) {
            Log::error("Delete CompetitionClass Error: " . $e->getMessage());
            return back()->withErrors(['error' => 'ไม่สามารถลบได้: ' . $e->getMessage()]);
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
}