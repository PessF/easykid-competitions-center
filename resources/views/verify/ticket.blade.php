<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตรวจสอบใบสมัคร | {{ $registration->regis_no }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Kanit', sans-serif; background-color: #f3f4f6; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4 sm:p-8">

    <div class="w-full max-w-md bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
        
        {{-- 🟢 HEADER: สถานะบัตร --}}
        <div class="bg-emerald-500 p-8 text-center text-white relative overflow-hidden">
            <div class="absolute inset-0 bg-black/10"></div>
            <div class="relative z-10 flex flex-col items-center">
                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center shadow-lg mb-4">
                    <i class="fas fa-check text-4xl text-emerald-500"></i>
                </div>
                <h1 class="text-2xl font-bold tracking-wide">ใบสมัครถูกต้อง</h1>
                <p class="text-emerald-100 text-sm mt-1 font-medium">สแกนสำเร็จ ข้อมูลตรงกับในระบบ</p>
            </div>
        </div>

        {{-- 📋 BODY: ข้อมูลที่จะแสดง --}}
        <div class="p-6 sm:p-8">
            
            <div class="space-y-4">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">รหัสใบสมัคร</p>
                    <p class="text-lg font-mono font-semibold text-gray-900 bg-gray-50 p-2.5 rounded-xl border border-gray-100 text-center">
                        {{ $registration->regis_no }}
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-4 pt-2">
                    <div class="bg-blue-50/50 p-3.5 rounded-xl border border-blue-100/50">
                        <p class="text-[10px] font-bold text-blue-400 uppercase mb-0.5">งานแข่งขัน</p>
                        <p class="text-sm font-medium text-gray-800">{{ $registration->competition->name }}</p>
                    </div>
                    <div class="bg-amber-50/50 p-3.5 rounded-xl border border-amber-100/50">
                        <p class="text-[10px] font-bold text-amber-500 uppercase mb-0.5">รุ่นที่ลงสมัคร</p>
                        <p class="text-sm font-medium text-gray-800">{{ $registration->competitionClass->name }}</p>
                    </div>
                </div>
            </div>

            {{-- 🔐 โซนพิเศษ: โชว์เฉพาะ Admin หรือ Staff เท่านั้น --}}
            @if($isStaff)
                <div class="mt-6 pt-6 border-t-2 border-dashed border-gray-200">
                    <div class="flex items-center gap-2 mb-4">
                        <i class="fas fa-shield-alt text-blue-600"></i>
                        <span class="text-xs font-bold text-blue-600 uppercase tracking-widest">ข้อมูลสำหรับสตาฟ</span>
                    </div>

                    <div class="space-y-4">
                        <div class="flex justify-between items-start gap-4">
                            <span class="text-xs font-medium text-gray-500 shrink-0">ชื่อทีม</span>
                            <span class="text-sm font-semibold text-gray-900 text-right">{{ $registration->team->name }}</span>
                        </div>
                        <div class="flex justify-between items-start gap-4">
                            <span class="text-xs font-medium text-gray-500 shrink-0">สถาบัน</span>
                            <span class="text-sm font-medium text-gray-900 text-right">{{ $registration->team->school_name ?? '-' }}</span>
                        </div>
                        
                        <div class="pt-2">
                            <span class="text-xs font-medium text-gray-500 block mb-2">รายชื่อผู้เข้าแข่งขัน ({{ $registration->team->members->count() }} คน)</span>
                            <ul class="space-y-2">
                                @foreach($registration->team->members as $index => $member)
                                    <li class="flex items-center gap-2 text-sm text-gray-800 bg-gray-50 p-2 rounded-lg border border-gray-100">
                                        <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">{{ $index + 1 }}</span>
                                        {{ $member->first_name_th }} {{ $member->last_name_th }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @else
                <div class="mt-6 pt-4 border-t border-gray-100 text-center">
                    <p class="text-[10px] text-gray-400">
                        <i class="fas fa-lock mr-1"></i> ซ่อนข้อมูลส่วนบุคคล (แสดงเฉพาะเจ้าหน้าที่)
                    </p>
                </div>
            @endif

        </div>

        <div class="bg-gray-50 p-5 text-center border-t border-gray-100">
            @if($isStaff)
                <a href="{{ url('/') }}" class="inline-block w-full py-3 bg-gray-900 hover:bg-black text-white text-sm font-medium rounded-xl transition-colors shadow-lg shadow-gray-900/20">
                    กลับหน้าหลัก
                </a>
            @else
                <button onclick="window.close()" class="text-sm font-medium text-gray-500 hover:text-gray-900">
                    ปิดหน้านี้
                </button>
            @endif
        </div>

    </div>

</body>
</html>