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

class CompetitionClassController extends Controller
{
    /**
     * Helper: อัปโหลดไฟล์ขึ้น Google Drive (Stream Upload)
     */
    private function uploadClassFile($file, $compName, $className, $type)
    {
        $safeCompName = $this->sanitizeFolderName($compName);
        $safeClassName = $this->sanitizeFolderName($className);
        
        $ext = $file->getClientOriginalExtension();
        $fileName = strtolower($type) . "_" . time() . "_" . uniqid() . "." . $ext;
        
        $fullPath = "Competitions/{$safeCompName}/Competition_Class/{$safeClassName}/{$type}/$fileName";
        
        $stream = fopen($file->getRealPath(), 'r');
        Storage::disk('google')->put($fullPath, $stream);
        if (is_resource($stream)) {
            fclose($stream);
        }
        return $fullPath;
    }

    private function sanitizeFolderName($name)
    {
        return trim(preg_replace('/[\\\\\/:\*\?"<>\|]/', '_', $name));
    }

    /**
     * 🏆 หน้าหลักรายการย่อย (เพิ่ม Pagination และ Limit เพื่อประหยัด RAM)
     */
    public function index(Competition $competition)
    {
        // ใช้ paginate แทน get เพื่อรองรับ Octane
        $classes = $competition->classes()->latest()->paginate(20);
        
        // จำกัดการดึง Master Data
        $categories = Category::limit(100)->get();
        $robotModels = RobotModel::limit(100)->get();
        $gameTypes = GameType::limit(100)->get();

        return view('admin.competitions.classes.index', compact('competition', 'classes', 'categories', 'robotModels', 'gameTypes'));
    }

    /**
     * บันทึกรุ่นการแข่งขันใหม่
     */
    public function store(Request $request, Competition $competition)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'entry_fee' => 'required|numeric|min:0',
            'max_members' => 'required|integer|min:1',
            'max_teams' => 'nullable|integer|min:1',
            'rule_pdf' => 'nullable|mimes:pdf|max:51200', 
            'game_type_name' => 'required|string|max:255',
            'robot_name' => 'required|string|max:255',
            'robot_weight' => 'nullable|numeric|min:0',
            'robot_image' => 'nullable|image|mimes:jpeg,png,jpg',
            'master_robot_image_url' => 'nullable|string', 
            'allowed_category' => 'required|string',
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
                'robot_weight' => $request->robot_weight, 
                'robot_image_url' => $robotImagePath,
                'allowed_categories' => $categoriesSnapshot, 
            ]);

            return redirect()->back()->with('success', 'เพิ่มรุ่นการแข่งขันเรียบร้อยแล้ว!');

        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'ระบบขัดข้อง: ' . $e->getMessage()]);
        }
    }

    /**
     * อัปเดตข้อมูลรุ่นการแข่งขัน
     */
    public function update(Request $request, Competition $competition, CompetitionClass $class)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'entry_fee' => 'required|numeric|min:0',
            'max_members' => 'required|integer|min:1',
            'max_teams' => 'nullable|integer|min:1',
            'rule_pdf' => 'nullable|mimes:pdf|max:51200', 
            'game_type_name' => 'required|string|max:255',
            'robot_name' => 'required|string|max:255',
            'robot_weight' => 'nullable|numeric|min:0',
            'robot_image' => 'nullable|image|mimes:jpeg,png,jpg',
            'allowed_category' => 'required|string', 
        ]);

        try {
            $data = $request->only(['name', 'entry_fee', 'max_members', 'max_teams', 'game_type_name', 'robot_name', 'robot_weight']);
            
            if ($request->hasFile('rule_pdf')) {
                if ($class->rules_url) {
                    Storage::disk('google')->delete($class->rules_url);
                }
                $data['rules_url'] = $this->uploadClassFile($request->file('rule_pdf'), $competition->name, $request->name, 'Rule');
            }

            if ($request->hasFile('robot_image')) {
                if ($class->robot_image_url && str_contains($class->robot_image_url, 'Competition_Class')) {
                    Storage::disk('google')->delete($class->robot_image_url);
                }
                $data['robot_image_url'] = $this->uploadClassFile($request->file('robot_image'), $competition->name, $request->name, 'Picture');
            }

            $data['allowed_categories'] = Category::where('name', $request->allowed_category)
                ->get(['name', 'min_age', 'max_age'])
                ->toArray();

            $class->update($data);
            return redirect()->back()->with('success', 'อัปเดตข้อมูลรุ่นการแข่งขันเรียบร้อย!');

        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'ไม่สามารถแก้ไขได้: ' . $e->getMessage()]);
        }
    }

    /**
     * ลบรุ่นการแข่งขัน
     */
    public function destroy(Competition $competition, CompetitionClass $class)
    {
        try {
            if ($class->rules_url) {
                Storage::disk('google')->delete($class->rules_url);
            }
            if ($class->robot_image_url && str_contains($class->robot_image_url, 'Competition_Class')) {
                Storage::disk('google')->delete($class->robot_image_url);
            }

            $class->delete();
            return redirect()->back()->with('success', 'ลบรุ่นการแข่งขันเรียบร้อยแล้ว!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'ไม่สามารถลบได้: ' . $e->getMessage()]);
        }
    }

    /**
     * สำหรับดึงไฟล์ PDF กติกา (กู้คืน Syntax ที่พัง)
     */
    public function showRule(Competition $competition, CompetitionClass $class)
    {
        $disk = Storage::disk('google');
        $path = $class->rules_url;

        if (!$path || !$disk->exists($path)) {
            abort(404);
        }

        $file = $disk->get($path);
        
        return response($file, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="rules_' . $class->id . '.pdf"');
    }

    /**
     * สำหรับดึงรูปภาพหุ่นยนต์ (กู้คืน Syntax ที่พัง)
     */
    public function showPicture(Competition $competition, CompetitionClass $class)
    {
        $disk = Storage::disk('google');
        $path = $class->robot_image_url;

        if (!$path || !$disk->exists($path)) {
            return response()->file(public_path('images/default-robot.png')); 
        }

        $file = $disk->get($path);
        $mimeType = $disk->mimeType($path) ?? 'image/jpeg';
        
        return response($file, 200)->header('Content-Type', $mimeType);
    }
}