<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PaymentTransaction;
// use App\Models\Team;
use App\Models\Competition;
use App\Models\Registration;
use Carbon\Carbon; // 🚀 เพิ่มสำหรับจัดการวันที่ของกราฟ
use Illuminate\Support\Facades\DB; // 🚀 เพิ่มสำหรับคำสั่ง Query กราฟ

class AdminController extends Controller
{
    public function index()
    {
        // 1. ดึงสถิติภาพรวม (Top Stats)
        
        // บิลที่รอตรวจสอบจริงจากตาราง payment_transactions
        $pendingPayments = PaymentTransaction::where('status', 'waiting_verify')->count();
        
        // ยอดเงินรวมที่ได้รับการอนุมัติแล้ว
        $totalRevenue = PaymentTransaction::where('status', 'approved')->sum('total_amount');
        
        // 🚀 แก้ไข: นับจำนวนการสมัคร (Registrations) แทนจำนวนทีมในระบบ
        $totalTeams = Registration::count();
        
        // นับงานแข่งขันที่ไม่ใช่ 'draft'
        $activeCompetitions = Competition::where('status', '!=', 'draft')->count(); 

        // ==========================================
        // 🚀 2. ดึงข้อมูลสำหรับกราฟ (7 วันล่าสุด)
        // ==========================================
        $chartLabels = [];
        $chartDataObj = [];

        // 2.1 สร้างโครงสร้าง 7 วันเตรียมไว้ก่อน ป้องกันปัญหาวันที่ไม่มีคนสมัครแล้วกราฟแหว่ง
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $chartLabels[] = $date->translatedFormat('d M'); // เช่น 10 เม.ย.
            $chartDataObj[$date->format('Y-m-d')] = 0; // ตั้งค่าเริ่มต้นเป็น 0
        }

        // 2.2 ดึงข้อมูลจริงจากฐานข้อมูล
        $registrationsStats = Registration::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(6)->startOfDay())
            ->groupBy('date')
            ->get();

        // 2.3 นำข้อมูลจริงมาหยอดลงในโครงที่เตรียมไว้
        foreach ($registrationsStats as $stat) {
            $dateKey = Carbon::parse($stat->date)->format('Y-m-d');
            if (isset($chartDataObj[$dateKey])) {
                $chartDataObj[$dateKey] = $stat->count;
            }
        }

        // 2.4 แปลงรูปแบบให้อยู่ใน Array ธรรมดาสำหรับส่งให้ Chart.js
        $chartData = array_values($chartDataObj);


        // ==========================================
        // 3. ดึงข้อมูลรายการล่าสุด (Recent Data)
        // ==========================================
        
        // ดึงข้อมูลบิลล่าสุดที่ "รอตรวจสอบ"
        $recentPayments = PaymentTransaction::with('user:id,name')
            ->where('status', 'waiting_verify')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // ดึงข้อมูลการสมัครล่าสุด
        $recentRegistrations = Registration::with([
                'team:id,name', 
                'competitionClass:id,name'
            ])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'pendingPayments',
            'totalRevenue',
            'totalTeams',
            'activeCompetitions',
            'recentPayments',
            'recentRegistrations',
            'chartLabels',  
            'chartData'      
        ));
    }

    // --- ส่วนจัดการผู้ใช้งานคงเดิมตามที่คุณภูมิเขียนไว้ ซึ่งดีอยู่แล้ว ---

    public function manageUsers(Request $request)
    {
        $search = $request->input('search');
        $roleFilter = $request->input('role'); 

        $users = User::select([
                'id', 'name', 'email', 'role', 'avatar', 'created_at',
                'prefix_th', 'first_name_th', 'last_name_th',
                'prefix_en', 'first_name_en', 'last_name_en',
                'phone_number', 'shirt_size', 'birthday'
            ])
            ->when($search, function ($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone_number', 'like', "%{$search}%")
                      ->orWhere('first_name_th', 'like', "%{$search}%");
                });
            })
            ->when($roleFilter, function ($query) use ($roleFilter) {
                $query->where('role', $roleFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.users', compact('users', 'search', 'roleFilter'));
    }

    public function updateRole(Request $request, $id)
    {
        $request->validate(['role' => 'required|in:user,staff,admin']);
        $user = User::findOrFail($id);

        if ($user->id === auth()->id() && $request->role !== 'admin') {
            return back()->with('error', 'ไม่สามารถเปลี่ยนแปลงหรือลดสิทธิ์ของบัญชีตัวเองได้');
        }

        $user->update(['role' => $request->role]);
        return back()->with('success', "เปลี่ยนระดับสิทธิ์เรียบร้อยแล้ว");
    }

    public function destroyUser($id)
    {
        $user = User::select(['id', 'role'])->findOrFail($id);
        if ($user->id === auth()->id() || $user->role === 'admin') {
            return back()->with('error', 'ไม่สามารถลบบัญชีนี้ได้');
        }
        $user->delete();
        return back()->with('success', "ลบบัญชีผู้ใช้งานเรียบร้อยแล้ว");
    }
}