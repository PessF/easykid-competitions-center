<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

class AdminPaymentController extends Controller
{
    public function index(Request $request)
    {
        // 1. เพิ่ม pending_payment เข้าไปในสถิติเพื่อให้ Admin เห็นภาพรวมที่ถูกต้อง
        $stats = [
            'pending_payment' => Registration::where('status', 'pending_payment')->count(),
            'waiting_verify'  => Registration::where('status', 'waiting_verify')->count(),
            'approved'        => Registration::where('status', 'approved')->count(),
            'rejected'        => Registration::where('status', 'rejected')->count(),
            'all'             => Registration::count(),
        ];

        $statusFilter = $request->query('status', 'waiting_verify');
        $competitionId = $request->query('competition_id');
        $search = $request->query('search');

        $query = Registration::with(['team', 'user', 'competition', 'competitionClass']);

        // กรองตามสถานะ
        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }
        
        // กรองตามการแข่งขัน
        if ($competitionId) {
            $query->where('competition_id', $competitionId);
        }
        
        // ค้นหา
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('regis_no', 'like', "%{$search}%")
                  ->orWhereHas('team', fn($sq) => $sq->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('user', fn($sq) => $sq->where('name', 'like', "%{$search}%"));
            });
        }

        $registrations = $query->latest()->paginate(10)->appends($request->query());
        $competitions = Competition::orderBy('name')->get();

        return view('admin.payments', compact('registrations', 'competitions', 'stats'));
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

        $registration = Registration::findOrFail($id);
        $action = $request->input('action'); 
        $reason = $request->input('reason'); 
        $teamName = $registration->team->name ?? 'ไม่ระบุ';

        try {
            if ($action === 'approve') {
                $registration->update([
                    'status' => 'approved',
                    'verified_by' => Auth::id(),
                    'verified_at' => Carbon::now(),
                    'reject_reason' => null
                ]);

                try {
                    Mail::to($registration->user->email)->send(new RegistrationApprovedMail($registration));
                } catch (\Exception $e) {
                    Log::error('Mail Error (Approve): ' . $e->getMessage());
                }

                // ใช้ String concatenation ธรรมดาเพื่อเลี่ยงปัญหา &quot;
                return redirect()->back()->with('success', 'อนุมัติสลิปของทีม ' . $teamName . ' เรียบร้อยแล้ว');
            } 
            
            if ($action === 'reject') {
                $registration->update([
                    'status' => 'rejected',
                    'verified_by' => Auth::id(),
                    'verified_at' => Carbon::now(),
                    'reject_reason' => $reason
                ]);

                try {
                    Mail::to($registration->user->email)->send(new RegistrationRejectedMail($registration, $reason));
                } catch (\Exception $e) {
                    Log::error('Mail Error (Reject): ' . $e->getMessage());
                }

                return redirect()->back()->with('success', 'ปฏิเสธการสมัครของทีม ' . $teamName . ' เรียบร้อยแล้ว');
            }
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'เกิดข้อผิดพลาดในการตรวจสอบ: ' . $e->getMessage()]);
        }

        return back();
    }

    public function slip($id)
    {
        $registration = Registration::findOrFail($id);
        
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
}