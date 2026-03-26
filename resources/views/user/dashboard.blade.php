<x-user-layout>
    <x-slot name="title">ค้นหางานแข่งขัน | Easykids</x-slot>

    <div x-data="{ 
        search: '', 
        filterStatus: 'all',
        items: {{ Js::from($competitions) }} 
    }" class="space-y-8 pb-12">
        
        {{-- 🎯 1. Hero Video Banner --}}
        <div class="relative overflow-hidden rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 h-[18rem] md:h-[22rem] flex flex-col justify-end">
            {{-- Background Video --}}
            <video autoplay loop muted playsinline class="absolute inset-0 w-full h-full object-cover z-0">
                <source src="{{ asset('videos/header_bg.mp4') }}" type="video/mp4">
            </video>
            
            {{-- Gradient Overlay --}}
            <div class="absolute inset-0 bg-gradient-to-t from-gray-900/90 via-gray-900/40 to-transparent z-10"></div>
            
            {{-- เนื้อหาข้อความ --}}
            <div class="relative z-20 p-8 md:px-10 md:py-10 text-white w-full">
                <div class="max-w-3xl">
                    <h1 class="text-3xl md:text-5xl font-bold mb-3 tracking-tight flex items-center gap-3 drop-shadow-md">
                        พร้อมที่จะท้าทายหรือยัง?
                        <svg class="w-8 h-8 md:w-10 md:h-10 text-blue-400 drop-shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </h1>
                    <p class="text-gray-200 text-sm md:text-lg font-normal drop-shadow-sm max-w-2xl">ค้นหางานแข่งขันที่สนใจ สร้างทีมของคุณ แล้วพาน้องๆ ไปสู่ชัยชนะได้เลย</p>
                </div>
            </div>
        </div>

        {{-- 🔍 2. Search & Filter Bar (🛠️ FIXED BUG) --}}
        <div class="bg-white dark:bg-[#121212] p-2.5 rounded-xl border border-gray-200 dark:border-white/10 shadow-sm flex flex-col md:flex-row gap-2 items-center sticky top-20 z-20 backdrop-blur-xl bg-opacity-90 dark:bg-opacity-90">
            
            {{-- Input Search --}}
            <div class="flex-1 relative w-full group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within:text-blue-500">
                    <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" x-model="search" placeholder="ค้นหาชื่อการแข่งขัน, สถานที่..." 
                    class="block w-full pl-11 pr-4 py-3 bg-transparent border-none focus:ring-0 font-normal text-gray-700 dark:text-gray-200 placeholder-gray-400 transition-all outline-none">
            </div>
            
            {{-- เส้นแบ่ง (Divider) ซ่อนในมือถือ ปรากฏเฉพาะจอใหญ่ --}}
            <div class="hidden md:block w-px h-8 bg-gray-200 dark:bg-white/10 mx-2"></div>
            
            {{-- Select Filter Wrapper --}}
            <div class="w-full md:w-[220px] flex-shrink-0 relative">
                {{-- เพิ่ม bg-none เพื่อทับ default arrow ของ Tailwind Forms และใช้ dark:bg แบบสีทึบ --}}
                <select x-model="filterStatus" class="w-full bg-gray-50 dark:bg-[#1a1a1a] border border-gray-200 dark:border-white/10 rounded-lg pl-4 pr-10 py-3 focus:ring-2 focus:ring-blue-500/50 font-semibold text-sm text-gray-700 dark:text-gray-300 cursor-pointer transition-colors hover:bg-gray-100 dark:hover:bg-[#222] appearance-none bg-none outline-none m-0">
                    <option value="all">สถานะทั้งหมด</option>
                    <option value="registration">เปิดรับสมัคร</option>
                    <option value="ongoing">กำลังแข่งขัน</option>
                </select>
                {{-- Custom Dropdown Arrow --}}
                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>
        </div>

        {{-- 🏆 3. รายการการ์ดแบบแนวนอน --}}
        <div class="grid grid-cols-1 gap-6">
            @foreach($competitions as $comp)
                <div x-show="(search === '' || '{{ strtolower($comp->name) }}'.includes(search.toLowerCase())) && (filterStatus === 'all' || '{{ $comp->status }}' === filterStatus)"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform translate-y-4"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    class="group bg-white dark:bg-[#121212] rounded-2xl border border-gray-200 dark:border-white/10 overflow-hidden hover:border-blue-400/40 hover:shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:hover:shadow-[0_8px_30px_rgba(59,130,246,0.05)] transition-all duration-300 flex flex-col md:flex-row">
                    
                    {{-- ฝั่งรูปภาพ --}}
                    <div class="md:w-[320px] lg:w-[360px] h-60 md:h-auto relative overflow-hidden shrink-0 bg-gray-100 dark:bg-gray-800">
                        <img src="{{ route('admin.competitions.banner', $comp->id) }}" 
                             alt="{{ $comp->name }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-in-out">
                        
                        {{-- Badge วันที่ --}}
                        <div class="absolute top-4 left-4 bg-white/95 backdrop-blur-md dark:bg-gray-900/90 px-4 py-2.5 rounded-xl text-center shadow-sm border border-black/5 dark:border-white/10 z-10 flex flex-col items-center justify-center min-w-[3.5rem]">
                            <span class="block text-2xl font-bold leading-none text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($comp->event_start_date)->format('d') }}</span>
                            <span class="text-[10px] font-bold uppercase text-blue-600 tracking-widest mt-0.5">{{ \Carbon\Carbon::parse($comp->event_start_date)->translatedFormat('M') }}</span>
                        </div>
                    </div>

                    {{-- ฝั่งเนื้อหา --}}
                    <div class="flex-1 p-6 md:p-8 flex flex-col justify-between">
                        <div>
                            <div class="flex flex-wrap items-center gap-2 mb-4">
                                @if($comp->status === 'registration')
                                    <span class="px-2.5 py-1 bg-green-50 dark:bg-green-500/10 text-green-600 dark:text-green-400 text-[10px] font-semibold uppercase rounded-md tracking-wider flex items-center border border-green-200 dark:border-green-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5 animate-pulse"></span> Open Now
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 text-[10px] font-semibold uppercase rounded-md tracking-wider flex items-center border border-blue-200 dark:border-blue-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-1.5"></span> {{ $comp->status }}
                                    </span>
                                @endif
                                <span class="text-xs text-gray-500 dark:text-gray-400 font-medium flex items-center px-2 py-1">
                                    <svg class="w-3.5 h-3.5 mr-1.5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    {{ $comp->location }}
                                </span>
                            </div>
                            
                            <h3 class="text-2xl font-semibold mb-2 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors tracking-tight line-clamp-1">{{ $comp->name }}</h3>
                            <p class="text-gray-500 dark:text-gray-400 text-sm line-clamp-2 leading-relaxed font-normal">{{ $comp->description }}</p>
                        </div>

                        {{-- ส่วนท้ายการ์ด --}}
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mt-6 pt-5 border-t border-gray-100 dark:border-white/5">
                            <div class="flex items-center space-x-3">
                                <div class="flex -space-x-2.5">
                                    <div class="w-8 h-8 rounded-lg border-2 border-white dark:border-[#121212] bg-indigo-50 dark:bg-indigo-900/50 flex items-center justify-center text-[10px] font-semibold text-indigo-600 dark:text-indigo-300 z-20">A</div>
                                    <div class="w-8 h-8 rounded-lg border-2 border-white dark:border-[#121212] bg-blue-50 dark:bg-blue-900/50 flex items-center justify-center text-[10px] font-semibold text-blue-600 dark:text-blue-300 z-10">B</div>
                                    <div class="w-8 h-8 rounded-lg border-2 border-white dark:border-[#121212] bg-gray-50 dark:bg-gray-800/50 flex items-center justify-center text-[10px] font-semibold text-gray-600 dark:text-gray-400 z-0">+</div>
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">เปิดรับสมัครแล้ว</span>
                            </div>

                            <a href="{{ route('competitions.show', $comp->id) }}" 
                               class="w-full sm:w-auto px-6 py-2.5 bg-gray-900 dark:bg-white dark:text-black text-white text-sm font-semibold rounded-lg hover:bg-blue-600 dark:hover:bg-blue-500 transition-colors flex items-center justify-center gap-2 group/btn">
                                ดูรายละเอียด & สมัคร
                                <svg class="w-4 h-4 transform group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- 📭 4. Empty State --}}
        <div x-show="items.filter(i => i.name.toLowerCase().includes(search.toLowerCase()) && (filterStatus === 'all' || i.status === filterStatus)).length === 0" 
             style="display: none;"
             class="py-24 text-center bg-white dark:bg-[#121212] rounded-2xl border border-gray-200 dark:border-white/10 mt-6 shadow-sm">
             <div class="w-16 h-16 bg-gray-50 dark:bg-white/5 rounded-full flex items-center justify-center mx-auto mb-5">
                 <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
             </div>
             <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 tracking-tight">ไม่พบการแข่งขันที่ตรงกับเงื่อนไข</h3>
             <p class="text-gray-500 dark:text-gray-400 text-sm font-normal">ลองเปลี่ยนคำค้นหา หรือเลือกสถานะอื่นดูนะครับ</p>
             <button @click="search = ''; filterStatus = 'all'" class="mt-5 px-4 py-2 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/50 rounded-lg font-semibold text-sm transition-colors focus:outline-none">ล้างการค้นหา</button>
        </div>
    </div>
</x-user-layout>