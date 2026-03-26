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

    <!-- maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="font-sans antialiased bg-gray-50 dark:bg-[#0a0a0a] text-gray-900 leading-relaxed transition-colors duration-300"
    x-data="{ mobileSidebarOpen: false }">

    <div class="flex h-screen overflow-hidden">

        {{-- 1. Backdrop สำหรับมือถือ (คลิกส่วนมืดแล้วปิด Sidebar) --}}
        <div x-show="mobileSidebarOpen" @click="mobileSidebarOpen = false"
            class="fixed inset-0 z-40 bg-black/50 lg:hidden transition-opacity duration-300"
            x-transition:enter="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="opacity-100"
            x-transition:leave-end="opacity-0">
        </div>

        {{-- 2. Sidebar --}}
        <aside :class="mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-50 w-64 flex-shrink-0 bg-white dark:bg-[#0f0f0f] border-r border-gray-100 dark:border-white/5 transition-transform duration-300 transform lg:static lg:translate-x-0 overflow-y-auto">

            <div class="flex items-center justify-between px-6 py-8">
                <span class="text-xl font-semibold tracking-tighter text-black dark:text-white uppercase">
                    Admin<span class="text-gray-400 pl-2">Panel</span>
                </span>
                <button @click="mobileSidebarOpen = false" class="lg:hidden text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <nav class="px-4 pb-4 space-y-1">
                {{-- =================================== --}}
                {{-- 1. DASHBOARD --}}
                {{-- =================================== --}}
                <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Main</div>
                <x-admin-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    {{ __('Dashboard') }}
                </x-admin-nav-link>

                {{-- =================================== --}}
                {{-- 2. OPERATION (งานหลักแอดมิน) --}}
                {{-- =================================== --}}
                <div class="px-4 pt-6 pb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Operation</div>
                
                <x-admin-nav-link :href="route('admin.competitions.index')" :active="request()->routeIs('admin.competitions.*')">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    {{ __('จัดการงานแข่งขัน') }}
                </x-admin-nav-link>

                {{-- รอทำหน้า Index สำหรับ Teams --}}
                <x-admin-nav-link :href="route('admin.teams.index')" :active="request()->routeIs('admin.teams.*')">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    {{ __('รายชื่อทีมผู้สมัคร') }}
                </x-admin-nav-link>

                <x-admin-nav-link href="#" :active="false">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    {{ __('ตรวจสอบการชำระเงิน') }}
                </x-admin-nav-link>

                {{-- =================================== --}}
                {{-- 3. MASTER DATA --}}
                {{-- =================================== --}}
                <div class="px-4 pt-6 pb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Master Data</div>
                
                <x-admin-nav-link :href="route('admin.category-settings')" :active="request()->routeIs('admin.category-settings')">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    {{ __('ตั้งค่าหมวดหมู่') }}
                </x-admin-nav-link>

                <x-admin-nav-link :href="route('admin.robot-models.index')" :active="request()->routeIs('admin.robot-models.index')">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    {{ __('คลังแม่แบบหุ่นยนต์') }}
                </x-admin-nav-link>

                {{-- =================================== --}}
                {{-- 4. SYSTEM --}}
                {{-- =================================== --}}
                <div class="px-4 pt-6 pb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">System</div>
                
                <x-admin-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13.732 4c-.76-1.01-1.93-1.42-3.232-1.42s-2.472.41-3.232 1.42"/></svg>
                    {{ __('จัดการผู้ใช้งาน') }}
                </x-admin-nav-link>
            </nav>
        </aside>

        {{-- 3. Main Content Area --}}
        <div class="flex-1 flex flex-col overflow-hidden">
            <header
                class="flex items-center justify-between px-4 lg:px-8 py-4 bg-white dark:bg-[#0f0f0f] border-b border-gray-100 dark:border-white/5">

                <div class="flex items-center">
                    {{-- Hamburger Button (แสดงเฉพาะมือถือ) --}}
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
                        <span class="text-sm font-bold dark:text-white">{{ Auth::user()->name }}</span>
                        <span class="text-[12px] text-gray-400 uppercase tracking-tighter mt-1">Administrator</span>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="group flex items-center text-gray-400 hover:text-red-500 transition-colors duration-300">
                            <span
                                class="hidden xs:inline text-xs font-bold uppercase tracking-widest mr-2">Logout</span>
                            <div
                                class="p-2 rounded-lg bg-gray-50 dark:bg-white/5 group-hover:bg-red-50 dark:group-hover:bg-red-500/10 transition-colors duration-300">
                                <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform duration-300"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
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
</body>

</html>
