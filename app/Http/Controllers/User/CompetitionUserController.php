<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\CompetitionClass;
use Illuminate\Support\Facades\Storage;
use App\Models\Registration;
use App\Models\Team;
use Carbon\Carbon;
use App\Mail\RegistrationSubmittedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; 

class CompetitionUserController extends Controller
{
    public function index()
    {
        $competitions = Competition::where('status', '!=', 'draft')
            ->with(['classes']) 
            ->withCount(['registrations' => function($query) {
                $query->where('status', '!=', 'rejected');
            }])
            ->orderBy('event_start_date', 'asc')
            ->paginate(10);

        return view('user.dashboard', compact('competitions'));
    }

    public function show($id)
    {
        $competition = Competition::with(['classes'])
            ->where('status', '!=', 'draft') 
            ->findOrFail($id);

        $myTeams = auth()->user()->teams()->with('members')->get();

        return view('user.show', compact('competition', 'myTeams'));
    }

    public function register(Request $request, $compId, $classId)
    {
        $request->validate([
            'team_id' => 'required|exists:teams,id'
        ], [
            'team_id.required' => 'กรุณาเลือกทีมที่ต้องการส่งเข้าแข่งขัน'
        ]);

        $user = auth()->user();
        $team = Team::with('members')->findOrFail($request->team_id);
        $competition = Competition::findOrFail($compId);
        $class = CompetitionClass::where('competition_id', $compId)->findOrFail($classId);

        if ($competition->dynamic_status !== 'open') {
            return back()->with('error', 'ไม่อยู่ในช่วงเวลาที่เปิดรับสมัคร กรุณาตรวจสอบกำหนดการแข่งขันอีกครั้ง');
        }

        if ($team->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $alreadyRegistered = Registration::where('competition_class_id', $classId)
            ->where('team_id', $team->id)
            ->where('status', '!=', 'rejected')
            ->exists();

        if ($alreadyRegistered) {
            return back()->with('error', 'ทีมของคุณได้ทำการสมัครเข้าแข่งขันในรุ่นนี้ไปเรียบร้อยแล้ว');
        }

        $memberCount = $team->members->count();
        $min = $class->min_members ?? 1; // เผื่อกรณีค่าว่าง
        $max = $class->max_members;

        if ($memberCount < $min || $memberCount > $max) {
            if ($min === $max) {
                return back()->with('error', "จำนวนสมาชิกในทีมต้องมี {$max} คนพอดี");
            } else {
                return back()->with('error', "จำนวนสมาชิกในทีมต้องอยู่ระหว่าง {$min} - {$max} คน");
            }
        }

        $allowedCategories = is_string($class->allowed_categories) 
                            ? json_decode($class->allowed_categories, true) 
                            : $class->allowed_categories;
        
        if (!empty($allowedCategories)) {
            $minAllowed = collect($allowedCategories)->min('min_age');
            $maxAllowed = collect($allowedCategories)->max('max_age');

            foreach ($team->members as $member) {
                if (!$member->birth_date) {
                    return back()->with('error', 'กรุณาระบุวันเกิดของสมาชิกทุกคนในทีมให้ครบถ้วนในเมนูจัดการทีม');
                }

                $age = Carbon::parse($member->birth_date)->age; 

                if ($age < $minAllowed || $age > $maxAllowed) {
                    return back()->with('error', "อายุของ {$member->first_name_th} ({$age} ปี) ไม่เข้าเกณฑ์ของรุ่นนี้ ({$minAllowed}-{$maxAllowed} ปี)");
                }
            }
        }

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

        try {
            Mail::to($user->email)->send(new RegistrationSubmittedMail($registration));
        } catch (\Exception $e) {
            Log::error('ไม่สามารถส่งอีเมลยืนยันการสมัครได้: ' . $e->getMessage());
        }

        return redirect()->route('user.dashboard')
            ->with('success', "สมัครแข่งขันสำเร็จ!<br><br>รหัสใบสมัคร: <strong>{$regisNo}</strong><br>สถานะปัจจุบัน: รอชำระเงินค่าสมัคร");
    }

    public function myRegistrations()
    {
        $registrations = Registration::with(['competition', 'competitionClass', 'team.members'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.registrations', compact('registrations'));
    }

    public function uploadSlip(Request $request, $id)
    {
        // 1. ตรวจสอบขนาดไฟล์เบื้องต้น
        if (empty($request->all()) && $request->server('CONTENT_LENGTH') > 0) {
            return back()->with('error', 'ไฟล์มีขนาดใหญ่เกินไป (ระบบรองรับสูงสุด 5MB)');
        }

        // 2. Validate ความถูกต้องของไฟล์
        $request->validate([
            'payment_slip' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ], [
            'payment_slip.required' => 'กรุณาอัปโหลดรูปภาพหลักฐานการโอนเงิน',
            'payment_slip.image' => 'ไฟล์ที่อัปโหลดต้องเป็นรูปภาพเท่านั้น',
            'payment_slip.mimes' => 'รองรับเฉพาะไฟล์รูปภาพนามสกุล JPEG, PNG, JPG เท่านั้น',
            'payment_slip.max' => 'ขนาดไฟล์รูปภาพต้องไม่เกิน 5MB',
        ]);

        // 3. ดึงข้อมูลใบสมัคร
        $registration = Registration::with(['competition'])->where('user_id', auth()->id())->findOrFail($id);

        // 4. เช็คว่าสถานะปัจจุบันอนุญาตให้อัปโหลดได้หรือไม่
        if (!in_array($registration->status, ['pending_payment', 'rejected'])) {
            return back()->with('error', 'ใบสมัครนี้ไม่สามารถอัปโหลดสลิปได้ในขณะนี้');
        }

        if ($request->hasFile('payment_slip')) {
            try {
                $file = $request->file('payment_slip');
                
                if (!$file->isValid()) {
                    return back()->with('error', 'ไฟล์รูปภาพไม่สมบูรณ์ กรุณาลองใหม่อีกครั้ง');
                }

                // 🚀 ส่วนที่หายไป: การสร้างชื่อโฟลเดอร์และชื่อไฟล์
                $safeCompName = preg_replace('/[^A-Za-z0-9ก-๙\-\s]/u', '', $registration->competition->name);
                $safeCompName = str_replace(' ', '-', trim($safeCompName));
                
                $folderPath = "{$safeCompName}/team_{$registration->team_id}/slips";
                $filename = "slip_" . $registration->regis_no . '_' . time() . '.' . $file->getClientOriginalExtension();

                // 5. ลบสลิปเก่าทิ้งก่อน (ถ้ามี) เพื่อประหยัดพื้นที่ Google Drive
                if ($registration->payment_slip_path && Storage::disk('google_secure')->exists($registration->payment_slip_path)) {
                    Storage::disk('google_secure')->delete($registration->payment_slip_path);
                }

                // 6. อัปโหลดไฟล์ใหม่ขึ้น Google Drive
                $path = $file->storeAs($folderPath, $filename, 'google_secure');

                if (!$path) {
                    Log::error("Secure Upload Failed: Regis No {$registration->regis_no}");
                    return back()->with('error', 'ระบบ Cloud Storage ขัดข้อง ไม่สามารถอัปโหลดไฟล์ได้');
                }

                // 7. อัปเดตฐานข้อมูล: ล้างค่าเก่าที่โดน Reject ทิ้งให้หมด
                $registration->update([
                    'payment_slip_path' => $path, 
                    'status' => 'waiting_verify', // กลับไปรอตรวจสอบ
                    'reject_reason' => null,      // ล้างเหตุผลที่เคยโดนปฏิเสธ
                    'verified_by' => null,        // ล้างชื่อคนตรวจเก่า
                    'verified_at' => null         // ล้างวันที่ตรวจเก่า
                ]);

                return back()->with('success', 'ส่งหลักฐานการชำระเงินเรียบร้อยแล้ว กรุณารอทีมงานตรวจสอบอีกครั้งครับ');

            } catch (\Exception $e) {
                // บันทึก Error ลง Log เพื่อให้เรามาไล่ดูได้ภายหลัง
                Log::error("Upload Exception: " . $e->getMessage());
                return back()->with('error', 'เกิดข้อผิดพลาดในการเชื่อมต่อระบบไฟล์: ' . $e->getMessage());
            }
        }

        return back()->with('error', 'ไม่พบไฟล์แนบ');
    }

    public function showSlip($id)
    {
        $registration = Registration::where('user_id', auth()->id())->findOrFail($id);
        
        if (!$registration->payment_slip_path) {
            abort(404, 'ไม่พบไฟล์สลิปโอนเงิน');
        }

        $disk = Storage::disk('google_secure');
        $path = $registration->payment_slip_path;

        if (!$disk->exists($path)) {
            abort(404, 'ไฟล์สลิปถูกลบหรือสูญหาย');
        }

        $mimeType = $disk->mimeType($path) ?? 'image/jpeg';
        
        return response()->stream(function () use ($disk, $path) {
            if (ob_get_level() > 0) ob_end_clean();
            $stream = $disk->readStream($path);
            fpassthru($stream);
            if (is_resource($stream)) fclose($stream);
        }, 200, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'private, max-age=3600' 
        ]);
    }

    public function showRule($compId, $classId)
    {
        $class = CompetitionClass::where('competition_id', $compId)->findOrFail($classId);
        
        $disk = Storage::disk('google'); 
        $path = $class->rules_url;

        if (!$path || !$disk->exists($path)) {
            abort(404, 'ไม่พบไฟล์กติกาการแข่งขัน หรือไฟล์ถูกลบไปแล้ว');
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


    public function eTicket($id)
    {
        // ดึงข้อมูลใบสมัคร (ต้องเป็นของตัวเอง และต้อง "อนุมัติแล้ว" เท่านั้น)
        $registration = Registration::with(['competition', 'competitionClass', 'team.members', 'user'])
            ->where('user_id', auth()->id())
            ->where('status', 'approved')
            ->findOrFail($id);

        return view('user.e-ticket', compact('registration'));
    }
}