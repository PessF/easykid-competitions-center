<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage; // 🚀 เพิ่มสำหรับจัดการไฟล์
use Illuminate\Support\Facades\Log; // 🚀 เพิ่มสำหรับเก็บ Error Log

class ProfileController extends Controller
{
    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        // 1. ดักจับกรณีอัปโหลดไฟล์เกินขนาดที่ PHP กำหนด (ป้องกัน Error 500)
        if (empty($request->all()) && $request->server('CONTENT_LENGTH') > 0) {
            return back()->withInput()->withErrors(['avatar' => 'ไฟล์รูปภาพมีขนาดใหญ่เกินไป (ระบบรองรับสูงสุด 2MB)']);
        }

        /** @var \App\Models\User $user */
        $user = $request->user();

        // 2. ตรวจสอบข้อมูล (Validation)
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'prefix_th'     => ['required', 'string', 'max:255'],
            'first_name_th' => ['required', 'string', 'max:255'],
            'last_name_th'  => ['required', 'string', 'max:255'],
            'prefix_en'     => ['required', 'string', 'max:255'],
            'first_name_en' => ['required', 'string', 'max:255'],
            'last_name_en'  => ['required', 'string', 'max:255'],
            'birthday'      => ['required', 'date', 'before:today'],
            'phone_number'  => ['required', 'string', 'min:10'],
            'shirt_size'    => ['nullable', 'string', 'max:10'],
            'avatar'        => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'], // 🚀 กฎอัปโหลดรูป
        ]);

        try {
            // 3. จัดการอัปโหลดรูปภาพใหม่ (ถ้ามี)
            if ($request->hasFile('avatar')) {
                $file = $request->file('avatar');
                
                if ($file->isValid()) {
                    // ลบรูปเก่าทิ้ง (เฉพาะรูปที่ไม่ได้มาจากลิงก์ Google)
                    if ($user->avatar && !str_starts_with($user->avatar, 'http')) {
                        Storage::disk('public')->delete($user->avatar);
                    }

                    // สร้างชื่อไฟล์และโฟลเดอร์จัดเก็บ
                    $folderName = "Users/User_" . $user->id . "_" . str_replace(' ', '_', $validated['first_name_en']);
                    $fileName = "avatar_" . time() . "." . $file->getClientOriginalExtension();

                    // บันทึกลง Storage/App/Public
                    $fullPath = $file->storeAs($folderName, $fileName, 'public');
                    $user->avatar = $fullPath;
                } else {
                    return back()->withInput()->withErrors(['avatar' => 'ไฟล์รูปภาพไม่สมบูรณ์ กรุณาลองใหม่อีกครั้ง']);
                }
            }

            // 4. อัปเดตข้อมูลอื่นๆ
            $user->name = $validated['name'];
            $user->prefix_th = $validated['prefix_th'];
            $user->first_name_th = $validated['first_name_th'];
            $user->last_name_th = $validated['last_name_th'];
            $user->prefix_en = $validated['prefix_en'];
            $user->first_name_en = $validated['first_name_en'];
            $user->last_name_en = $validated['last_name_en'];
            $user->birthday = $validated['birthday'];
            $user->phone_number = $validated['phone_number'];
            $user->shirt_size = $validated['shirt_size'];
            
            // ป้องกันกรณีเปลี่ยนอีเมล (ถ้าใน Modal มีให้แก้ แต่ตอนนี้เราล็อคไว้)
            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }

            $user->save();

            return Redirect::back()
                ->with('status', 'profile-updated')
                ->with('success', 'อัปเดตข้อมูลโปรไฟล์และรูปภาพเรียบร้อยแล้ว');

        } catch (\Exception $e) {
            Log::error('Profile Update Error: ' . $e->getMessage());
            return Redirect::back()
                ->withInput()
                ->with('error', 'ระบบไม่สามารถอัปเดตข้อมูลได้ กรุณาลองใหม่อีกครั้ง');
        }
    }

    /**
     * ดึงรูปภาพ Avatar (กรณีรูปไม่ได้อยู่ใน Public Storage)
     */
    public function showAvatar($id)
    {
        $user = User::findOrFail($id);

        if (!$user->avatar) {
            abort(404);
        }

        // ถ้ารูปมาจาก Google ให้ Redirect ไปที่ลิงก์นั้นเลย
        if (str_starts_with($user->avatar, 'http')) {
            return redirect($user->avatar);
        }

        // ถ้ารูปอยู่ในระบบ Local Public Storage (ไม่ต้องใช้ Stream แล้ว)
        return redirect()->asset('storage/' . $user->avatar);
    }
    
    // --- ส่วน Delete Account ไม่ค่อยได้ใช้ ปล่อยไว้หรือลบทิ้งก็ได้ครับ ---
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}