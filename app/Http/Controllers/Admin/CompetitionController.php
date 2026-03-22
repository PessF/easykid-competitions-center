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
        $competitions = Competition::latest()->get();
        return view('admin.competitions.index', compact('competitions'));
    }

    public function store(Request $request)
    {
        $this->validateCompetition($request);

        try {
            $bannerPath = null;
            if ($request->hasFile('banner')) {
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
        $competition = Competition::findOrFail($id);
        $this->validateCompetition($request);

        try {
            // ดึงข้อมูลทั้งหมดที่อนุญาตให้แก้
            $data = $request->only([
                'name', 'location', 'description', 'latitude', 'longitude',
                'status', 'regis_start_date', 'regis_end_date', 'event_start_date', 'event_end_date'
            ]);

            if ($request->hasFile('banner')) {
                if ($competition->banner_url) {
                    Storage::disk('google')->delete($competition->banner_url);
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
                Storage::disk('google')->delete($competition->banner_url);
            }
            $competition->delete();
            return redirect()->route('admin.competitions.index')->with('success', 'ลบรายการแข่งขันเรียบร้อยแล้ว!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'ไม่สามารถลบได้: ' . $e->getMessage()]);
        }
    }

    public function showBanner($id)
    {
        $competition = Competition::findOrFail($id);
        if (!$competition->banner_url || !Storage::disk('google')->exists($competition->banner_url)) {
            abort(404);
        }
        $file = Storage::disk('google')->get($competition->banner_url);
        $mimeType = Storage::disk('google')->mimeType($competition->banner_url);
        return response($file, 200)->header('Content-Type', $mimeType);
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
            'status' => 'required|in:draft,registration,ongoing,completed',
            'regis_start_date' => 'nullable|date',
            'regis_end_date' => 'nullable|date|after_or_equal:regis_start_date',
            'event_start_date' => 'nullable|date|after:regis_end_date',
            'event_end_date' => 'nullable|date|after_or_equal:event_start_date',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ], [
            'name.required' => 'กรุณากรอกชื่อรายการแข่งขัน',
            'regis_end_date.after_or_equal' => 'วันปิดรับสมัครต้องไม่ต่ำกว่าวันเริ่มรับสมัคร',
            
            'event_start_date.after' => 'วันเริ่มการแข่งขันต้องเป็นวันหลังจากที่ปิดรับสมัครแล้วเท่านั้น',
            
            'event_end_date.after_or_equal' => 'วันจบการแข่งขันต้องไม่ต่ำกว่าวันเริ่มแข่ง',
        ]);
    }

    private function uploadBanner($file, $competitionName)
    {
        $safeCompName = $this->sanitizeFolderName($competitionName);
        $fileName = "banner_" . time() . "_" . uniqid() . "." . $file->getClientOriginalExtension();
        
        // โครงสร้าง: Competitions/{ชื่อการแข่งขัน}/Banner
        $fullPath = "Competitions/{$safeCompName}/Banner/$fileName";
        
        Storage::disk('google')->put($fullPath, file_get_contents($file));
        return $fullPath;
    }

    private function sanitizeFolderName($name)
    {
        // ลบช่องว่างส่วนเกิน และเปลี่ยนอักขระที่ห้ามใช้ในชื่อไฟล์ให้เป็นขีดล่าง (_)
        return trim(preg_replace('/[\\\\\/:\*\?"<>\|]/', '_', $name));
    }
}