<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตรวจสอบใบสมัคร | {{ $registration->regis_no }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    {{-- 🚀 เพิ่ม SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Kanit', sans-serif; background-color: #0a0a0a; color: #e5e7eb; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4 sm:p-8">

    <div class="w-full max-w-md bg-[#121212] rounded-2xl sm:rounded-[2rem] shadow-2xl overflow-hidden border border-white/5 relative">
        
        {{-- 🟢 HEADER: สถานะบัตร --}}
        @if(!empty($registration->checked_in_at))
            <div class="bg-[#1a1a1a] border-b border-amber-500/20 p-6 sm:p-8 text-center relative overflow-hidden">
                <div class="relative z-10 flex flex-col items-center">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-amber-500/10 border border-amber-500/20 rounded-full flex items-center justify-center shadow-[0_0_15px_rgba(245,158,11,0.2)] mb-3 sm:mb-4 transform scale-100 animate-pulse">
                        <i class="fas fa-clock text-3xl sm:text-4xl text-amber-400"></i>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-normal tracking-wide text-white">เข้างานเรียบร้อย</h1>
                    <p class="text-amber-400 text-xs sm:text-sm mt-2 sm:mt-2.5 font-normal bg-amber-500/10 border border-amber-500/20 px-4 py-1.5 rounded-md sm:rounded-lg">
                        เช็คอินเมื่อ: {{ \Carbon\Carbon::parse($registration->checked_in_at)->format('H:i น.') }}
                    </p>
                </div>
            </div>
        @else
            <div class="bg-[#1a1a1a] border-b border-emerald-500/20 p-6 sm:p-8 text-center relative overflow-hidden">
                <div class="relative z-10 flex flex-col items-center">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-emerald-500/10 border border-emerald-500/20 rounded-full flex items-center justify-center shadow-[0_0_15px_rgba(16,185,129,0.2)] mb-3 sm:mb-4">
                        <i class="fas fa-check text-3xl sm:text-4xl text-emerald-400"></i>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-normal tracking-wide text-white">ใบสมัครถูกต้อง</h1>
                    <p class="text-emerald-400 text-xs sm:text-sm mt-2 sm:mt-2.5 font-normal bg-emerald-500/10 border border-emerald-500/20 px-4 py-1.5 rounded-md sm:rounded-lg">พร้อมสำหรับการเช็คอิน</p>
                </div>
            </div>
        @endif

        {{-- 📋 BODY: ข้อมูลที่จะแสดง (โชว์ข้อมูลให้ทุกคนเห็นเหมือนกัน) --}}
        <div class="p-5 sm:p-8 pb-4">
            
            <div class="space-y-3 sm:space-y-4">
                {{-- รหัสใบสมัคร --}}
                <div class="text-center pb-3 border-b border-dashed border-white/10">
                    <p class="text-xs sm:text-sm font-normal text-gray-500 uppercase tracking-widest mb-1">รหัสใบสมัคร</p>
                    <p class="text-xl sm:text-2xl font-mono font-normal text-blue-400 tracking-wider">
                        {{ $registration->regis_no }}
                    </p>
                </div>

                {{-- งานแข่งขัน & รุ่น --}}
                <div class="grid grid-cols-1 gap-2.5 sm:gap-3 pt-2">
                    <div class="bg-blue-500/5 p-3.5 sm:p-4 rounded-xl sm:rounded-2xl border border-blue-500/10 flex items-center gap-3">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 shrink-0">
                            <i class="fas fa-trophy text-sm sm:text-lg"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs font-normal text-blue-400/80 uppercase leading-none mb-1 sm:mb-1.5">งานแข่งขัน</p>
                            <p class="text-sm sm:text-base font-normal text-white leading-tight truncate">{{ $registration->competition->name }}</p>
                        </div>
                    </div>
                    <div class="bg-amber-500/5 p-3.5 sm:p-4 rounded-xl sm:rounded-2xl border border-amber-500/10 flex items-center gap-3">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 shrink-0">
                            <i class="fas fa-medal text-sm sm:text-lg"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-xs font-normal text-amber-400/80 uppercase leading-none mb-1 sm:mb-1.5">รุ่นที่ลงสมัคร</p>
                            <p class="text-sm sm:text-base font-normal text-white leading-tight truncate">{{ $registration->competitionClass->name }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 📋 ข้อมูลรายละเอียด (ปลดล็อคให้ทุกคนเห็นแล้ว) --}}
            <div class="mt-6 sm:mt-8 pt-5 sm:pt-6 border-t border-white/5">
                <div class="space-y-4 sm:space-y-6">
                    
                    {{-- 🧑‍💼 ข้อมูลผู้ลงทะเบียน (เจ้าของบัตร) --}}
                    <div class="bg-indigo-500/5 p-4 sm:p-5 rounded-xl sm:rounded-2xl border border-indigo-500/10 relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-10 text-indigo-400 text-4xl sm:text-5xl">
                            <i class="fas fa-id-badge"></i>
                        </div>
                        <span class="text-[10px] sm:text-xs font-normal text-indigo-400 uppercase mb-1.5 sm:mb-2 block relative z-10">ผู้ลงทะเบียน (ผู้ควบคุมทีม)</span>
                        <div class="text-base sm:text-lg font-normal text-white mb-2.5 sm:mb-3 relative z-10">
                            {{ $registration->user->name ?? ($registration->user->first_name . ' ' . $registration->user->last_name) }}
                        </div>
                        <div class="flex flex-col gap-2 relative z-10">
                            <div class="text-xs sm:text-sm text-gray-400 flex items-center gap-2.5 sm:gap-3">
                                <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-md sm:rounded-lg bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 shrink-0"><i class="fas fa-envelope text-[10px] sm:text-xs"></i></div>
                                <span class="truncate">{{ $registration->user->email ?? '-' }}</span>
                            </div>
                            <div class="text-xs sm:text-sm text-gray-400 flex items-center gap-2.5 sm:gap-3">
                                <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-md sm:rounded-lg bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 shrink-0"><i class="fas fa-phone-alt text-[10px] sm:text-xs"></i></div>
                                <span>{{ $registration->user->phone ?? ($registration->user->phone_number ?? '-' ) }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- 🛡️ ข้อมูลทีม --}}
                    <div class="bg-[#1a1a1a] p-4 sm:p-5 rounded-xl sm:rounded-2xl border border-white/5 shadow-sm">
                        <div class="flex justify-between items-center gap-3 sm:gap-4 border-b border-white/5 pb-2.5 sm:pb-3 mb-2.5 sm:mb-3">
                            <span class="text-[10px] sm:text-sm font-normal text-gray-500 uppercase">ชื่อทีม</span>
                            <span class="text-sm sm:text-base font-normal text-gray-200 text-right truncate">{{ $registration->team->name }}</span>
                        </div>
                        <div class="flex justify-between items-center gap-3 sm:gap-4">
                            <span class="text-[10px] sm:text-sm font-normal text-gray-500 uppercase">สถาบัน</span>
                            <span class="text-sm sm:text-base font-normal text-gray-300 text-right truncate">{{ $registration->team->school_name ?? '-' }}</span>
                        </div>
                    </div>
                    
                    {{-- 👥 รายชื่อผู้เข้าแข่งขันแบบละเอียด --}}
                    <div>
                        <span class="text-[10px] sm:text-sm font-normal text-gray-500 uppercase tracking-wider flex items-center mb-2.5 sm:mb-3 pl-1">
                            สมาชิกในทีม 
                            <span class="bg-white/10 text-gray-300 px-2 py-0.5 rounded-md ml-2 text-[10px] sm:text-xs border border-white/5">{{ $registration->team->members->count() }}</span>
                        </span>
                        <div class="space-y-3 sm:space-y-4">
                            @foreach($registration->team->members as $index => $member)
                                <div class="bg-[#1a1a1a] p-4 sm:p-5 rounded-xl sm:rounded-2xl border border-white/5 shadow-sm hover:border-blue-500/30 transition-colors">
                                    {{-- ชื่อ --}}
                                    <div class="flex items-center gap-2.5 sm:gap-3 border-b border-white/5 pb-2.5 sm:pb-3 mb-2.5 sm:mb-3">
                                        <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-md sm:rounded-lg bg-blue-500/10 border border-blue-500/20 text-blue-400 flex items-center justify-center text-xs sm:text-sm font-normal shrink-0">
                                            {{ $index + 1 }}
                                        </div>
                                        <span class="text-sm sm:text-base font-normal text-white truncate">{{ $member->first_name_th }} {{ $member->last_name_th }}</span>
                                    </div>
                                    
                                    {{-- รายละเอียด: อายุ & ไซส์เสื้อ --}}
                                    <div class="flex items-center justify-between px-1 sm:px-2 text-[10px] sm:text-sm text-gray-400">
                                        <div class="flex items-center gap-1.5 sm:gap-2.5">
                                            <i class="fas fa-birthday-cake text-rose-400/80"></i>
                                            <span>อายุ: <strong class="text-gray-200 ml-1 font-normal">{{ $member->birth_date ? \Carbon\Carbon::parse($member->birth_date)->age . ' ปี' : '-' }}</strong></span>
                                        </div>
                                        <div class="w-px h-4 sm:h-5 bg-white/10"></div>
                                        <div class="flex items-center gap-1.5 sm:gap-2.5">
                                            <i class="fas fa-tshirt text-emerald-400/80"></i>
                                            <span>ไซส์เสื้อ: <strong class="text-gray-200 ml-1 font-normal uppercase">{{ $member->shirt_size ?? '-' }}</strong></span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="bg-[#0a0a0a] p-5 sm:p-6 text-center border-t border-white/5">
            
            {{-- 🚀 โซนปุ่มเช็คอิน (ล็อคให้โชว์เฉพาะแอดมิน/สตาฟเท่านั้น) --}}
            @if($isStaff)
                @if(empty($registration->checked_in_at))
                    {{-- กรณียังไม่เคยเช็คอิน: โชว์ปุ่มให้กด --}}
                    <form id="checkinForm" action="{{ URL::signedRoute('verify.ticket.checkin', ['reg_no' => $registration->regis_no]) }}" method="POST" class="mb-3 sm:mb-4">
                        @csrf
                        <button type="button" onclick="confirmCheckIn()" class="w-full py-3 sm:py-4 bg-emerald-600 hover:bg-emerald-500 text-white text-sm sm:text-lg font-normal rounded-xl sm:rounded-2xl transition-all shadow-lg shadow-emerald-500/20 flex items-center justify-center gap-2 focus:outline-none">
                            <i class="fas fa-sign-in-alt text-base sm:text-xl"></i> ยืนยันการเข้างาน (Check-in)
                        </button>
                    </form>
                @else
                    {{-- กรณีเช็คอินไปแล้ว: โชว์ปุ่มสถานะแบบกดไม่ได้ --}}
                    <div class="mb-3 sm:mb-4 w-full py-3 sm:py-4 bg-[#1a1a1a] text-gray-500 text-sm sm:text-lg font-normal rounded-xl sm:rounded-2xl flex items-center justify-center gap-2 cursor-not-allowed border border-white/5 border-dashed">
                        <i class="fas fa-check-circle text-base sm:text-xl opacity-60"></i> ตรวจสอบเรียบร้อยแล้ว
                    </div>
                @endif
                
                <a href="{{ url('/') }}" class="inline-block w-full py-2.5 sm:py-3.5 bg-[#1a1a1a] hover:bg-white/5 text-gray-300 hover:text-white text-xs sm:text-base font-normal rounded-xl sm:rounded-2xl transition-colors border border-white/5">
                    กลับหน้าหลัก
                </a>
            @else
                <button onclick="window.close()" class="w-full py-2.5 sm:py-3.5 bg-[#1a1a1a] hover:bg-white/5 text-gray-300 hover:text-white text-xs sm:text-base font-normal rounded-xl sm:rounded-2xl transition-colors border border-white/5 focus:outline-none">
                    ปิดหน้านี้
                </button>
            @endif
        </div>

    </div>

    {{-- 🚀 สคริปต์จัดการ SweetAlert --}}
    <script>
        function confirmCheckIn() {
            Swal.fire({
                title: 'ยืนยันการเช็คอิน?',
                text: "ต้องการบันทึกการเข้างานของทีมนี้ใช่หรือไม่?",
                icon: 'question',
                showCancelButton: true,
                background: '#1e1e1e', // Dark mode background
                color: '#fff', // Dark mode text color
                confirmButtonColor: '#059669', 
                cancelButtonColor: '#4b5563', 
                confirmButtonText: 'ยืนยันเข้างาน',
                cancelButtonText: 'ยกเลิก',
                fontFamily: 'Kanit',
                customClass: {
                    popup: 'rounded-2xl border border-white/10',
                    title: 'font-kanit font-normal',
                    htmlContainer: 'font-kanit font-normal text-gray-400',
                    confirmButton: 'font-kanit rounded-xl font-normal',
                    cancelButton: 'font-kanit rounded-xl font-normal'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'กำลังบันทึก...',
                        allowOutsideClick: false,
                        background: '#1e1e1e',
                        color: '#fff',
                        didOpen: () => {
                            Swal.showLoading()
                        },
                        customClass: {
                            popup: 'rounded-2xl border border-white/10',
                            title: 'font-kanit font-normal'
                        }
                    });
                    document.getElementById('checkinForm').submit();
                }
            })
        }

        @if(session('success'))
            Swal.fire({
                title: 'สำเร็จ!',
                text: "{{ session('success') }}",
                icon: 'success',
                background: '#1e1e1e',
                color: '#fff',
                timer: 2000,
                timerProgressBar: true,
                showConfirmButton: false,
                customClass: {
                    popup: 'rounded-2xl border border-white/10',
                    title: 'font-kanit font-normal',
                    htmlContainer: 'font-kanit text-gray-400 font-normal'
                },
                willClose: () => {
                    window.close();
                    window.location.href = "{{ url('/') }}";
                }
            });
        @endif
    </script>

</body>
</html>