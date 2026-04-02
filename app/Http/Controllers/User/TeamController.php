<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Arr; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TeamController extends Controller
{
    /**
     * หน้าแรก แสดงรายการทีมทั้งหมดของ User
     */
    public function index(Request $request)
    {
        $teams = $request->user()
                    ->teams()
                    ->withCount('members')
                    ->latest()
                    ->paginate(15);
        
        return view('user.team', compact('teams'));
    }

    public function create()
    {
        return view('user.teams.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                      => ['required', 'string', 'max:255'],
            'school_name'               => ['required', 'string', 'max:255'],
            'members'                   => ['required', 'array', 'min:1'],
            'members.*.prefix_th'       => ['nullable', 'string', 'max:100'],
            'members.*.first_name_th'   => ['required', 'string', 'max:255'],
            'members.*.last_name_th'    => ['required', 'string', 'max:255'],
            'members.*.prefix_en'       => ['nullable', 'string', 'max:100'],
            'members.*.first_name_en'   => ['nullable', 'string', 'max:255'],
            'members.*.last_name_en'    => ['nullable', 'string', 'max:255'],
            'members.*.birth_date'      => ['required', 'date'],
            'members.*.shirt_size'      => ['nullable', 'string', 'max:10'],
        ]);

        try {
            DB::transaction(function () use ($validated, $request) {
                
                $team = $request->user()->teams()->create([
                    'name'        => $validated['name'],
                    'school_name' => $validated['school_name'],
                ]);

                foreach ($validated['members'] as $memberData) {
                    $team->members()->create($memberData);
                }
                
            });

            return redirect()->route('user.teams.index')
                ->with('success', 'สร้างทีมและบันทึกรายชื่อลูกทีมเรียบร้อยแล้ว!');

        } catch (\Exception $e) {
            Log::error('Create Team Error: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', 'เกิดข้อผิดพลาดในการสร้างทีม กรุณาลองใหม่อีกครั้ง');
        }
    }

    public function update(Request $request, Team $team)
    {
        // 🔒 1. Security Check: ป้องกันคนอื่นเอา ID ทีมมาแก้ URL ตรงๆ
        if ($team->user_id !== $request->user()->id) {
            abort(403, 'คุณไม่มีสิทธิ์แก้ไขทีมนี้');
        }

        // 🚀 2. MASTER DATA LOCK: ห้ามแก้ข้อมูลถ้าทีมนี้มีใบสมัครค้างอยู่
        $isLocked = Registration::where('team_id', $team->id)
            ->whereIn('status', ['pending_payment', 'waiting_verify', 'approved'])
            ->exists();

        if ($isLocked) {
            return back()->with('error', 'ไม่สามารถแก้ไขข้อมูลได้ เนื่องจากทีมนี้ถูกนำไปใช้สมัครแข่งขันแล้ว (หากต้องการแก้ไข กรุณาติดต่อแอดมิน)');
        }

        // 3. Validate ข้อมูลแบบ Array
        $validated = $request->validate([
            'name'                      => ['required', 'string', 'max:255'],
            'school_name'               => ['required', 'string', 'max:255'],
            'members'                   => ['required', 'array', 'min:1'],
            'members.*.id'              => ['nullable', 'integer'], 
            'members.*.prefix_th'       => ['nullable', 'string', 'max:100'],
            'members.*.first_name_th'   => ['required', 'string', 'max:255'],
            'members.*.last_name_th'    => ['required', 'string', 'max:255'],
            'members.*.prefix_en'       => ['nullable', 'string', 'max:100'],
            'members.*.first_name_en'   => ['required', 'string', 'max:255'], 
            'members.*.last_name_en'    => ['required', 'string', 'max:255'], 
            'members.*.birth_date'      => ['required', 'date'],
            'members.*.shirt_size'      => ['nullable', 'string', 'max:10'],
        ]);

        try {
            DB::transaction(function () use ($validated, $team) {
                $team->update([
                    'name'        => $validated['name'],
                    'school_name' => $validated['school_name'],
                ]);

                $submittedMembers = collect($validated['members']);
                $submittedIds = $submittedMembers->pluck('id')->filter()->toArray();

                $team->members()->whereNotIn('id', $submittedIds)->delete();

                foreach ($submittedMembers as $memberData) {
                    if (!empty($memberData['id'])) {
                        $team->members()
                             ->where('id', $memberData['id'])
                             ->update(Arr::except($memberData, ['id']));
                    } else {
                        $team->members()->create(Arr::except($memberData, ['id']));
                    }
                }
            });

            return redirect()->route('user.teams.index')
                ->with('success', 'อัปเดตข้อมูลทีมและรายชื่อสมาชิกเรียบร้อยแล้ว!');

        } catch (\Exception $e) {
            Log::error('Update Team Error: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', 'เกิดข้อผิดพลาดในการอัปเดตข้อมูล กรุณาลองใหม่อีกครั้ง');
        }
    }

    /**
     * ลบทีมและลูกทีมทั้งหมด
     */
    public function destroy(Request $request, Team $team)
    {
        // 🔒 1. Security Check
        if ($team->user_id !== $request->user()->id) {
            abort(403, 'คุณไม่มีสิทธิ์ลบทีมนี้');
        }

        // 🚀 2. MASTER DATA LOCK: ห้ามลบถ้าทีมนี้มีใบสมัครค้างอยู่
        $isLocked = Registration::where('team_id', $team->id)
            ->whereIn('status', ['pending_payment', 'waiting_verify', 'approved'])
            ->exists();

        if ($isLocked) {
            return back()->with('error', 'ไม่สามารถลบทีมได้ เนื่องจากทีมนี้มีประวัติการส่งใบสมัครเข้าแข่งขันแล้ว');
        }

        try {
            $team->delete();

            return redirect()->route('user.teams.index')
                ->with('success', 'ลบทีมการแข่งขันออกจากระบบเรียบร้อยแล้ว');
                
        } catch (\Exception $e) {
            Log::error('Delete Team Error: ' . $e->getMessage());
            return back()->with('error', 'ไม่สามารถลบทีมได้ กรุณาลองใหม่อีกครั้ง');
        }
    }
}