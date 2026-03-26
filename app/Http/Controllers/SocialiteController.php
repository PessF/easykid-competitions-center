<?php

namespace App\Http\Controllers;

use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;

class SocialiteController extends Controller
{
    public function googleLogin()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function googleAuth()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            
            // ค้นหา User จาก Email
            $user = User::where('email', $googleUser->email)->first();

            if ($user) {
                // --- กรณีมี User ในระบบอยู่แล้ว ---
                $updateData = [
                    'google_id' => $googleUser->id,
                ];

                // ถ้ายังไม่เคยยืนยัน Email ให้ยืนยันเลย (เพราะมาจาก Google)
                if (is_null($user->email_verified_at)) {
                    $updateData['email_verified_at'] = now();
                }

                $user->update($updateData);
            } else {
                // --- กรณีเป็น User ใหม่แกะกล่อง ---
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar, 
                    'email_verified_at' => now(),
                    'has_setup_profile' => false, 
                    'role' => 'user',
                ]);
            }

            // ล็อคอินเข้าระบบ
            Auth::login($user);

            // --- Logic การ Redirect (ลำดับความสำคัญ) ---
            
            // ถ้าเป็น Admin ให้ไปหน้า Admin
            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            }

            // ถ้ายังตั้งค่าโปรไฟล์ไม่เสร็จ (ไม่ว่าจะเป็น User เก่าหรือใหม่)
            if (!$user->has_setup_profile) {
                return redirect()->route('profile.setup');
            }


            // 3. กรณีทั่วไป ไปหน้า Dashboard
            return redirect()->intended(route('user.dashboard'));

        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }
}