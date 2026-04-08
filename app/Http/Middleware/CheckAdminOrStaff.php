<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminOrStaff
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // ถ้าล็อกอินแล้ว และเป็น Admin หรือ Staff ให้ผ่านได้
        if (auth()->check() && auth()->user()->isAdminOrStaff()) {
            return $next($request);
        }

        // ถ้าเป็นแค่ User ธรรมดา ให้เตะกลับไปหน้า Dashboard ของตัวเอง
        return redirect()->route('user.dashboard')->with('error', 'คุณไม่มีสิทธิ์เข้าถึงส่วนของระบบหลังบ้าน');
    }
}