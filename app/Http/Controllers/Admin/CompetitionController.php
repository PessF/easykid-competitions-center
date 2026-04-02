<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompetitionController extends Controller
{
    public function index()
    {
        $competitions = Competition::withCount(['classes', 'registrations'])->latest()->paginate(10);
        return view('admin.competitions.index', compact('competitions'));
    }

    public function store(Request $request)
    {
        if (empty($request->all()) && $request->server('CONTENT_LENGTH') > 0) {
            return back()->withInput()->withErrors(['banner' => 'ไฟล์มีขนาดใหญ่เกินไป (ระบบรองรับสูงสุด 20MB)']);
        }

        $this->validateCompetition($request);

        try {
            $bannerPath = null;
            if ($request->hasFile('banner')) {
                // 🚀 ด่านที่ 2: เช็คไฟล์พัง/ไม่สมบูรณ์
                if (!$request->file('banner')->isValid()) {
                    return back()->withInput()->withErrors(['banner' => 'ไฟล์รูปภาพแบนเนอร์ไม่สมบูรณ์ กรุณาลองใหม่อีกครั้ง']);
                }
                $bannerPath = $this->uploadBanner($request->file('banner'), $request->name);
            }

            Competition::create([
                'name' => $request->name,
                'location' => $request->location,
                'description' => $request->description,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'status' => $request->status,
                'regis_start_date' => $request->regis_start_date,
                'regis_end_date' => $request->regis_end_date,
                'event_start_date' => $request->event_start_date,
                'event_end_date' => $request->event_end_date,
                'banner_url' => $bannerPath,
            ]);
            return redirect()->route('admin.competitions.index')->with('success', 'สร้างงานแข่งขันใหม่เรียบร้อยแล้ว!');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'ระบบขัดข้อง: ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, string $id)
    {
        // 🚀 ปรับเป็น 20MB
        if (empty($request->all()) && $request->server('CONTENT_LENGTH') > 0) {
            return back()->withInput()->withErrors(['banner' => 'ไฟล์มีขนาดใหญ่เกินไป (ระบบรองรับสูงสุด 20MB)']);
        }

        $competition = Competition::findOrFail($id);
        $this->validateCompetition($request);

        try {
            $data = $request->only([
                'name', 'location', 'description', 'latitude', 'longitude',
                'status', 'regis_start_date', 'regis_end_date', 'event_start_date', 'event_end_date'
            ]);

            if ($request->hasFile('banner')) {
                if (!$request->file('banner')->isValid()) {
                    return back()->withInput()->withErrors(['banner' => 'ไฟล์รูปภาพแบนเนอร์ไม่สมบูรณ์ กรุณาลองใหม่อีกครั้ง']);
                }

                if ($competition->banner_url) {
                    Storage::disk('public')->delete($competition->banner_url);
                }
                $data['banner_url'] = $this->uploadBanner($request->file('banner'), $request->name);
            }

            $competition->update($data);
            return redirect()->route('admin.competitions.index')->with('success', 'อัปเดตข้อมูลงานแข่งขันแล้ว!');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'ไม่สามารถแก้ไขได้: ' . $e->getMessage()]);
        }
    }

    public function destroy(string $id)
    {
        try {
            $competition = Competition::findOrFail($id);
            if ($competition->banner_url) {
                Storage::disk('public')->delete($competition->banner_url);
            }
            $competition->delete();
            return redirect()->route('admin.competitions.index')->with('success', 'ลบรายการแข่งขันเรียบร้อยแล้ว!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'ไม่สามารถลบได้: ' . $e->getMessage()]);
        }
    }

    // --- Helper Functions ---

    private function validateCompetition(Request $request)
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'status' => 'required|in:draft,published,cancelled',
            'regis_start_date' => 'nullable|date',
            'regis_end_date' => 'nullable|date|after_or_equal:regis_start_date|before_or_equal:event_start_date',
            'event_start_date' => 'nullable|date|after:regis_end_date',
            'event_end_date' => 'nullable|date|after_or_equal:event_start_date',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg|max:20480',
        ], [
            'name.required' => 'กรุณากรอกชื่อรายการแข่งขัน',
            'status.in' => 'สถานะไม่ถูกต้อง กรุณาเลือกใหม่',
            'regis_end_date.after_or_equal' => 'วันปิดรับสมัครต้องไม่ต่ำกว่าวันเริ่มรับสมัคร',
            'regis_end_date.before_or_equal' => 'วันปิดรับสมัครต้องไม่เลยวันเริ่มแข่งขัน',
            'event_start_date.after' => 'วันเริ่มการแข่งขันต้องเป็นวันหลังจากที่ปิดรับสมัครแล้วเท่านั้น',
            'event_end_date.after_or_equal' => 'วันจบการแข่งขันต้องไม่ต่ำกว่าวันเริ่มแข่ง',
            'banner.max' => 'ขนาดไฟล์แบนเนอร์ต้องไม่เกิน 20MB',
            'banner.image' => 'ไฟล์แบนเนอร์ต้องเป็นรูปภาพเท่านั้น',
        ]);
    }

    private function uploadBanner($file, $competitionName)
    {
        $safeCompName = $this->sanitizeFolderName($competitionName);
        $fileName = "banner_" . time() . "_" . uniqid() . "." . $file->getClientOriginalExtension();
        
        $folderPath = "competitions/{$safeCompName}/banner";
        
        return $file->storeAs($folderPath, $fileName, 'public');
    }

    private function sanitizeFolderName($name)
    {
        return trim(preg_replace('/[\\\\\/:\*\?"<>\|]/', '_', $name));
    }
}