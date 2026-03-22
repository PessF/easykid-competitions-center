<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleWare
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
public function handle(Request $request, Closure $next)
{
    // ถ้า Login แล้ว และเป็น Admin ให้ผ่านไปได้
    if (auth()->check() && auth()->user()->isAdmin()) {
        return $next($request);
    }

    // ถ้าไม่ใช่ ดีดกลับหน้าแรกพร้อมข้อความเตือน
    return redirect('/')->with('error', 'คุณไม่มีสิทธิ์เข้าถึงส่วนนี้');
}
}
