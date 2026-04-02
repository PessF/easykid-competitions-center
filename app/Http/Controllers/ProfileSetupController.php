<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileSetupController extends Controller
{
    public function index()
    {
        // เช็คว่าถ้า setup เรียบร้อยแล้ว ไม่ให้เข้าหน้านี้อีก ให้เด้งไป Dashboard
        if (Auth::user()->has_setup_profile) {
            return redirect()->route('user.dashboard');
        }

        return view('auth.setup-profile'); 
    }

    public function store(Request $request)
    {
        // 🚀 ด่านที่ 1: ดักจับ PHP Post Drop (รูปโปรไฟล์ตั้งไว้ที่ 2MB)
        if (empty($request->all()) && $request->server('CONTENT_LENGTH') > 0) {
            return back()->withInput()->withErrors(['avatar' => 'ไฟล์มีขนาดใหญ่เกินไป (ระบบรองรับสูงสุด 2MB)']);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $needsPassword = is_null($user->password);

        // 1. Validation Rules
        $rules = [
            'prefix_th' => ['required', 'string', 'max:255'],
            'first_name_th' => ['required', 'string', 'max:255'],
            'last_name_th' => ['required', 'string', 'max:255'],
            'prefix_en' => ['required', 'string', 'max:255'],
            'first_name_en' => ['required', 'string', 'max:255'],
            'last_name_en' => ['required', 'string', 'max:255'],
            'birthday' => ['required', 'date', 'before:today'],
            'phone_number' => ['required', 'string', 'min:10'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'], // ลิมิต 2MB
            'shirt_size' => ['nullable', 'string', 'max:10'],
        ];

        if ($needsPassword) {
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        }

        $validated = $request->validate($rules);

        // 2. จัดการรูปภาพ (เก็บลง Local Public Disk ตามนโยบาย)
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            
            // 🚀 ด่านที่ 2: เช็คความสมบูรณ์ของไฟล์
            if (!$file->isValid()) {
                return back()->withInput()->withErrors(['avatar' => 'ไฟล์รูปภาพไม่สมบูรณ์ กรุณาลองใหม่อีกครั้ง']);
            }

            try {
                // ลบรูปเก่าใน Public Disk (เช็คด้วยว่าไม่ใช่ลิงก์จาก Google Login)
                if ($user->avatar && !str_starts_with($user->avatar, 'http')) {
                    Storage::disk('public')->delete($user->avatar);
                }

                $folderName = "Users/User_" . $user->id . "_" . str_replace(' ', '_', $validated['first_name_en']);
                $fileName = "avatar_" . time() . "." . $file->getClientOriginalExtension();

                // บันทึกลง Public Disk ทันที (ไม่ต้องใช้ Stream แล้ว เร็วกว่าเดิมเยอะ)
                $fullPath = $file->storeAs($folderName, $fileName, 'public');
                
                $user->avatar = $fullPath;

            } catch (\Exception $e) {
                $safeError = str_replace(["'", '"'], "", $e->getMessage());
                return back()->withInput()->withErrors(['avatar' => 'ไม่สามารถบันทึกรูปภาพได้: ' . $safeError]);
            }
        }

        // 3. บันทึกข้อมูลที่เหลือลงฐานข้อมูล
        $user->prefix_th = $validated['prefix_th'];
        $user->first_name_th = $validated['first_name_th'];
        $user->last_name_th = $validated['last_name_th'];
        $user->prefix_en = $validated['prefix_en'];
        $user->first_name_en = $validated['first_name_en'];
        $user->last_name_en = $validated['last_name_en'];
        $user->birthday = $validated['birthday'];
        $user->phone_number = $validated['phone_number'];
        $user->shirt_size = $validated['shirt_size'] ?? null;
        $user->has_setup_profile = true;

        if ($needsPassword) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('user.dashboard')->with('status', 'profile-setup-completed');
    }
}