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
        body { font-family: 'Kanit', sans-serif; background-color: #f3f4f6; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4 sm:p-8">

    <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100 relative">
        
        {{-- 🟢 HEADER: สถานะบัตร --}}
        @if(!empty($registration->checked_in_at))
            <div class="bg-gradient-to-br from-amber-400 to-amber-500 p-8 text-center text-white relative overflow-hidden">
                <div class="absolute inset-0 bg-black/10"></div>
                <div class="relative z-10 flex flex-col items-center">
                    <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center shadow-lg mb-4 transform scale-100 animate-pulse">
                        <i class="fas fa-clock text-4xl text-amber-500"></i>
                    </div>
                    <h1 class="text-2xl font-normal tracking-wide">เข้างานเรียบร้อย</h1>
                    <p class="text-amber-50 text-base mt-2 font-normal bg-black/20 px-5 py-1.5 rounded-full">
                        เช็คอินเมื่อ: {{ \Carbon\Carbon::parse($registration->checked_in_at)->format('H:i น.') }}
                    </p>
                </div>
            </div>
        @else
            <div class="bg-gradient-to-br from-emerald-400 to-emerald-500 p-8 text-center text-white relative overflow-hidden">
                <div class="absolute inset-0 bg-black/10"></div>
                <div class="relative z-10 flex flex-col items-center">
                    <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center shadow-lg mb-4">
                        <i class="fas fa-check text-4xl text-emerald-500"></i>
                    </div>
                    <h1 class="text-2xl font-normal tracking-wide">ใบสมัครถูกต้อง</h1>
                    <p class="text-emerald-50 text-base mt-2 font-normal bg-black/20 px-5 py-1.5 rounded-full">พร้อมสำหรับการเช็คอิน</p>
                </div>
            </div>
        @endif

        {{-- 📋 BODY: ข้อมูลที่จะแสดง (โชว์ข้อมูลให้ทุกคนเห็นเหมือนกัน) --}}
        <div class="p-6 sm:p-8 pb-4">
            
            <div class="space-y-4">
                {{-- รหัสใบสมัคร --}}
                <div class="text-center pb-2 border-b border-dashed border-gray-200">
                    <p class="text-sm font-normal text-gray-500 uppercase tracking-wider mb-1">รหัสใบสมัคร</p>
                    <p class="text-2xl font-mono font-normal text-blue-600 tracking-wider">
                        {{ $registration->regis_no }}
                    </p>
                </div>

                {{-- งานแข่งขัน & รุ่น --}}
                <div class="grid grid-cols-1 gap-3 pt-2">
                    <div class="bg-blue-50/50 p-4 rounded-2xl border border-blue-100 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 shrink-0">
                            <i class="fas fa-trophy text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-normal text-blue-500 uppercase leading-none mb-1.5">งานแข่งขัน</p>
                            <p class="text-base font-normal text-gray-800 leading-tight">{{ $registration->competition->name }}</p>
                        </div>
                    </div>
                    <div class="bg-amber-50/50 p-4 rounded-2xl border border-amber-100 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center text-amber-500 shrink-0">
                            <i class="fas fa-medal text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-normal text-amber-500 uppercase leading-none mb-1.5">รุ่นที่ลงสมัคร</p>
                            <p class="text-base font-normal text-gray-800 leading-tight">{{ $registration->competitionClass->name }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 📋 ข้อมูลรายละเอียด (ปลดล็อคให้ทุกคนเห็นแล้ว) --}}
            <div class="mt-8 pt-6 border-t-2 border-gray-100">
                <div class="space-y-6">
                    
                    {{-- 🧑‍💼 ข้อมูลผู้ลงทะเบียน (เจ้าของบัตร) --}}
                    <div class="bg-indigo-50 p-5 rounded-2xl border border-indigo-100 relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-5 text-indigo-900 text-5xl">
                            <i class="fas fa-id-badge"></i>
                        </div>
                        <span class="text-xs font-normal text-indigo-500 uppercase mb-2 block relative z-10">ผู้ลงทะเบียน (ผู้ควบคุมทีม)</span>
                        <div class="text-lg font-normal text-gray-900 mb-3 relative z-10">
                            {{ $registration->user->name ?? ($registration->user->first_name . ' ' . $registration->user->last_name) }}
                        </div>
                        <div class="flex flex-col gap-2.5 relative z-10">
                            <div class="text-sm text-gray-700 flex items-center gap-3">
                                <div class="w-7 h-7 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 shrink-0"><i class="fas fa-envelope text-xs"></i></div>
                                <span class="truncate">{{ $registration->user->email ?? '-' }}</span>
                            </div>
                            <div class="text-sm text-gray-700 flex items-center gap-3">
                                <div class="w-7 h-7 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 shrink-0"><i class="fas fa-phone-alt text-xs"></i></div>
                                <span>{{ $registration->user->phone ?? ($registration->user->phone_number ?? '-' ) }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- 🛡️ ข้อมูลทีม --}}
                    <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
                        <div class="flex justify-between items-center gap-4 border-b border-gray-100 pb-3 mb-3">
                            <span class="text-sm font-normal text-gray-500 uppercase">ชื่อทีม</span>
                            <span class="text-base font-normal text-gray-900 text-right">{{ $registration->team->name }}</span>
                        </div>
                        <div class="flex justify-between items-center gap-4">
                            <span class="text-sm font-normal text-gray-500 uppercase">สถาบัน</span>
                            <span class="text-base font-normal text-gray-800 text-right">{{ $registration->team->school_name ?? '-' }}</span>
                        </div>
                    </div>
                    
                    {{-- 👥 รายชื่อผู้เข้าแข่งขันแบบละเอียด --}}
                    <div>
                        <span class="text-sm font-normal text-gray-500 uppercase tracking-wider block mb-3 pl-1">
                            สมาชิกในทีม <span class="bg-gray-200 text-gray-700 px-2.5 py-0.5 rounded-full ml-1 text-xs">{{ $registration->team->members->count() }}</span>
                        </span>
                        <div class="space-y-4">
                            @foreach($registration->team->members as $index => $member)
                                <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm hover:border-blue-300 transition-colors">
                                    {{-- ชื่อ --}}
                                    <div class="flex items-center gap-3 border-b border-gray-100 pb-3 mb-3">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-100 to-blue-200 text-blue-700 flex items-center justify-center text-sm font-normal shrink-0 shadow-inner">
                                            {{ $index + 1 }}
                                        </div>
                                        <span class="text-base font-normal text-gray-900">{{ $member->first_name_th }} {{ $member->last_name_th }}</span>
                                    </div>
                                    
                                    {{-- รายละเอียด: อายุ & ไซส์เสื้อ --}}
                                    <div class="flex items-center justify-between px-2 text-sm text-gray-600">
                                        <div class="flex items-center gap-2.5">
                                            <i class="fas fa-birthday-cake text-rose-400"></i>
                                            <span>อายุ: <strong class="text-gray-900 ml-1 font-normal">{{ $member->birth_date ? \Carbon\Carbon::parse($member->birth_date)->age . ' ปี' : '-' }}</strong></span>
                                        </div>
                                        <div class="w-px h-5 bg-gray-200"></div>
                                        <div class="flex items-center gap-2.5">
                                            <i class="fas fa-tshirt text-emerald-500"></i>
                                            <span>ไซส์เสื้อ: <strong class="text-gray-900 ml-1 font-normal uppercase">{{ $member->shirt_size ?? '-' }}</strong></span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="bg-gray-50 p-6 text-center border-t border-gray-200">
            
            {{-- 🚀 โซนปุ่มเช็คอิน (ล็อคให้โชว์เฉพาะแอดมิน/สตาฟเท่านั้น) --}}
            @if($isStaff)
                @if(empty($registration->checked_in_at))
                    {{-- กรณียังไม่เคยเช็คอิน: โชว์ปุ่มให้กด --}}
                    <form id="checkinForm" action="{{ URL::signedRoute('verify.ticket.checkin', ['reg_no' => $registration->regis_no]) }}" method="POST" class="mb-4">
                        @csrf
                        <button type="button" onclick="confirmCheckIn()" class="w-full py-4 bg-emerald-600 hover:bg-emerald-700 text-white text-lg font-normal rounded-2xl transition-all shadow-xl shadow-emerald-600/30 flex items-center justify-center gap-2 hover:-translate-y-1">
                            <i class="fas fa-sign-in-alt text-xl"></i> ยืนยันการเข้างาน (Check-in)
                        </button>
                    </form>
                @else
                    {{-- กรณีเช็คอินไปแล้ว: โชว์ปุ่มสถานะแบบกดไม่ได้ --}}
                    <div class="mb-4 w-full py-4 bg-gray-200 text-gray-500 text-lg font-normal rounded-2xl flex items-center justify-center gap-2 cursor-not-allowed border-2 border-gray-300 border-dashed">
                        <i class="fas fa-check-circle text-xl"></i> ตรวจสอบเรียบร้อยแล้ว
                    </div>
                @endif
                
                <a href="{{ url('/') }}" class="inline-block w-full py-3.5 bg-gray-900 hover:bg-black text-white text-base font-normal rounded-2xl transition-colors shadow-lg shadow-gray-900/20">
                    กลับหน้าหลัก
                </a>
            @else
                <button onclick="window.close()" class="w-full py-3.5 bg-gray-200 hover:bg-gray-300 text-gray-800 text-base font-normal rounded-2xl transition-colors">
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
                confirmButtonColor: '#059669', 
                cancelButtonColor: '#6b7280', 
                confirmButtonText: 'ยืนยันเข้างาน',
                cancelButtonText: 'ยกเลิก',
                fontFamily: 'Kanit',
                customClass: {
                    title: 'font-kanit font-normal',
                    htmlContainer: 'font-kanit',
                    confirmButton: 'font-kanit rounded-xl font-normal',
                    cancelButton: 'font-kanit rounded-xl font-normal'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'กำลังบันทึก...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        },
                        customClass: {
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
                timer: 2000,
                timerProgressBar: true,
                showConfirmButton: false,
                customClass: {
                    title: 'font-kanit font-normal',
                    htmlContainer: 'font-kanit'
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