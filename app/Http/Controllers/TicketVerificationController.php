<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Registration;

class TicketVerificationController extends Controller
{
    public function verify(Request $request, $reg_no)
    {
        // 1. ดึงข้อมูลใบสมัคร พร้อมกับข้อมูลทีมและเด็กๆ จากรหัส REG-XXXX
        $registration = Registration::with(['competition', 'competitionClass', 'team.members'])
            ->where('regis_no', $reg_no)
            ->firstOrFail();

        $isStaff = false;
        
        // เช็คว่าคนที่สแกน ได้ล็อกอินอยู่ไหม? และมีสิทธิ์เป็น admin หรือ staff หรือเปล่า?
        if (auth()->check() && in_array(auth()->user()->role, ['admin', 'staff'])) {
            $isStaff = true;
        }

        // 3. โยนข้อมูลทั้งหมดไปที่หน้าจอแสดงผล (หน้าจอเราจะทำในขั้นตอนต่อไปครับ)
        return view('verify.ticket', compact('registration', 'isStaff'));
    }
}