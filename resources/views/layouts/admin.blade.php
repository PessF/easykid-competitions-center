<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="{{ asset('images/favicon.png?v=' . time()) }}" type="image/png">

    <title>{{ $title ?? 'Easykids Competitions - Admin Panel' }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=kanit:300,400,500,600,700,800,900&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* 🚀 มาตรการเด็ดขาด: ซ่อน Scrollbar ของ "ทุก Element" ทั้งเว็บ แต่ยัง Scroll ได้ปกติ */
        *::-webkit-scrollbar {
            display: none !important;
        }

        * {
            -ms-overflow-style: none !important;
            /* สำหรับ IE และ Edge */
            scrollbar-width: none !important;
            /* สำหรับ Firefox */
        }
    </style>
</head>

<body
    class="font-sans antialiased bg-gray-50 dark:bg-[#0a0a0a] text-gray-900 leading-relaxed transition-colors duration-300"
    x-data="{ mobileSidebarOpen: false }">

    <div class="flex h-screen overflow-hidden">

        {{-- 1. Backdrop สำหรับมือถือ (คลิกส่วนมืดแล้วปิด Sidebar) --}}
        <div x-show="mobileSidebarOpen" @click="mobileSidebarOpen = false"
            class="fixed inset-0 z-40 bg-black/50 lg:hidden transition-opacity duration-300"
            x-transition:enter="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="opacity-100"
            x-transition:leave-end="opacity-0" style="display: none;">
        </div>

        {{-- 2. Sidebar --}}
        <aside :class="mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-50 w-64 flex-shrink-0 bg-white dark:bg-[#0f0f0f] border-r border-gray-100 dark:border-white/5 transition-transform duration-300 transform -translate-x-full lg:static lg:translate-x-0 overflow-y-auto">

            @php
                $pendingPaymentsCount = \App\Models\Registration::where('status', 'waiting_verify')->count();
                // 🚀 เช็คสิทธิ์ User ปัจจุบัน
                $userRole = Auth::user()->role ?? 'staff';
            @endphp

            <div class="flex items-center justify-between px-6 py-8">
                <span class="text-xl font-semibold tracking-tighter text-black dark:text-white uppercase">
                    {{ $userRole === 'admin' ? 'Admin' : 'Staff' }}<span class="text-gray-400 pl-2">Panel</span>
                </span>
                <button @click="mobileSidebarOpen = false" class="lg:hidden text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- ดักจับการคลิกเมนูบนมือถือให้พับเก็บอัตโนมัติ --}}
            <nav class="px-4 pb-4 space-y-1" @click="if(window.innerWidth < 1024) mobileSidebarOpen = false">
                
                {{-- =================================== --}}
                {{-- 1. DASHBOARD (เห็นทั้ง Admin และ Staff) --}}
                {{-- =================================== --}}
                <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Main</div>
                <x-admin-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                    <i class="fas fa-chart-pie w-5 h-5 mr-3 flex items-center justify-center"></i>
                    {{ __('Dashboard') }}
                </x-admin-nav-link>

                {{-- =================================== --}}
                {{-- 2. OPERATION --}}
                {{-- =================================== --}}
                <div class="px-4 pt-6 pb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Operation</div>

                {{-- 🚀 เฉพาะ Admin ที่จัดการงานแข่งขันได้ --}}
                @if($userRole === 'admin')
                    <x-admin-nav-link :href="route('admin.competitions.index')" :active="request()->routeIs('admin.competitions.*')">
                        <i class="fas fa-trophy w-5 h-5 mr-3 flex items-center justify-center"></i>
                        {{ __('จัดการงานแข่งขัน') }}
                    </x-admin-nav-link>
                @endif

                {{-- 🚀 ทั้ง Admin และ Staff สามารถดูรายชื่อการสมัคร (Teams) ได้ --}}
                <x-admin-nav-link :href="route('admin.teams.index')" :active="request()->routeIs('admin.teams.*')">
                    <i class="fas fa-users w-5 h-5 mr-3 flex items-center justify-center"></i>
                    {{ __('รายชื่อการสมัคร') }}
                </x-admin-nav-link>

                {{-- 🚀 เฉพาะ Admin ที่จัดการเรื่องเงิน (Payments) ได้ --}}
                @if($userRole === 'admin')
                    <x-admin-nav-link :href="route('admin.payments.index')" :active="request()->routeIs('admin.payments.*')">
                        <div class="flex items-center justify-between w-full">
                            <div class="flex items-center">
                                <i class="fas fa-file-invoice-dollar w-5 h-5 mr-3 flex items-center justify-center"></i>
                                {{ __('ตรวจการชำระเงิน') }}
                            </div>
                            @if ($pendingPaymentsCount > 0)
                                <span
                                    class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 text-[10px] font-bold text-white bg-red-500 rounded-full shadow-sm animate-pulse-slow">
                                    {{ $pendingPaymentsCount > 99 ? '99+' : $pendingPaymentsCount }}
                                </span>
                            @endif
                        </div>
                    </x-admin-nav-link>
                @endif

                {{-- 🚀 ตรวจสอบสิทธิ์: แสดงเฉพาะ Admin เท่านั้น --}}
                @if($userRole === 'admin')
                    {{-- =================================== --}}
                    {{-- 3. MASTER DATA --}}
                    {{-- =================================== --}}
                    <div class="px-4 pt-6 pb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Master Data</div>

                    <x-admin-nav-link :href="route('admin.category-settings')" :active="request()->routeIs('admin.category-settings')">
                        <i class="fas fa-tags w-5 h-5 mr-3 flex items-center justify-center"></i>
                        {{ __('ตั้งค่าหมวดหมู่') }}
                    </x-admin-nav-link>

                    <x-admin-nav-link :href="route('admin.robot-models.index')" :active="request()->routeIs('admin.robot-models.index')">
                        <i class="fas fa-robot w-5 h-5 mr-3 flex items-center justify-center"></i>
                        {{ __('คลังแม่แบบหุ่นยนต์') }}
                    </x-admin-nav-link>

                    {{-- =================================== --}}
                    {{-- 4. SYSTEM --}}
                    {{-- =================================== --}}
                    <div class="px-4 pt-6 pb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">System</div>

                    <x-admin-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                        <i class="fas fa-user-cog w-5 h-5 mr-3 flex items-center justify-center"></i>
                        {{ __('จัดการผู้ใช้งาน') }}
                    </x-admin-nav-link>
                @endif
            </nav>
        </aside>

        {{-- 3. Main Content Area --}}
        <div class="flex-1 flex flex-col overflow-hidden w-full transition-all duration-300">
            <header
                class="flex items-center justify-between px-4 lg:px-8 py-4 bg-white dark:bg-[#0f0f0f] border-b border-gray-100 dark:border-white/5">
                <div class="flex items-center">
                    <button @click="mobileSidebarOpen = true" class="mr-4 text-gray-500 lg:hidden focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <div class="flex items-center">
                        <div class="h-1.5 w-1.5 rounded-full bg-green-500 mr-2 animate-pulse"></div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">System
                            Active</span>
                    </div>
                </div>

                <div class="flex items-center space-x-4 lg:space-x-6">
                    <div
                        class="hidden sm:flex flex-col items-end leading-none border-r border-gray-100 dark:border-white/10 pr-6 font-medium">
                        <span class="text-sm font-bold dark:text-white">{{ Auth::user()->name ?? 'User' }}</span>
                        {{-- 🚀 แสดงตำแหน่งอิงตามสิทธิ์ผู้ใช้งาน --}}
                        <span class="text-[12px] text-gray-400 uppercase tracking-tighter mt-1">
                            {{ $userRole === 'admin' ? 'Administrator' : 'Staff' }}
                        </span>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="group flex items-center text-gray-400 hover:text-red-500 transition-colors duration-300">
                            <span
                                class="hidden xs:inline text-xs font-bold uppercase tracking-widest mr-2">Logout</span>
                            <div
                                class="p-2 rounded-lg bg-gray-50 dark:bg-white/5 group-hover:bg-red-50 dark:group-hover:bg-red-500/10 transition-colors duration-300">
                                <i
                                    class="fas fa-sign-out-alt w-4 h-4 transform group-hover:translate-x-1 transition-transform duration-300 flex items-center justify-center"></i>
                            </div>
                        </button>
                    </form>
                </div>
            </header>

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 dark:bg-[#0a0a0a] p-4 lg:p-8">
                {{ $slot }}
            </main>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- 🚀 GLOBAL SWEETALERT2 & HELPER FUNCTIONS --}}
    {{-- ========================================================= --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // เช็คว่า User ใช้ Dark Mode อยู่หรือไม่
            const isDark = document.documentElement.classList.contains('dark');
            const swalConfig = {
                background: isDark ? '#1a1a1a' : '#ffffff',
                color: isDark ? '#ffffff' : '#111827',
                customClass: {
                    popup: 'rounded-2xl border border-gray-100 dark:border-gray-800 shadow-xl font-kanit',
                    confirmButton: 'rounded-xl px-6 py-2.5 font-semibold tracking-wide',
                    cancelButton: 'rounded-xl px-6 py-2.5 font-semibold tracking-wide'
                }
            };

            // 1. ตรวจจับ Session Success
            @if (session('success'))
                Swal.fire({
                    ...swalConfig,
                    icon: 'success',
                    title: 'สำเร็จ!',
                    text: "{{ session('success') }}",
                    confirmButtonColor: '#2563eb', // blue-600
                    timer: 2000,
                    showConfirmButton: false
                });
            @endif

            // 2. ตรวจจับ Session Error
            @if (session('error'))
                Swal.fire({
                    ...swalConfig,
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด!',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#ef4444', // red-500
                    confirmButtonText: 'ตกลง'
                });
            @endif

            // 3. ตรวจจับ Validation Errors
            @if ($errors->any())
                let errorHtml = '<ul class="text-sm text-red-500 text-left list-disc list-inside mt-3 space-y-1">';
                @foreach ($errors->all() as $error)
                    errorHtml += '<li>{{ $error }}</li>';
                @endforeach
                errorHtml += '</ul>';

                Swal.fire({
                    ...swalConfig,
                    icon: 'warning',
                    title: 'ข้อมูลไม่ถูกต้อง ⚠️',
                    html: '<p class="text-gray-600 dark:text-gray-400 text-sm mb-2 text-left">กรุณาตรวจสอบข้อมูลด้านล่างให้ถูกต้อง:</p>' +
                        errorHtml,
                    confirmButtonColor: '#f59e0b', // amber-500
                    confirmButtonText: 'แก้ไขข้อมูล'
                });
            @endif
        });

        /**
         * 🚀 GLOBAL Helper: สำหรับลบข้อมูล (เรียกใช้ได้ทุกหน้า)
         * ตัวอย่างการใช้: <button onclick="confirmDelete('url/to/delete', 'ชื่อรายการ')">ลบ</button>
         */
        window.confirmDelete = function(url, name) {
            const isDark = document.documentElement.classList.contains('dark');
            Swal.fire({
                title: 'ยืนยันการลบ?',
                text: `คุณกำลังจะลบ "${name}" ข้อมูลจะไม่สามารถกู้คืนได้!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'ยืนยันการลบ',
                cancelButtonText: 'ยกเลิก',
                background: isDark ? '#1a1a1a' : '#ffffff',
                color: isDark ? '#ffffff' : '#111827',
                customClass: {
                    popup: 'rounded-2xl border border-gray-100 dark:border-gray-800 shadow-xl font-kanit',
                    confirmButton: 'rounded-xl px-6 py-2.5 font-semibold tracking-wide',
                    cancelButton: 'rounded-xl px-6 py-2.5 font-semibold tracking-wide'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // สร้าง Form จำลองขึ้นมาส่งค่า DELETE อัตโนมัติ
                    let form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;
                    form.innerHTML = `@csrf @method('DELETE')`;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
</body>

</html>