<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckProfileSetup
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user && !$user->has_setup_profile && !$request->routeIs('profile.setup*')) {
            return redirect()->route('profile.setup')
                ->with('error', 'กรุณาตั้งค่าข้อมูลส่วนตัวให้เรียบร้อยก่อนเข้าใช้งานระบบ');
        }

        return $next($request);
    }
}