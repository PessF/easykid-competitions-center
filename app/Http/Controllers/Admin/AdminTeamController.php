<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\Competition;
use App\Models\CompetitionClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminTeamController extends Controller
{
    public function index(Request $request)
    {
        $competitions = Competition::select('id', 'name')->orderBy('event_start_date', 'desc')->get();
        
        $compId = $request->query('competition_id');
        $classId = $request->query('competition_class_id');
        $status = $request->query('status', 'all');
        $search = $request->query('search');

        $classes = [];
        if ($compId) {
            $classes = CompetitionClass::where('competition_id', $compId)->select('id', 'name')->get();
        }

        // 🚀 OPTIMIZE: โหลดข้อมูลล่วงหน้าเฉพาะ "คอลัมน์" ที่ใช้งานจริง ลดภาระ Database & RAM ได้มหาศาล
        $query = Registration::with([
            'team:id,name,school_name', 
            'user:id,name,email,phone_number,avatar',
            'competition:id,name',
            'competitionClass:id,name',
            // โหลดสมาชิกล่วงหน้า พร้อมดึงเฉพาะฟิลด์ที่แสดงใน Modal
            'team.members:id,team_id,prefix_th,first_name_th,last_name_th,prefix_en,first_name_en,last_name_en,birth_date,shirt_size'
        ]);

        if ($compId) {
            $query->where('competition_id', $compId);
        }
        if ($classId) {
            $query->where('competition_class_id', $classId);
        }
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('regis_no', 'like', "%{$search}%")
                  ->orWhereHas('team', function ($qTeam) use ($search) {
                      $qTeam->where('name', 'like', "%{$search}%")
                            ->orWhere('school_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function ($qUser) use ($search) {
                      $qUser->where('name', 'like', "%{$search}%")
                            ->orWhere('phone_number', 'like', "%{$search}%");
                  });
            });
        }

        $registrations = $query->orderBy('created_at', 'desc')
                               ->paginate(20)
                               ->appends($request->query());

        return view('admin.teams', compact('registrations', 'competitions', 'classes', 'compId', 'classId', 'status', 'search'));
    }

    public function destroy($id)
    {
        // 🚀 SECURITY: ป้องกันระดับ Controller เผื่อหลุด Middleware
        abort_if(auth()->user()->role !== 'admin', 403, 'คุณไม่มีสิทธิ์ลบข้อมูลนี้');

        try {
            $registration = Registration::findOrFail($id);
            $regisNo = $registration->regis_no;
            
            $registration->delete();

            return back()->with('success', "ลบข้อมูลใบสมัครรหัส {$regisNo} เรียบร้อยแล้ว");

        } catch (\Exception $e) {
            Log::error("Error deleting registration ID {$id}: " . $e->getMessage());
            return back()->with('error', 'เกิดข้อผิดพลาดในการลบข้อมูล กรุณาลองใหม่อีกครั้ง');
        }
    }
}