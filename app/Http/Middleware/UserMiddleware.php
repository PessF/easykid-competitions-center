<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. ถ้า Login แล้ว และ Role "ไม่ใช่" user (เช่น เป็น admin)
        if (Auth::check() && Auth::user()->role !== 'user') {
            // 2. ดีดไปหน้า Admin Dashboard ทันที
            return redirect()->route('admin.dashboard');
        }

        // 3. ถ้าเป็น user ปกติ ก็ให้ผ่านไปหน้า Dashboard ของตัวเองได้
        return $next($request);
    }
}