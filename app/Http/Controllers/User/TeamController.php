<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Team;
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
                    ->get();
        
        return view('user.team', compact('teams'));
    }

    public function create()
    {
        return view('user.teams.create');
    }

    public function store(Request $request)
    {
        // 1. ตรวจสอบข้อมูลแบบ Array (รับข้อมูลลูกทีมได้หลายคนพร้อมกัน)
        $validated = $request->validate([
            'name'                      => ['required', 'string', 'max:255'],
            'school_name'               => ['required', 'string', 'max:255'],
            // กฎของ Array ลูกทีม
            'members'                   => ['required', 'array', 'min:1'], // ต้องมีลูกทีมอย่างน้อย 1 คน
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
            // 2. ใช้ DB Transaction คลุมการทำงาน
            DB::transaction(function () use ($validated, $request) {
                
                // 2.1 สร้างทีมให้ User ปัจจุบัน
                $team = $request->user()->teams()->create([
                    'name'        => $validated['name'],
                    'school_name' => $validated['school_name'],
                ]);

                // 2.2 วนลูปสร้างลูกทีมทั้งหมด บันทึกเชื่อมกับทีมที่เพิ่งสร้าง
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

        // 2. Validate ข้อมูลแบบ Array
        $validated = $request->validate([
            'name'                      => ['required', 'string', 'max:255'],
            'school_name'               => ['required', 'string', 'max:255'],
            'members'                   => ['required', 'array', 'min:1'],
            'members.*.id'              => ['nullable', 'integer'], // 🚀 ต้องรับ ID มาด้วย เพื่อแยกระหว่างคนเก่า/คนใหม่
            'members.*.prefix_th'       => ['nullable', 'string', 'max:100'],
            'members.*.first_name_th'   => ['required', 'string', 'max:255'],
            'members.*.last_name_th'    => ['required', 'string', 'max:255'],
            'members.*.prefix_en'       => ['nullable', 'string', 'max:100'],
            'members.*.first_name_en'   => ['required', 'string', 'max:255'], // ปรับเป็น required ตามฟอร์ม
            'members.*.last_name_en'    => ['required', 'string', 'max:255'], // ปรับเป็น required ตามฟอร์ม
            'members.*.birth_date'      => ['required', 'date'],
            'members.*.shirt_size'      => ['nullable', 'string', 'max:10'],
        ]);

        try {
            DB::transaction(function () use ($validated, $team) {
                // 3. อัปเดตข้อมูลทีมหลัก (Parent)
                $team->update([
                    'name'        => $validated['name'],
                    'school_name' => $validated['school_name'],
                ]);

                // 4. THE SYNC LOGIC (จัดการลูกทีม)
                $submittedMembers = collect($validated['members']);
                
                // ดึงเฉพาะ ID ของลูกทีมที่ถูกส่งมา (กรอง null ทิ้งเผื่อเป็นคนใหม่)
                $submittedIds = $submittedMembers->pluck('id')->filter()->toArray();

                // 4.1 ลบลูกทีมที่ "ไม่มี" อยู่ในฟอร์มที่ส่งมา (แปลว่าถูกกดปุ่มกากบาททิ้งไปแล้ว)
                $team->members()->whereNotIn('id', $submittedIds)->delete();

                // 4.2 วนลูปเพื่อ อัปเดตคนเดิม หรือ สร้างคนใหม่
                foreach ($submittedMembers as $memberData) {
                    if (!empty($memberData['id'])) {
                        // มี ID = คนเก่า -> ทำการ Update
                        // ใช้ Arr::except เพื่อตัดช่อง 'id' ออก ป้องกันความผิดพลาดตอนเซฟลง DB
                        $team->members()
                             ->where('id', $memberData['id'])
                             ->update(Arr::except($memberData, ['id']));
                    } else {
                        // ไม่มี ID = คนใหม่ที่เพิ่งกดปุ่มเพิ่ม -> ทำการ Create
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
     * ลบทีมและลูกทีมทั้งหมด (Cascade Delete)
     */
    public function destroy(Request $request, Team $team)
    {
        // 🔒 Security Check
        if ($team->user_id !== $request->user()->id) {
            abort(403, 'คุณไม่มีสิทธิ์ลบทีมนี้');
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