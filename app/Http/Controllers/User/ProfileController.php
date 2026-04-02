<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;  
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    /**
     * อัปเดตข้อมูลโปรไฟล์ของผู้ใช้งาน
     */
    public function update(Request $request): RedirectResponse
    {
        // 1. ตรวจสอบความถูกต้องของข้อมูล (Validation)
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'prefix_th'     => ['nullable', 'string', 'max:100'],
            'first_name_th' => ['nullable', 'string', 'max:255'],
            'last_name_th'  => ['nullable', 'string', 'max:255'],
            'prefix_en'     => ['nullable', 'string', 'max:100'],
            'first_name_en' => ['nullable', 'string', 'max:255'],
            'last_name_en'  => ['nullable', 'string', 'max:255'],
            'birthday'      => ['nullable', 'date'],
            'phone_number'  => ['nullable', 'string', 'max:20'],
            'shirt_size'    => ['nullable', 'string', 'max:10'],
        ]);

        try {
            // 2. เข้าถึงตัวแปร User ปัจจุบัน
            $user = $request->user();

            // 3. เติมข้อมูลที่ผ่านการตรวจสอบแล้วลงใน Model
            $user->fill($validated);

            // 4. บันทึกข้อมูล
            $user->save();

            // 5. ส่งกลับไปหน้าเดิม (แยก with เพื่อความชัวร์ 100%)
            return Redirect::back()
                ->with('status', 'profile-updated')
                ->with('success', 'อัปเดตข้อมูลโปรไฟล์เรียบร้อยแล้ว');

        } catch (\Exception $e) {
            // หากเกิดข้อผิดพลาด ให้บันทึก Log และส่ง Error แจ้งเตือน
            Log::error('Profile Update Error: ' . $e->getMessage());
            
            return Redirect::back()
                ->with('error', 'ระบบไม่สามารถอัปเดตข้อมูลได้ กรุณาลองใหม่อีกครั้ง');
        }
    }
}