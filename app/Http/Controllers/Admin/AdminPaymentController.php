<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentTransaction;
use App\Models\Registration; 
use App\Models\Competition; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Mail\RegistrationApprovedMail;
use App\Mail\RegistrationRejectedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB; 

class AdminPaymentController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'waiting_verify'  => PaymentTransaction::where('status', 'waiting_verify')->count(),
            'approved'        => PaymentTransaction::where('status', 'approved')->count(),
            'rejected'        => PaymentTransaction::where('status', 'rejected')->count(),
            'all'             => PaymentTransaction::count(),
        ];

        $statusFilter = $request->query('status', 'waiting_verify');
        $competitionId = $request->query('competition_id');
        $search = $request->query('search');

        $query = PaymentTransaction::with(['user', 'competition', 'registrations.team', 'registrations.competitionClass']);

        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }
        
        if ($competitionId) {
            $query->where('competition_id', $competitionId);
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('tx_no', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($sq) => $sq->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('registrations', fn($sq) => $sq->where('regis_no', 'like', "%{$search}%"))
                  ->orWhereHas('registrations.team', fn($sq) => $sq->where('name', 'like', "%{$search}%"));
            });
        }

        $transactions = $query->latest()->paginate(10)->appends($request->query());
        $competitions = Competition::orderBy('name')->get();

        return view('admin.payments', compact('transactions', 'competitions', 'stats'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'reason' => 'required_if:action,reject|nullable|string|max:1000',
        ], [
            'action.in' => 'คำสั่งไม่ถูกต้อง',
            'reason.required_if' => 'กรุณาระบุเหตุผลที่ปฏิเสธสลิปโอนเงิน',
        ]);

        $transaction = PaymentTransaction::with([
            'competition', 
            'user', 
            'registrations.competitionClass', 
            'registrations.team.members', 
            'registrations.user'
        ])->findOrFail($id);
        
        $action = $request->input('action'); 
        $reason = $request->input('reason'); 
        $txNo = $transaction->tx_no;

        try {
            DB::beginTransaction();

            if ($action === 'approve') {
                $transaction->update([
                    'status' => 'approved',
                    'verified_by' => Auth::id(),
                    'verified_at' => Carbon::now(),
                    'reject_reason' => null
                ]);
                Registration::where('payment_transaction_id', $transaction->id)->update(['status' => 'approved']);
                
                DB::commit(); 

                try {
                    Mail::to($transaction->user->email)->send(new RegistrationApprovedMail($transaction));
                } catch (\Exception $e) {
                    Log::error('Mail Error (Approve): ' . $e->getMessage());
                }

                // ซิงค์ข้อมูลลง Google Sheet หลังจาก Approve สำเร็จ
                $this->syncToGoogleSheet($transaction);

                return redirect()->back()->with('success', "อนุมัติบิล {$txNo} เรียบร้อยแล้ว");

            } elseif ($action === 'reject') {
                $transaction->update([
                    'status' => 'rejected',
                    'verified_by' => Auth::id(),
                    'verified_at' => Carbon::now(),
                    'reject_reason' => $reason
                ]);
                Registration::where('payment_transaction_id', $transaction->id)->update(['status' => 'rejected']);

                DB::commit();

                try {
                    Mail::to($transaction->user->email)->send(new RegistrationRejectedMail($transaction, $reason));
                } catch (\Exception $e) {
                    Log::error('Mail Error (Reject): ' . $e->getMessage());
                }

                return redirect()->back()->with('success', "ปฏิเสธบิล {$txNo} เรียบร้อยแล้ว");
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'เกิดข้อผิดพลาดในการตรวจสอบ: ' . $e->getMessage()]);
        }

        return back();
    }

    private function syncToGoogleSheet(PaymentTransaction $transaction)
    {
        $sheetService = new \App\Services\GoogleSheetService();
        $folderId = env('GOOGLE_MASTER_FOLDER_ID');

        if (!$folderId) {
            Log::error('Google Sheet Config Error: ยังไม่ได้ตั้งค่า GOOGLE_MASTER_FOLDER_ID');
            return;
        }

        foreach ($transaction->registrations as $registration) {
            try {
                $fileName = $transaction->competition->name; 
                
                // 1. 🚀 โลจิก Sanitize ชื่อแท็บ (ห้ามมีอักขระพิเศษ และห้ามยาวเกิน 31 ตัว)
                $rawTabName = $registration->competitionClass->name;
                $cleanTabName = str_replace(['/', '*', ':', '?', '[', ']'], '-', $rawTabName);
                $tabName = mb_substr($cleanTabName, 0, 31);
                
                $adminEmail = 'info@easykidsrobotics.com';
                
                // ดึงค่า Max Members เพื่อใช้ล็อกจำนวนคอลัมน์
                $maxMembers = (int) ($registration->competitionClass->max_members ?? 2);
                
                // 2. 🚀 สร้าง Headers แบบ Fixed Schema ตาม Max Members
                $headers = [
                    'ประทับเวลา', 'อีเมลผู้ส่งสมัคร', 'รุ่นการแข่งขัน', 'ชื่อทีม (Team Name)', 'โรงเรียน / สถาบัน'
                ];

                for ($i = 1; $i <= $maxMembers; $i++) {
                    $headers[] = "คำนำหน้า (คนที่ $i)";
                    $headers[] = "ชื่อ-นามสกุล (คนที่ $i) [TH]";
                    $headers[] = "ชื่อ-นามสกุล (คนที่ $i) [EN]";
                    $headers[] = "ไซส์เสื้อ (คนที่ $i)";
                    $headers[] = "วัน/เดือน/ปีเกิด (คนที่ $i)";
                }

                $headers = array_merge($headers, [
                    'ชื่อจริง (ผู้ส่งสมัคร)', 
                    'นามสกุล (ผู้ส่งสมัคร)', 
                    'เบอร์โทรศัพท์ (ผู้ส่งสมัคร)', 
                    'วัน/เดือน/ปีเกิด (ผู้ส่งสมัคร)', 
                    'ไซส์เสื้อ (ผู้ส่งสมัคร)',
                    'หลักฐานการชำระเงิน (ลิงก์สลิป)', 
                    'สถานะการตรวจสอบ'
                ]);

                // 3. 🚀 เตรียมข้อมูลแถว (Row Data)
                $rowData = [
                    Carbon::now('Asia/Bangkok')->format('d/m/Y H:i:s'), 
                    $registration->user->email ?? '-', 
                    $rawTabName, 
                    $registration->team->name ?? '-', 
                    $registration->team->school_name ?? '-', 
                ];

                // วนลูปตามสมาชิก โดยอิงจากจำนวนสมาชิกสูงสุด (เพื่อไม่ให้คอลัมน์เลื่อน)
                $members = $registration->team->members ?? collect();
                for ($i = 0; $i < $maxMembers; $i++) {
                    $member = $members->get($i); 
                    $rowData[] = $member ? ($member->prefix_th ?: '-') : '-';
                    $rowData[] = $member ? (trim(($member->first_name_th ?? '') . ' ' . ($member->last_name_th ?? '')) ?: '-') : '-';
                    $rowData[] = $member ? (trim(($member->first_name_en ?? '') . ' ' . ($member->last_name_en ?? '')) ?: '-') : '-';
                    $rowData[] = $member ? ($member->shirt_size ?: '-') : '-';
                    $rowData[] = ($member && !empty($member->birth_date)) ? Carbon::parse($member->birth_date)->format('d/m/Y') : '-';
                }

                // ข้อมูลผู้ส่งสมัคร (ใช้ Nullsafe ?-> และ ?? เพื่อความปลอดภัย)
                $u = $transaction->user;
                $rowData[] = $u ? ($u->first_name_th ?? $u->name ?? '-') : '-'; 
                $rowData[] = $u?->last_name_th ?? '-'; 
                $rowData[] = !empty($u?->phone_number) ? "'" . $u->phone_number : '-'; 
                $rowData[] = !empty($u?->birthday) ? Carbon::parse($u->birthday)->format('d/m/Y') : '-'; 
                $rowData[] = $u?->shirt_size ?? '-'; 

                // ลิงก์สลิป (เรียกผ่าน route เพื่อความปลอดภัย)
                $rowData[] = route('admin.payments.slip', $transaction->id); 
                $rowData[] = 'อนุมัติการสมัครเรียบร้อย';

                // ยิงข้อมูลไปที่ Service
                $sheetId = $sheetService->processAutomation($rowData, $headers, $fileName, $tabName, $folderId, $adminEmail);

                // บันทึก Sheet ID ลง Competition (ถ้ายังไม่มี)
                if (empty($transaction->competition->google_sheet_id)) {
                    $transaction->competition->update(['google_sheet_id' => $sheetId]);
                }
                
                // หน่วงเวลาเล็กน้อยเพื่อเลี่ยง Rate Limit ของ Google API
                usleep(500000); 

            } catch (\Exception $e) {
                Log::error("Google Sheet Automation Error (Regis ID: {$registration->id}): " . $e->getMessage());
            }
        }
    }

    public function slip($id)
    {
        $transaction = PaymentTransaction::findOrFail($id);
        if (!$transaction->payment_slip_path) abort(404, 'ไม่พบไฟล์สลิปโอนเงิน');

        $disk = Storage::disk('google_secure');
        $path = $transaction->payment_slip_path;

        if (!$disk->exists($path)) abort(404, 'ไฟล์สลิปถูกลบหรือสูญหาย');

        $mimeType = $disk->mimeType($path) ?? 'image/jpeg';
        
        return response()->stream(function () use ($disk, $path) {
            if (ob_get_level() > 0) ob_end_clean();
            $stream = $disk->readStream($path);
            if ($stream) {
                fpassthru($stream);
                fclose($stream);
            }
        }, 200, ['Content-Type' => $mimeType, 'Cache-Control' => 'private, max-age=3600']);
    }
}