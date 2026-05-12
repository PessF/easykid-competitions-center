<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\CompetitionClass;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Storage;
use App\Models\Registration;
use App\Models\Team;
use Carbon\Carbon;
use App\Mail\RegistrationSubmittedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB; 

class CompetitionUserController extends Controller
{
    public function index()
    {
        $competitions = Competition::where('status', '!=', 'draft')
            ->with(['classes' => function($query) {
                $query->select('id', 'competition_id', 'name', 'entry_fee');
            }]) 
            ->withCount(['registrations' => function($query) {
                $query->where('status', '!=', 'rejected');
            }])
            ->orderBy('event_start_date', 'asc')
            ->paginate(10);

        return view('user.dashboard', compact('competitions'));
    }

    public function show($id)
    {
        $competition = Competition::with(['classes' => function($query) {
                $query->withCount(['registrations' => function($q) {
                    // 🚀 1. นับโควต้าเฉพาะคนที่จ่ายเงินแล้ว หรือรอตรวจสอบสลิปเท่านั้น
                    $q->whereIn('status', ['waiting_verify', 'approved']);
                }]);
            }])
            ->where('status', '!=', 'draft') 
            ->findOrFail($id);

        $myTeams = auth()->user()->teams()
            ->select('id', 'user_id', 'name', 'school_name')
            ->with(['members:id,team_id,first_name_th,last_name_th,birth_date'])
            ->get();

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
        $team = Team::with('members')->select('id', 'user_id')->findOrFail($request->team_id);
        
        $competition = Competition::findOrFail($compId);
        $competitionClass = CompetitionClass::findOrFail($classId); 
        
        if ($competition->dynamic_status !== 'open') {
            return back()->with('error', 'ไม่อยู่ในช่วงเวลาที่เปิดรับสมัคร กรุณาตรวจสอบกำหนดการแข่งขันอีกครั้ง');
        }

        if ($team->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        // Backend Validation: ตรวจสอบจำนวนสมาชิกในทีม
        $memberCount = $team->members->count();
        if ($memberCount < $competitionClass->min_members || $memberCount > $competitionClass->max_members) {
            return back()->with('error', 'จำนวนสมาชิกในทีมไม่ตรงตามเงื่อนไขของรุ่นการแข่งขัน');
        }

        // Backend Validation: ตรวจสอบอายุสมาชิกทุกคนในทีม (คำนวณเทียบกับ "วันแข่งขันจริง")
        $categories = $competitionClass->allowed_categories ?? [];
        if (!empty($categories)) {
            $minAge = collect($categories)->min('min_age');
            $maxAge = collect($categories)->max('max_age');
            
            $baseDate = $competition->event_start_date ? Carbon::parse($competition->event_start_date) : now();
            
            foreach ($team->members as $member) {
                if (empty($member->birth_date)) {
                    return back()->with('error', 'ไม่สามารถสมัครได้: มีสมาชิกในทีมบางคนยังไม่ได้ระบุวัน/เดือน/ปีเกิด');
                }
                
                $birthDate = Carbon::parse($member->birth_date);
                
                $ageAtEvent = $birthDate->diffInYears($baseDate, false);
                $finalAge = floor($ageAtEvent);
                
                if ($finalAge < $minAge || $finalAge > $maxAge) {
                    $eventName = $competition->event_start_date ? 'วันแข่งขัน (' . $baseDate->translatedFormat('d M Y') . ')' : 'วันนี้';
                    return back()->with('error', "ไม่สามารถสมัครได้: มีสมาชิกในทีมอายุไม่เข้าเกณฑ์ที่กำหนด ({$minAge} - {$maxAge} ปี) ปัจจุบันอายุ {$finalAge} ปี ณ {$eventName}");
                }
            }
        }

        // ลอจิกป้องกันการสมัครซ้ำ 
        $alreadyRegistered = Registration::where('competition_class_id', $classId)
            ->where('team_id', $team->id)
            ->whereIn('status', ['pending_payment', 'waiting_verify', 'approved']) 
            ->exists();

        if ($alreadyRegistered) {
            return back()->with('error', 'ทีมของคุณมีชื่ออยู่ในระบบการรับสมัครรุ่นนี้อยู่แล้ว (กำลังรอชำระเงิน / รอตรวจสอบ / หรืออนุมัติแล้ว)');
        }

        // ลอจิกการสร้างใบสมัคร และรันเลขที่ใบสมัครอย่างปลอดภัย (Lock Table)
        DB::beginTransaction();
        try {
            // 🚀 2. Lock Table ตรวจสอบโควต้า max_teams ก่อนรันเลขใบสมัคร
            $lockedClass = CompetitionClass::lockForUpdate()->findOrFail($classId);
            
            if (!is_null($lockedClass->max_teams) && $lockedClass->max_teams > 0) {
                // ดึงจำนวนทีมที่ "จ่ายเงินแล้ว หรือรอตรวจสอบ" เพื่อมาเช็คโควต้า
                $currentRegistrations = Registration::where('competition_class_id', $classId)
                    ->whereIn('status', ['waiting_verify', 'approved'])
                    ->count();

                if ($currentRegistrations >= $lockedClass->max_teams) {
                    DB::rollBack();
                    return back()->with('error', 'ไม่สามารถสมัครได้: รุ่นการแข่งขันนี้มีทีมจองสิทธิ์ (ชำระเงิน) เต็มจำนวนแล้ว');
                }
            }

            $datePrefix = now()->format('Ymd');
            
            $latestRegistration = Registration::whereDate('created_at', now()->toDateString())
                ->lockForUpdate()
                ->orderBy('id', 'desc')
                ->first();

            if ($latestRegistration) {
                $parts = explode('-', $latestRegistration->regis_no);
                $nextNumber = (int) end($parts) + 1;
            } else {
                $nextNumber = 1;
            }

            $runningNumber = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            $regisNo = "REG-{$compId}-{$classId}-{$datePrefix}-{$runningNumber}";

            Registration::create([
                'regis_no' => $regisNo,
                'user_id' => $user->id,
                'team_id' => $team->id,
                'competition_id' => $compId,
                'competition_class_id' => $classId,
                'status' => 'pending_payment',
            ]);

            DB::commit();

            // 🚀 3. เพิ่มข้อความแจ้งเตือนกระตุ้นให้รีบจ่ายเงิน
            $successMessage = "สมัครแข่งขันสำเร็จ!<br><br>";
            $successMessage .= "รหัสใบสมัคร: <strong>{$regisNo}</strong><br>";
            $successMessage .= "สถานะปัจจุบัน: เพิ่มลงตะกร้ารอชำระเงินแล้ว<br><br>";
            $successMessage .= "<span style='color: red;'><strong>⚠️ คำเตือน:</strong> กรุณารีบชำระเงินเพื่อรักษาสิทธิ์ของคุณ เนื่องจากระบบจะนับโควต้าที่นั่งให้กับทีมที่แจ้งชำระเงินแล้วเท่านั้น หากโควต้าเต็มก่อนที่คุณจะชำระเงิน สิทธิ์ของคุณจะถูกยกเลิกทันที</span>";

            return redirect()->route('user.competitions.show', $compId)
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Registration Error: ' . $e->getMessage());
            return back()->with('error', 'เกิดข้อผิดพลาดในการรันรหัสใบสมัคร กรุณาลองใหม่อีกครั้ง');
        }
    }

    public function myRegistrations()
    {
        $registrations = Registration::with([
                'competition:id,name', 
                'competitionClass:id,name,entry_fee', 
                'team:id,name', 
                'team.members:id,team_id,first_name_th,last_name_th', 
                'paymentTransaction:id,tx_no'
            ])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.registrations', compact('registrations'));
    }

    public function submitGroupPayment(Request $request)
    {
        $request->validate([
            'registration_ids' => 'required|array',
            'registration_ids.*' => 'exists:registrations,id',
            'payment_slip' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ], [
            'registration_ids.required' => 'กรุณาเลือกอย่างน้อย 1 รายการเพื่อชำระเงิน',
            'payment_slip.required' => 'กรุณาอัปโหลดสลิปโอนเงิน',
            'payment_slip.image' => 'ไฟล์ที่อัปโหลดต้องเป็นรูปภาพเท่านั้น',
            'payment_slip.mimes' => 'รองรับเฉพาะไฟล์รูปภาพนามสกุล JPEG, PNG, JPG เท่านั้น',
            'payment_slip.max' => 'ขนาดไฟล์รูปภาพต้องไม่เกิน 5MB',
        ]);

        if ($request->is_tax_invoice_requested) {
        $request->validate([
            'tax_payer_name' => 'required|string|max:255',
            'tax_id' => 'required|string|size:13',
            'tax_payer_branch' => 'required|string|max:100',
            'tax_payer_address' => 'required|string',
            'tax_payer_email' => 'required|email', // 🚀 ตรวจสอบรูปแบบอีเมล
        ]);
    }

        $registrations = Registration::with(['competitionClass:id,entry_fee', 'competition:id,name'])
            ->whereIn('id', $request->registration_ids)
            ->where('user_id', auth()->id())
            ->whereIn('status', ['pending_payment', 'rejected']) 
            ->get();

        if ($registrations->isEmpty()) {
            return back()->with('error', 'ไม่พบรายการที่สามารถชำระเงินได้');
        }

        $compId = $registrations->first()->competition_id;
        if ($registrations->contains('competition_id', '!=', $compId)) {
            return back()->with('error', 'ไม่สามารถรวมบิลข้ามงานแข่งขันได้ กรุณาเลือกชำระเงินทีละงาน');
        }

        $totalAmount = $registrations->sum(fn($reg) => $reg->competitionClass->entry_fee);

        $oldTransactionIds = $registrations->pluck('payment_transaction_id')->filter()->unique();

        try {
            $file = $request->file('payment_slip');
            
            $folderMonth = now()->format('Y-m');
            $safeCompName = preg_replace('/[^A-Za-z0-9ก-๙\-\s]/u', '', $registrations->first()->competition->name);
            $safeCompName = str_replace(' ', '-', trim($safeCompName));
            
            $folderPath = "payment_transactions/{$safeCompName}/{$folderMonth}";
            
            $txNo = "TX-" . now()->format('Ymd') . "-" . rand(10000, 99999);
            $filename = "slip_{$txNo}_" . time() . '.' . $file->getClientOriginalExtension();

            $path = $file->storeAs($folderPath, $filename, 'google_secure');

            if (!$path) throw new \Exception('ไม่สามารถบันทึกไฟล์ลงเซิร์ฟเวอร์ได้');

            $transaction = PaymentTransaction::create([
                    'tx_no' => $txNo,
                    'user_id' => auth()->id(),
                    'competition_id' => $compId,
                    'total_amount' => $totalAmount,
                    'payment_slip_path' => $path,
                    'status' => 'waiting_verify',
                    'is_tax_invoice_requested' => $request->has('is_tax_invoice_requested'),
                    'tax_payer_name' => $request->tax_payer_name,
                    'tax_id' => $request->tax_id,
                    'tax_payer_branch' => $request->tax_payer_branch,
                    'tax_payer_address' => $request->tax_payer_address,
                    'tax_payer_phone' => $request->tax_payer_phone ?? auth()->user()->phone_number,
                    'tax_payer_email' => $request->tax_payer_email ?? auth()->user()->email,
                ]);

            Registration::whereIn('id', $registrations->pluck('id'))->update([
                'payment_transaction_id' => $transaction->id,
                'status' => 'waiting_verify'
            ]);

            if ($oldTransactionIds->isNotEmpty()) {
                foreach ($oldTransactionIds as $oldId) {
                    $oldTx = PaymentTransaction::find($oldId);
                    if ($oldTx && $oldTx->registrations()->count() === 0) {
                        $oldTx->delete();
                    }
                }
            }

            $transaction->load(['competition', 'registrations.competitionClass', 'registrations.team.members', 'user']);

            try {
                Mail::to(auth()->user()->email)->send(new RegistrationSubmittedMail($transaction));
            } catch (\Exception $e) {
                Log::error('ไม่สามารถส่งอีเมลยืนยันการชำระเงินได้');
            }

            return back()->with('success', "ส่งหลักฐานการชำระเงินเรียบร้อยแล้ว! รหัสบิล: {$txNo}");

        } catch (\Exception $e) {

        // dd($e->getMessage());
        Log::error("Payment Group Upload Error: " . $e->getMessage());
            return back()->with('error', 'เกิดข้อผิดพลาดในการอัปโหลดไฟล์');
        }
    }

    public function showRule($compId, $classId)
    {
        $class = CompetitionClass::select('id', 'competition_id', 'rules_url')->where('competition_id', $compId)->findOrFail($classId);
        
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
        $registration = Registration::with(['competition', 'competitionClass', 'team.members', 'user'])
            ->where('user_id', auth()->id())
            ->where('status', 'approved')
            ->findOrFail($id);

        return view('user.e-ticket', compact('registration'));
    }

    public function destroy($id)
    {
        $registration = Registration::where('user_id', auth()->id())->findOrFail($id);

        if (!in_array($registration->status, ['pending_payment', 'rejected'])) {
            return back()->with('error', 'ไม่สามารถยกเลิกรายการนี้ได้ เนื่องจากสถานะถูกเปลี่ยนแปลงไปแล้ว');
        }

        $registration->delete();

        return back()->with('success', 'ยกเลิกใบสมัครเรียบร้อยแล้ว');
    }
}