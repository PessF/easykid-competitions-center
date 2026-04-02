<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Easykids Competitions' }}</title>

    <link href="https://fonts.bunny.net/css?family=kanit:300,400,500,600,700,800,900&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" href="{{ asset('images/favicon.png?v=' . time()) }}" type="image/png">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        /* ซ่อน Scrollbar สำหรับ Chrome, Safari และ Opera */
        .no-scrollbar::-webkit-scrollbar,
        .custom-scrollbar::-webkit-scrollbar,
        body::-webkit-scrollbar,
        aside::-webkit-scrollbar,
        main::-webkit-scrollbar {
            display: none;
        }

        /* ซ่อน Scrollbar สำหรับ IE, Edge และ Firefox */
        .no-scrollbar,
        .custom-scrollbar,
        body,
        aside,
        main {
            -ms-overflow-style: none;
            /* IE and Edge */
            scrollbar-width: none;
            /* Firefox */
        }
    </style>
</head>

<body
    class="font-sans antialiased bg-[#f8fafc] dark:bg-[#0a0a0a] text-gray-900 leading-relaxed transition-colors duration-300"
    x-data="{ mobileSidebarOpen: false }">

    <div class="flex h-screen overflow-hidden">
        {{-- 1. Backdrop สำหรับมือถือ --}}
        <div x-show="mobileSidebarOpen" @click="mobileSidebarOpen = false"
            class="fixed inset-0 z-40 bg-black/50 lg:hidden transition-opacity" style="display: none;"></div>

        {{-- 2. Sidebar --}}
        <aside :class="mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-50 w-72 bg-white dark:bg-[#0f0f0f] border-r border-gray-100 dark:border-white/5 transition-transform duration-300 transform lg:static lg:translate-x-0 overflow-y-auto shadow-2xl lg:shadow-none">

            <div class="flex flex-col h-full">
                <div class="px-8 py-8 border-b border-gray-100 dark:border-white/5 flex justify-center items-center">
                    <img src="{{ asset('images/logo.png') }}" alt="Easykids Robotics Logo"
                        class="w-auto h-12 object-contain">
                </div>

                <nav class="flex-1 px-4 pt-6 pb-4 space-y-2">
                    <div class="px-4 py-2 text-[10px] font-semibold text-gray-400 uppercase tracking-[0.2em]">เมนูหลัก
                    </div>

                    @php $isDashboard = request()->routeIs('user.dashboard'); @endphp
                    <a href="{{ route('user.dashboard') }}"
                        class="flex items-center px-4 py-3 text-sm rounded-xl transition-all duration-300 
                              {{ $isDashboard ? 'text-blue-600 bg-blue-50 dark:bg-blue-500/10 dark:text-blue-400 font-semibold' : 'font-normal text-gray-500 hover:text-gray-900 hover:bg-gray-50 dark:text-gray-400 dark:hover:text-white dark:hover:bg-white/5' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        ค้นหางานแข่งขัน
                    </a>

                    @php $isTeams = request()->routeIs('user.teams.*'); @endphp
                    <a href="{{ route('user.teams.index') }}"
                        class="flex items-center px-4 py-3 text-sm rounded-xl transition-all duration-300 
                              {{ $isTeams ? 'text-blue-600 bg-blue-50 dark:bg-blue-500/10 dark:text-blue-400 font-semibold' : 'font-normal text-gray-500 hover:text-gray-900 hover:bg-gray-50 dark:text-gray-400 dark:hover:text-white dark:hover:bg-white/5' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13.732 4c-.76-1.01-1.93-1.42-3.232-1.42s-2.472.41-3.232 1.42" />
                        </svg>
                        จัดการทีมของฉัน
                    </a>

                    @php
                        $isRegistrations = request()->routeIs('user.registrations');
                        $pendingPaymentCount = \App\Models\Registration::where('user_id', auth()->id())
                            ->where('status', 'pending_payment')
                            ->count();
                    @endphp
                    <a href="{{ route('user.registrations') }}"
                        class="flex items-center justify-between px-4 py-3 text-sm rounded-xl transition-all duration-300 
                              {{ $isRegistrations ? 'text-blue-600 bg-blue-50 dark:bg-blue-500/10 dark:text-blue-400 font-semibold' : 'font-normal text-gray-500 hover:text-gray-900 hover:bg-gray-50 dark:text-gray-400 dark:hover:text-white dark:hover:bg-white/5' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            ประวัติการสมัคร
                        </div>
                        @if ($pendingPaymentCount > 0)
                            <div
                                class="flex items-center justify-center min-w-[20px] h-5 px-1.5 text-[10px] font-bold text-white bg-red-500 rounded-full shadow-sm">
                                {{ $pendingPaymentCount }}
                            </div>
                        @endif
                    </a>
                </nav>
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header
                class="flex items-center justify-between px-6 lg:px-10 py-4 bg-white/80 dark:bg-[#0f0f0f]/80 backdrop-blur-md border-b border-gray-100 dark:border-white/5 sticky top-0 z-30">
                <button @click="mobileSidebarOpen = true"
                    class="lg:hidden text-gray-500 hover:text-gray-700 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <div class="flex items-center space-x-4 ml-auto">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center space-x-3 focus:outline-none group">
                                <div class="text-right hidden sm:block">
                                    <p
                                        class="text-sm font-semibold dark:text-white leading-none group-hover:text-blue-600 transition-colors">
                                        {{ Auth::user()->name }}
                                    </p>
                                    <p class="text-[10px] text-blue-500 font-semibold uppercase tracking-wider mt-1">
                                        ผู้เข้าแข่งขัน
                                    </p>
                                </div>
                                <div
                                    class="w-10 h-10 rounded-md bg-gray-200 overflow-hidden ring-2 ring-blue-500/10 group-hover:ring-blue-500/30 transition-all shadow-sm">
                                    {{-- 🚀 FIX: เปลี่ยนการเรียกรูปภาพมาเป็น Local Public Storage --}}
                                    @if (Auth::user()->avatar)
                                        @if (str_starts_with(Auth::user()->avatar, 'http'))
                                            <img src="{{ Auth::user()->avatar }}" class="w-full h-full object-cover" alt="{{ Auth::user()->name }}">
                                        @else
                                            <img src="{{ asset('storage/' . Auth::user()->avatar) }}" class="w-full h-full object-cover" alt="{{ Auth::user()->name }}">
                                        @endif
                                    @else
                                        <img src="{{ asset('images/default-avatar.png') }}" class="w-full h-full object-cover" alt="{{ Auth::user()->name }}">
                                    @endif
                                </div>
                                <svg class="fill-current h-4 w-4 text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300 transition-colors"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <button @click.prevent="$dispatch('open-modal', 'profile-edit-modal')"
                                class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition duration-150 ease-in-out font-normal">
                                โปรไฟล์ของฉัน
                            </button>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();"
                                    class="text-red-600 font-normal hover:bg-red-50">
                                    {{ __('ออกจากระบบ') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </header>

            <main class="flex-1 flex flex-col overflow-x-hidden overflow-y-auto relative">
                <div class="p-6 lg:p-8 flex-1">
                    {{ $slot }}
                </div>

                <footer class="mt-auto py-5 px-6 lg:px-8 border-t border-gray-100 dark:border-white/5 bg-transparent">
                    <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                        <p class="text-xs font-normal text-gray-500 dark:text-gray-400">&copy; {{ date('Y') }}
                            Easykids Robotics. All rights reserved.</p>
                        <div
                            class="flex items-center space-x-6 text-xs font-semibold text-gray-500 dark:text-gray-400">
                            <a href="{{ route('privacy.policy') }}"
                                class="hover:text-blue-600 transition-colors">Privacy Policy</a>
                            <a href="{{ route('terms.service') }}"
                                class="hover:text-blue-600 transition-colors">Terms
                                of Service</a>
                            <a href="https://www.easykidsrobotics.com/contact-us/"
                                class="hover:text-blue-600 transition-colors">ติดต่อเรา</a>
                        </div>
                    </div>
                </footer>
            </main>
        </div>
    </div>

    {{-- Profile Edit Modal --}}
    <x-modal name="profile-edit-modal" focusable>
        <div class="bg-white dark:bg-[#121212] flex flex-col max-h-[90vh] rounded-2xl overflow-hidden shadow-2xl">

            {{-- Sticky Header --}}
            <div
                class="flex items-center justify-between p-5 sm:p-6 md:p-8 pb-4 border-b border-gray-100 dark:border-white/5 shrink-0 z-10 bg-white dark:bg-[#121212]">
                <div class="flex items-center gap-3 md:gap-4">
                    <div
                        class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-50 dark:bg-blue-900/30 rounded-xl flex items-center justify-center text-blue-600 dark:text-blue-400 shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white leading-tight">
                            จัดการโปรไฟล์</h2>
                        <p class="text-[10px] sm:text-xs text-gray-500 dark:text-gray-400 mt-0.5 font-normal">
                            อัปเดตข้อมูลส่วนตัวสำหรับการแข่งขัน</p>
                    </div>
                </div>
                <button @click="$dispatch('close-modal', 'profile-edit-modal')"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors p-2 rounded-xl hover:bg-gray-50 dark:hover:bg-white/5 focus:outline-none focus:ring-2 focus:ring-gray-200">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Form Wrapper --}}
            <form id="profile-update-form" method="post" action="{{ route('profile.update') }}"
                class="flex flex-col min-h-0 overflow-hidden">
                @csrf
                @method('patch')

                {{-- Scrollable Body --}}
                <div class="p-5 sm:p-6 md:p-8 overflow-y-auto custom-scrollbar space-y-6 sm:space-y-8">

                    {{-- 0. ชื่อที่แสดงในระบบ --}}
                    <div class="space-y-1.5">
                        <label
                            class="text-xs sm:text-sm font-semibold text-gray-700 dark:text-gray-300">ชื่อที่แสดงในระบบ
                            (Display Name) <span class="text-red-500">*</span></label>
                        <input name="name" type="text" value="{{ old('name', Auth::user()->name) }}" required
                            class="w-full px-4 py-2.5 sm:py-3 bg-white dark:bg-black border {{ $errors->has('name') ? 'border-red-500' : 'border-gray-200 dark:border-white/10' }} rounded-xl focus:ring-2 focus:ring-blue-500/50 outline-none transition-all text-sm dark:text-white shadow-sm">
                        @error('name')
                            <span class="text-[10px] font-semibold text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- 1. ข้อมูลภาษาไทย --}}
                    <div
                        class="p-4 sm:p-5 bg-gray-50 dark:bg-white/5 rounded-2xl border border-gray-100 dark:border-white/5">
                        <h3
                            class="text-[10px] sm:text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-widest mb-3 sm:mb-4 flex items-center gap-2">
                            <span class="w-1.5 h-1.5 bg-blue-600 rounded-sm"></span> ข้อมูลภาษาไทย
                        </h3>

                        <div class="grid grid-cols-12 gap-3 sm:gap-4">
                            {{-- Prefix TH --}}
                            <div class="col-span-5 sm:col-span-3 md:col-span-3 lg:col-span-3 space-y-1.5">
                                <label class="text-[10px] sm:text-xs font-semibold text-gray-500">คำนำหน้า</label>
                                <div x-data="{ open: false, selected: '{{ old('prefix_th', Auth::user()->prefix_th) }}', placeholder: 'เลือก' }" class="relative" @click.away="open = false">
                                    <input type="hidden" name="prefix_th" x-model="selected">
                                    <button @click="open = !open" type="button"
                                        class="w-full px-3 sm:px-4 py-2.5 bg-white dark:bg-black border {{ $errors->has('prefix_th') ? 'border-red-500' : 'border-gray-200 dark:border-white/10' }} rounded-xl focus:ring-2 focus:ring-blue-500/50 outline-none transition-all text-xs sm:text-sm dark:text-white flex justify-between items-center text-left">
                                        <span x-text="selected || placeholder"
                                            :class="{ 'text-gray-400 dark:text-gray-500': !selected }"
                                            class="truncate mr-2"></span>
                                        <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 shrink-0"
                                            :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <div x-show="open" x-transition.opacity.duration.200ms
                                        class="absolute z-50 min-w-[160px] w-full mt-1 bg-white dark:bg-[#1a1a1a] border border-gray-100 dark:border-white/10 rounded-xl shadow-lg py-1.5 max-h-56 overflow-y-auto custom-scrollbar"
                                        style="display: none;">
                                        <template x-for="item in ['เด็กชาย', 'เด็กหญิง', 'นาย', 'นางสาว', 'นาง']">
                                            <div @click="selected = item; open = false"
                                                class="px-4 py-3 text-xs sm:text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5 cursor-pointer transition-colors"
                                                :class="{
                                                    'bg-blue-50/80 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 font-semibold': selected ===
                                                        item
                                                }">
                                                <span x-text="item"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            {{-- ชื่อจริง --}}
                            <div class="col-span-7 sm:col-span-4 md:col-span-5 lg:col-span-4 space-y-1.5">
                                <label class="text-[10px] sm:text-xs font-semibold text-gray-500">ชื่อจริง</label>
                                <input name="first_name_th" type="text"
                                    value="{{ old('first_name_th', Auth::user()->first_name_th) }}"
                                    class="w-full px-3 sm:px-4 py-2.5 bg-white dark:bg-black border {{ $errors->has('first_name_th') ? 'border-red-500' : 'border-gray-200 dark:border-white/10' }} rounded-xl focus:ring-2 focus:ring-blue-500/50 outline-none transition-all text-xs sm:text-sm dark:text-white">
                            </div>

                            {{-- นามสกุล --}}
                            <div class="col-span-12 sm:col-span-5 md:col-span-4 lg:col-span-5 space-y-1.5">
                                <label class="text-[10px] sm:text-xs font-semibold text-gray-500">นามสกุล</label>
                                <input name="last_name_th" type="text"
                                    value="{{ old('last_name_th', Auth::user()->last_name_th) }}"
                                    class="w-full px-3 sm:px-4 py-2.5 bg-white dark:bg-black border {{ $errors->has('last_name_th') ? 'border-red-500' : 'border-gray-200 dark:border-white/10' }} rounded-xl focus:ring-2 focus:ring-blue-500/50 outline-none transition-all text-xs sm:text-sm dark:text-white">
                            </div>
                        </div>
                    </div>

                    {{-- 2. ข้อมูลภาษาอังกฤษ --}}
                    <div
                        class="p-4 sm:p-5 bg-gray-50 dark:bg-white/5 rounded-2xl border border-gray-100 dark:border-white/5">
                        <h3
                            class="text-[10px] sm:text-xs font-semibold text-indigo-600 dark:text-indigo-400 uppercase tracking-widest mb-3 sm:mb-4 flex items-center gap-2">
                            <span class="w-1.5 h-1.5 bg-indigo-600 rounded-sm"></span> ข้อมูลภาษาอังกฤษ
                        </h3>
                        <div class="grid grid-cols-12 gap-3 sm:gap-4">

                            {{-- Prefix EN --}}
                            <div class="col-span-5 sm:col-span-3 md:col-span-3 lg:col-span-3 space-y-1.5">
                                <label class="text-[10px] sm:text-xs font-semibold text-gray-500">Prefix</label>
                                <div x-data="{ open: false, selected: '{{ old('prefix_en', Auth::user()->prefix_en) }}', placeholder: 'Select' }" class="relative" @click.away="open = false">
                                    <input type="hidden" name="prefix_en" x-model="selected">
                                    <button @click="open = !open" type="button"
                                        class="w-full px-3 sm:px-4 py-2.5 bg-white dark:bg-black border {{ $errors->has('prefix_en') ? 'border-red-500' : 'border-gray-200 dark:border-white/10' }} rounded-xl focus:ring-2 focus:ring-blue-500/50 outline-none transition-all text-xs sm:text-sm dark:text-white flex justify-between items-center text-left">
                                        <span x-text="selected || placeholder"
                                            :class="{ 'text-gray-400 dark:text-gray-500': !selected }"
                                            class="truncate mr-2"></span>
                                        <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 shrink-0"
                                            :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <div x-show="open" x-transition.opacity.duration.200ms
                                        class="absolute z-50 min-w-[160px] w-full mt-1 bg-white dark:bg-[#1a1a1a] border border-gray-100 dark:border-white/10 rounded-xl shadow-lg py-1.5 max-h-56 overflow-y-auto custom-scrollbar"
                                        style="display: none;">
                                        <template x-for="item in ['Master', 'Miss', 'Mr.', 'Ms.', 'Mrs.']">
                                            <div @click="selected = item; open = false"
                                                class="px-4 py-3 text-xs sm:text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5 cursor-pointer transition-colors"
                                                :class="{
                                                    'bg-blue-50/80 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 font-semibold': selected ===
                                                        item
                                                }">
                                                <span x-text="item"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            {{-- First Name --}}
                            <div class="col-span-7 sm:col-span-4 md:col-span-5 lg:col-span-4 space-y-1.5">
                                <label class="text-[10px] sm:text-xs font-semibold text-gray-500">First Name</label>
                                <input name="first_name_en" type="text"
                                    value="{{ old('first_name_en', Auth::user()->first_name_en) }}"
                                    class="w-full px-3 sm:px-4 py-2.5 bg-white dark:bg-black border {{ $errors->has('first_name_en') ? 'border-red-500' : 'border-gray-200 dark:border-white/10' }} rounded-xl focus:ring-2 focus:ring-blue-500/50 outline-none transition-all text-xs sm:text-sm dark:text-white">
                            </div>

                            {{-- Last Name --}}
                            <div class="col-span-12 sm:col-span-5 md:col-span-4 lg:col-span-5 space-y-1.5">
                                <label class="text-[10px] sm:text-xs font-semibold text-gray-500">Last Name</label>
                                <input name="last_name_en" type="text"
                                    value="{{ old('last_name_en', Auth::user()->last_name_en) }}"
                                    class="w-full px-3 sm:px-4 py-2.5 bg-white dark:bg-black border {{ $errors->has('last_name_en') ? 'border-red-500' : 'border-gray-200 dark:border-white/10' }} rounded-xl focus:ring-2 focus:ring-blue-500/50 outline-none transition-all text-xs sm:text-sm dark:text-white">
                            </div>
                        </div>
                    </div>

                    {{-- 3. ข้อมูลเบ็ดเตล็ด --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 sm:gap-5 pt-2">
                        <div class="space-y-1.5">
                            <label
                                class="text-[10px] sm:text-xs font-semibold text-gray-700 dark:text-gray-300">วัน/เดือน/ปี
                                เกิด (ค.ศ.)</label>
                            <input name="birthday" type="date"
                                value="{{ old('birthday', Auth::user()->birthday ? \Carbon\Carbon::parse(Auth::user()->birthday)->format('Y-m-d') : '') }}"
                                class="w-full px-4 py-2.5 bg-white dark:bg-black border {{ $errors->has('birthday') ? 'border-red-500' : 'border-gray-200 dark:border-white/10' }} rounded-xl focus:ring-2 focus:ring-blue-500/50 outline-none transition-all text-sm dark:text-white cursor-pointer [color-scheme:light] dark:[color-scheme:dark]">
                        </div>
                        <div class="space-y-1.5">
                            <label
                                class="text-[10px] sm:text-xs font-semibold text-gray-700 dark:text-gray-300">เบอร์โทรศัพท์</label>
                            <input name="phone_number" type="text"
                                value="{{ old('phone_number', Auth::user()->phone_number) }}"
                                placeholder="08XXXXXXXX"
                                class="w-full px-4 py-2.5 bg-white dark:bg-black border {{ $errors->has('phone_number') ? 'border-red-500' : 'border-gray-200 dark:border-white/10' }} rounded-xl focus:ring-2 focus:ring-blue-500/50 outline-none transition-all text-sm dark:text-white">
                        </div>

                        {{-- Shirt Size --}}
                        <div class="sm:col-span-2 md:col-span-1 space-y-1.5">
                            <label
                                class="text-[10px] sm:text-xs font-semibold text-gray-700 dark:text-gray-300">ไซส์เสื้อ
                                (Shirt Size)</label>
                            <div x-data="{ open: false, selected: '{{ old('shirt_size', Auth::user()->shirt_size) }}', placeholder: 'เลือกไซส์' }" class="relative" @click.away="open = false">
                                <input type="hidden" name="shirt_size" x-model="selected">
                                <button @click="open = !open" type="button"
                                    class="w-full px-4 py-2.5 bg-white dark:bg-black border {{ $errors->has('shirt_size') ? 'border-red-500' : 'border-gray-200 dark:border-white/10' }} rounded-xl focus:ring-2 focus:ring-blue-500/50 outline-none transition-all text-sm dark:text-white flex justify-between items-center text-left">
                                    <span x-text="selected || placeholder"
                                        :class="{ 'text-gray-400 dark:text-gray-500': !selected }"></span>
                                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-200"
                                        :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div x-show="open" x-transition.opacity.duration.200ms
                                    class="absolute bottom-full mb-1 z-50 min-w-[160px] w-full bg-white dark:bg-[#1a1a1a] border border-gray-100 dark:border-white/10 rounded-xl shadow-lg py-1.5 max-h-56 overflow-y-auto custom-scrollbar"
                                    style="display: none;">
                                    <template x-for="item in ['S', 'M', 'L', 'XL', '2XL', '3XL']">
                                        <div @click="selected = item; open = false"
                                            class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5 cursor-pointer transition-colors"
                                            :class="{
                                                'bg-blue-50/80 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 font-semibold': selected ===
                                                    item
                                            }">
                                            <span x-text="item"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 4. ส่วนที่แก้ไขไม่ได้ (Email) --}}
                    <div
                        class="p-3 sm:p-4 bg-gray-50 dark:bg-white/5 rounded-xl border border-dashed border-gray-200 dark:border-white/10 mt-4">
                        <label
                            class="text-[10px] sm:text-xs font-semibold text-gray-400 flex items-center gap-1.5 mb-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                    clip-rule="evenodd" />
                            </svg>
                            อีเมล (แก้ไขไม่ได้)
                        </label>
                        <div class="text-xs sm:text-sm font-normal text-gray-400 italic px-1">
                            {{ Auth::user()->email }}</div>
                    </div>

                    {{-- Hidden Input --}}
                    <input type="hidden" name="email" value="{{ Auth::user()->email }}">

                </div>

                {{-- Sticky Footer Buttons --}}
                <div
                    class="p-5 sm:p-6 md:p-8 pt-4 border-t border-gray-100 dark:border-white/5 bg-white dark:bg-[#121212] shrink-0 z-10">
                    <div class="flex flex-col sm:flex-row items-center gap-3">
                        <button type="submit"
                            class="w-full sm:w-auto flex-1 px-6 py-3.5 sm:py-3 bg-gray-900 dark:bg-white dark:text-black text-white text-sm font-semibold rounded-xl hover:bg-blue-600 dark:hover:bg-blue-500 transition-all shadow-sm order-1 sm:order-none focus:ring-2 focus:ring-blue-500/50 outline-none">
                            บันทึกการเปลี่ยนแปลง
                        </button>
                        <button type="button" @click="$dispatch('close-modal', 'profile-edit-modal')"
                            class="w-full sm:w-auto px-6 py-3.5 sm:py-3 bg-gray-100 dark:bg-white/5 text-gray-700 dark:text-gray-300 text-sm font-semibold rounded-xl hover:bg-gray-200 dark:hover:bg-white/10 transition-all order-2 sm:order-none">
                            ยกเลิก
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </x-modal>

    {{-- 🚀 FIX: เติม addslashes() ป้องกัน JS ช็อก --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ToastConfig = {
                background: '#ffffff',
                confirmButtonColor: '#2563eb',
                customClass: {
                    popup: 'rounded-2xl p-4 shadow-sm border border-gray-100',
                    title: 'text-xl font-bold text-gray-800',
                    htmlContainer: 'text-left',
                    confirmButton: 'rounded-lg px-8 py-2.5 font-bold text-sm transition-all hover:brightness-110'
                },
                buttonsStyling: true
            };

            // Success 
            @if (session('success') || session('status') === 'profile-updated')
                Swal.fire({
                    ...ToastConfig,
                    icon: 'success',
                    iconColor: '#10b981',
                    title: 'สำเร็จ',
                    html: `{!! addslashes(session('success') ?? 'บันทึกข้อมูลเรียบร้อยแล้ว') !!}`,
                    confirmButtonText: 'ตกลง',
                    confirmButtonColor: '#10b981',
                });
            @endif

            // Error 
            @if (session('error'))
                Swal.fire({
                    ...ToastConfig,
                    icon: 'error',
                    iconColor: '#ef4444',
                    title: 'เกิดข้อผิดพลาด',
                    html: `{!! addslashes(session('error')) !!}`,
                    confirmButtonText: 'ปิด',
                    confirmButtonColor: '#ef4444',
                });
            @endif

            // Validation Errors
            @if ($errors->any())
                let errorHtml = `
            <div class="mt-4 space-y-3">
                @foreach ($errors->all() as $error)
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border-l-4 border-amber-500">
                        <i class="fas fa-info-circle text-amber-500 text-xs"></i>
                        <span class="text-xs text-gray-700 font-medium leading-tight">{!! addslashes($error) !!}</span>
                    </div>
                @endforeach
            </div>
        `;

                Swal.fire({
                    ...ToastConfig,
                    icon: 'warning',
                    iconColor: '#f59e0b',
                    title: 'พบข้อผิดพลาด',
                    html: `
                <p class="text-gray-400 text-xs ml-1">กรุณาตรวจสอบข้อมูลตามรายการด้านล่าง:</p>
                ${errorHtml}
            `,
                    confirmButtonText: 'รับทราบ',
                    confirmButtonColor: '#f59e0b',
                    width: '28rem'
                });
            @endif
        });
    </script>
</body>

</html>