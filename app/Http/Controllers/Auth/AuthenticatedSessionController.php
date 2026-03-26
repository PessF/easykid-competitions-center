<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        //ถ้าเป็น Admin ให้ไปหน้า Admin Dashboard
        if ($user->isAdmin()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        //ถ้ายังไม่ได้ตั้งค่าโปรไฟล์ (ไม่ว่าจะบทบาทไหน) ให้ไปหน้า Setup ก่อน
        if (!$user->has_setup_profile) {
            return redirect()->route('profile.setup');
        }


        // 3. กรณีปกติ (User) ให้ไปหน้า Dashboard ทั่วไป
        return redirect()->intended(route('user.dashboard'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
