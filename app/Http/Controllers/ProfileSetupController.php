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
            return redirect()->route('dashboard');
        }

        return view('auth.setup-profile'); 
    }

    public function store(Request $request)
    {
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
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'shirt_size' => ['nullable', 'string', 'max:10'],
        ];

        if ($needsPassword) {
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        }

        $validated = $request->validate($rules);

        // 2. จัดการรูปภาพ (ย้ายจาก Local ไป Google Drive)
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            
            // สร้างโครงสร้างโฟลเดอร์: Users/User_1_Poom
            $folderName = "User_" . $user->id . "_" . str_replace(' ', '_', $validated['first_name_en']);
            $fileName = "avatar_" . time() . "." . $file->getClientOriginalExtension();
            $fullPath = "Users/$folderName/$fileName";

            try {
                // ลบรูปเก่าใน Google Drive ถ้าเคยมี Path เก็บไว้
                if ($user->avatar) {
                    Storage::disk('google_secure')->delete($user->avatar);
                }

                // Stream upload prevents RAM exhaustion (Octane-safe)
                $stream = fopen($file->getRealPath(), 'r');
                Storage::disk('google_secure')->put($fullPath, $stream);
                if (is_resource($stream)) {
                    fclose($stream);
                }
                
                // เก็บ Path ใหม่ลงในตัวแปรเพื่อรอ Save ลง DB
                $user->avatar = $fullPath;

            } catch (\Exception $e) {
                // กรณีเชื่อมต่อ Google Drive ไม่ได้ ให้กลับไปหน้าเดิมพร้อมแจ้ง Error
                return back()->withInput()->withErrors(['avatar' => 'การเชื่อมต่อ Google Drive ขัดข้อง: ' . $e->getMessage()]);
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

        return redirect()->route('dashboard')->with('status', 'profile-setup-completed');
    }
}