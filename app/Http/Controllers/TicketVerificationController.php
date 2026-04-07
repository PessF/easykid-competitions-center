<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Registration;

class TicketVerificationController extends Controller
{
    public function verify(Request $request, $reg_no)
    {
        $registration = Registration::with(['competition', 'competitionClass', 'team.members', 'user'])
            ->where('regis_no', $reg_no)
            ->firstOrFail();

        // เช็คว่าคนที่สแกนเป็นเจ้าหน้าที่หรือไม่
        $isStaff = auth()->check() && in_array(auth()->user()->role, ['admin', 'staff']);

        return view('verify.ticket', compact('registration', 'isStaff'));
    }

    public function checkIn(Request $request, $reg_no)
    {
        if (!(auth()->check() && in_array(auth()->user()->role, ['admin', 'staff']))) {
            abort(403, 'เฉพาะเจ้าหน้าที่เท่านั้นที่สามารถเช็คอินได้');
        }

        $registration = Registration::where('regis_no', $reg_no)->firstOrFail();

        $registration->update([
            'checked_in_at' => now()
        ]);

        // ส่งกลับหน้าเดิมพร้อมแจ้งเตือนว่าสำเร็จ
        return back()->with('success', 'ยืนยันการเข้างานสำเร็จ!');
    }
}