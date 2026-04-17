<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request; // 🚀 เปลี่ยนมาใช้ Request ปกติ
use Illuminate\Support\Facades\Auth;
use App\Models\User; // 🚀 เรียกใช้ Model User

class VerifyEmailController extends Controller
{
    public function __invoke(Request $request, $id, $hash): RedirectResponse
    {
        $user = User::findOrFail($id);

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403, 'ลิงก์ยืนยันอีเมลไม่ถูกต้องหรือหมดอายุแล้ว');
        }

        if ($user->hasVerifiedEmail()) {
            Auth::login($user);
            return $this->redirectBasedOnProfile($user);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        $user->email_verified_at = now();
        Auth::login($user);
        session()->save();

        return $this->redirectBasedOnProfile($user);
    }

    // ฟังก์ชันช่วยพาไปยังหน้าที่ถูกต้อง
    private function redirectBasedOnProfile($user): RedirectResponse
    {
        if (!$user->has_setup_profile) {
            return redirect()->route('profile.setup')->with('verified', 1);
        }
        return redirect()->intended(route('user.dashboard', absolute: false).'?verified=1');
    }
}