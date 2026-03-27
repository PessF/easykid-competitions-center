<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\CompetitionClass;
use Illuminate\Support\Facades\Storage;
use App\Models\Registration;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CompetitionUserController extends Controller
{
    public function index()
    {
        $competitions = Competition::whereIn('status', ['registration', 'ongoing'])
            ->with(['classes']) 
            ->withCount(['registrations' => function($query) {
                $query->where('status', '!=', 'rejected');
            }])
            ->orderBy('event_start_date', 'asc')
            ->get();

        return view('user.dashboard', compact('competitions'));
    }

public function show($id)
    {
        // 1. ดึงข้อมูลงานแข่งและรุ่นการแข่งขัน
        $competition = Competition::with(['classes'])
            ->where('status', '!=', 'draft') 
            ->findOrFail($id);

        $myTeams = auth()->user()->teams()->with('members')->get();

        return view('user.show', compact('competition', 'myTeams'));

    }

    /**
     * ดึงรูปแบนเนอร์จาก Google Drive
     */
    /**
     * ดึงรูปแบนเนอร์จาก Google Drive
     */
    public function banner($id)
    {
        $competition = Competition::findOrFail($id);
        
        // ถ้าไม่มีรูป ให้โชว์ 404
        if (!$competition->banner_url) abort(404);

        $disk = Storage::disk('google');
        $path = $competition->banner_url;
        
        if (!$disk->exists($path)) abort(404);

        $file = $disk->get($path);
        $mimeType = $disk->mimeType($path) ?? 'image/jpeg';
        
        return response($file, 200)->header('Content-Type', $mimeType);
    }

    /**
     * ดึงรูปหุ่นยนต์ในคลาสแข่งขัน
     */
    public function classPicture($compId, $classId)
    {
        $class = CompetitionClass::where('competition_id', $compId)->findOrFail($classId);
        
        if (!$class->robot_image_url) abort(404);

        $disk = Storage::disk('google');
        $path = $class->robot_image_url;
        
        if (!$disk->exists($path)) abort(404);

        $file = $disk->get($path);
        $mimeType = $disk->mimeType($path) ?? 'image/jpeg';
        
        return response($file, 200)->header('Content-Type', $mimeType);
    }

    /**
     * ดึงไฟล์กติกา PDF
     */
    public function rules($compId, $classId)
    {
        $class = CompetitionClass::where('competition_id', $compId)->findOrFail($classId);
        
        if (!$class->rules_url) abort(404);

        $disk = Storage::disk('google');
        $path = $class->rules_url;

        if (!$disk->exists($path)) abort(404);

        $file = $disk->get($path);
        
        return response($file, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="rules_' . $classId . '.pdf"');
    }

    public function register(Request $request, $compId, $classId)
    {
        // 1. รับค่าและเช็คพื้นฐาน
        $request->validate([
            'team_id' => 'required|exists:teams,id'
        ], [
            'team_id.required' => 'กรุณาเลือกทีมที่ต้องการส่งเข้าแข่งขัน'
        ]);

        $user = auth()->user();
        $team = Team::with('members')->findOrFail($request->team_id);
        $competition = Competition::findOrFail($compId);
        $class = CompetitionClass::where('competition_id', $compId)->findOrFail($classId);

        // ป้องกันการส่ง ID ทีมคนอื่นมาสมัคร
        if ($team->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        // ป้องกันการกดสมัครทีมเดิมซ้ำในรุ่นเดียวกัน
        $alreadyRegistered = Registration::where('competition_class_id', $classId)
            ->where('team_id', $team->id)
            ->where('status', '!=', 'rejected')
            ->exists();

        if ($alreadyRegistered) {
            return back()->with('error', 'ทีมของคุณได้ทำการสมัครเข้าแข่งขันในรุ่นนี้ไปเรียบร้อยแล้ว');
        }

        // ช็คโควตางานแข่งว่าเต็มหรือยัง
        if ($class->max_teams !== null) {
            $currentTeams = Registration::where('competition_class_id', $classId)
                ->where('status', '!=', 'rejected')
                ->count();

            if ($currentTeams >= $class->max_teams) {
                return back()->with('error', 'ขออภัย รุ่นการแข่งขันนี้มีทีมสมัครเต็มโควตาแล้ว');
            }
        }

        // เช็คจำนวนลูกทีม
        if ($class->max_members !== null && $team->members->count() > $class->max_members) {
            return back()->with('error', "จำนวนสมาชิกในทีมเกินกว่าที่รุ่นนี้กำหนด (สูงสุด {$class->max_members} คน)");
        }

        // แปลง JSON เกณฑ์อายุกลับมาเป็น Array ก่อน (กรณีที่ถูกเก็บเป็น String)
        $allowedCategories = is_string($class->allowed_categories) 
                            ? json_decode($class->allowed_categories, true) 
                            : $class->allowed_categories;
        
        if (!empty($allowedCategories)) {
            // หาอายุต่ำสุด และสูงสุดที่รุ่นนี้รับได้
            $minAllowed = collect($allowedCategories)->min('min_age');
            $maxAllowed = collect($allowedCategories)->max('max_age');

            foreach ($team->members as $member) {
                if (!$member->birth_date) {
                    return back()->with('error', 'กรุณาระบุวันเกิดของสมาชิกทุกคนในทีมให้ครบถ้วนในเมนูจัดการทีม');
                }

                // ใช้ Carbon คำนวณอายุแบบแม่นยำ 100% (นับถึงวันปัจจุบัน)
                $age = Carbon::parse($member->birth_date)->age; 

                // ONE FAIL = ALL FAIL (หลุด 1 คน เตะกลับทันที)
                if ($age < $minAllowed || $age > $maxAllowed) {
                    return back()->with('error', "อายุของ {$member->first_name_th} ({$age} ปี) ไม่เข้าเกณฑ์ของรุ่นนี้ ({$minAllowed}-{$maxAllowed} ปี)");
                }
            }
        }

        // สร้างรหัส Regis No อัตโนมัติ: REG-COMP-CLASS-YYYYMMDD-XXXX
        $datePrefix = now()->format('Ymd');
        $latestRegisToday = Registration::whereDate('created_at', now()->toDateString())->count() + 1;
        $runningNumber = str_pad($latestRegisToday, 4, '0', STR_PAD_LEFT);
        $regisNo = "REG-{$compId}-{$classId}-{$datePrefix}-{$runningNumber}";

        $registration = Registration::create([
            'regis_no' => $regisNo,
            'user_id' => $user->id,
            'team_id' => $team->id,
            'competition_id' => $compId,
            'competition_class_id' => $classId,
            'status' => 'pending_payment',
        ]);

        // พาผู้ใช้กลับไปที่ Dashboard พร้อมโชว์ SweetAlert ว่าสำเร็จ! 
        // (เดี๋ยวอนาคตเราจะเปลี่ยนไปพาเข้าหน้า Upload Slip แทนครับ)
        return redirect()->route('user.dashboard')
            ->with('success', "สมัครแข่งขันสำเร็จ! รหัสใบสมัครของคุณคือ {$regisNo} สถานะปัจจุบัน: รอชำระเงินค่าสมัคร");
    }
}