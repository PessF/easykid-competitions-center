<x-user-layout>
    <x-slot name="title">ค้นหางานแข่งขัน | Easykids</x-slot>

    <div x-data="{
        search: '',
        filterStatus: 'all',
        items: {{ Js::from($competitions) }}
    }" class="space-y-8 pb-12">

        {{--  Hero Video Banner --}}
        <div
            class="relative w-full rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden bg-[#0a0a0a] flex flex-col justify-end min-h-[18rem] sm:min-h-[22rem] md:aspect-[21/9] md:max-h-[26rem]">

            {{-- Background Video --}}
            <video autoplay loop muted playsinline
                class="absolute inset-0 w-full h-full object-cover object-center z-0 pointer-events-none opacity-70"
                poster="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7">
                <source src="{{ asset('videos/ek-videohero.mp4') }}" type="video/mp4">
            </video>

            {{-- Gradient Overlay (ไล่สีดำจากข้างล่างขึ้นมา เพื่อให้ตัวหนังสือลอยเด่น ไม่จมไปกับวิดีโอ) --}}
            <div class="absolute inset-0 bg-gradient-to-t from-[#0a0a0a] via-[#0a0a0a]/50 to-[#0a0a0a]/10 z-10"></div>

            {{-- เนื้อหาข้อความ --}}
            <div class="relative z-20 p-6 sm:p-8 md:px-10 md:py-10 text-white w-full">
                <div class="max-w-3xl">
                    <h1
                        class="text-3xl sm:text-4xl md:text-5xl font-semibold mb-3 tracking-tight flex items-center gap-3 drop-shadow-lg">
                        พร้อมที่จะท้าทายหรือยัง?
                    </h1>
                    <p class="text-gray-200 text-sm sm:text-base md:text-lg font-normal drop-shadow-md max-w-2xl">
                        สร้างทีมของคุณ แล้วพาน้องๆ ไปสู่ชัยชนะได้เลย
                    </p>
                </div>
            </div>
        </div>

        {{--  Search & Filter Bar (Custom Dropdown) --}}
        <div
            class="bg-white/90 dark:bg-[#1a1a1a]/90 p-3 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm flex flex-col md:flex-row gap-3 items-center sticky top-20 z-40 backdrop-blur-xl transition-all">

            {{-- Input Search --}}
            <div class="flex-1 relative w-full group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i
                        class="fas fa-search text-gray-400 group-focus-within:text-blue-500 transition-colors text-sm"></i>
                </div>
                <input type="text" x-model="search" placeholder="ค้นหาชื่อการแข่งขัน, สถานที่..."
                    class="block w-full pl-11 pr-4 py-3 bg-transparent border-none focus:ring-0 font-normal text-gray-700 dark:text-gray-200 placeholder-gray-400 transition-all outline-none text-base">
            </div>

            {{-- เส้นแบ่ง (Divider) --}}
            <div class="hidden md:block w-px h-8 bg-gray-200 dark:bg-gray-700 mx-2"></div>

            {{-- Custom Alpine Dropdown สำหรับ Filter --}}
            <div x-data="{
                open: false,
                options: { 'all': 'สถานะทั้งหมด', 'registration': 'เปิดรับสมัคร', 'ongoing': 'กำลังแข่งขัน' }
            }" class="w-full md:w-[220px] flex-shrink-0 relative"
                @click.outside="open = false">

                <button @click="open = !open" type="button"
                    class="w-full bg-gray-50 dark:bg-[#111] border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500/30 font-semibold text-sm text-gray-700 dark:text-gray-300 cursor-pointer transition-colors hover:bg-gray-100 dark:hover:bg-gray-800 flex justify-between items-center outline-none">
                    <span x-text="options[filterStatus]"></span>
                    <i class="fas fa-chevron-down text-[10px] text-gray-400 transition-transform duration-200"
                        :class="open && 'rotate-180'"></i>
                </button>

                <div x-show="open" x-transition.opacity style="display:none"
                    class="absolute z-50 w-full mt-1 bg-white dark:bg-[#1a1a1a] shadow-lg rounded-xl border border-gray-100 dark:border-gray-800 overflow-hidden">
                    <template x-for="(label, val) in options" :key="val">
                        <div @click="filterStatus = val; open = false"
                            class="px-4 py-3 text-sm cursor-pointer transition-colors"
                            :class="filterStatus === val ?
                                'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 font-semibold' :
                                'hover:bg-gray-50 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-300 font-normal'"
                            x-text="label"></div>
                    </template>
                </div>
            </div>
        </div>

        {{-- รายการการ์ดการแข่งขัน --}}
        <div class="grid grid-cols-1 gap-6">
            @foreach ($competitions as $comp)
                <div x-show="(search === '' || '{{ strtolower($comp->name) }}'.includes(search.toLowerCase())) && (filterStatus === 'all' || '{{ $comp->status }}' === filterStatus)"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform translate-y-4"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    class="group bg-white dark:bg-[#161616] rounded-2xl border border-gray-200 dark:border-gray-800 overflow-hidden hover:border-blue-300 dark:hover:border-gray-600 hover:shadow-lg hover:shadow-blue-500/5 transition-all duration-300 flex flex-col md:flex-row">

                    {{-- ฝั่งรูปภาพแบนเนอร์ --}}
                    <div
                        class="md:w-[320px] lg:w-[360px] h-60 md:h-auto relative overflow-hidden shrink-0 bg-gray-100 dark:bg-[#111]">
                        <img src="{{ route('admin.competitions.banner', $comp->id) }}" alt="{{ $comp->name }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-in-out">

                        {{-- Badge วันที่ --}}
                        <div
                            class="absolute top-4 left-4 bg-white/90 backdrop-blur-md dark:bg-[#0a0a0a]/90 px-4 py-2.5 rounded-xl text-center shadow-sm border border-black/5 dark:border-white/5 z-10 flex flex-col items-center justify-center min-w-[3.5rem]">
                            <span
                                class="block text-2xl font-semibold leading-none text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($comp->event_start_date)->format('d') }}</span>
                            <span
                                class="text-[10px] font-semibold uppercase text-blue-600 dark:text-blue-400 tracking-widest mt-0.5">{{ \Carbon\Carbon::parse($comp->event_start_date)->translatedFormat('M') }}</span>
                        </div>
                    </div>

                    {{-- ฝั่งเนื้อหา --}}
                    <div class="flex-1 p-6 md:p-8 flex flex-col justify-between">
                        <div>
                            <div class="flex flex-wrap items-center gap-3 mb-4">
                                @if ($comp->status === 'registration')
                                    <span
                                        class="px-3 py-1.5 bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 text-xs font-semibold uppercase rounded-lg tracking-wide flex items-center border border-green-200 dark:border-green-800/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-2 animate-pulse"></span>
                                        Open Now
                                    </span>
                                @else
                                    <span
                                        class="px-3 py-1.5 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 text-xs font-semibold uppercase rounded-lg tracking-wide flex items-center border border-blue-200 dark:border-blue-800/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-2"></span>
                                        {{ $comp->status }}
                                    </span>
                                @endif
                                <span class="text-sm text-gray-500 dark:text-gray-400 font-medium flex items-center">
                                    <i class="fas fa-map-marker-alt mr-1.5 text-gray-400"></i>
                                    {{ $comp->location }}
                                </span>
                            </div>

                            <h3
                                class="text-2xl font-semibold mb-2 text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors tracking-tight line-clamp-1">
                                {{ $comp->name }}</h3>
                            <p
                                class="text-gray-500 dark:text-gray-400 text-base line-clamp-2 leading-relaxed font-normal">
                                {{ $comp->description }}</p>
                        </div>

                        {{-- ส่วนท้ายการ์ด --}}
                        <div
                            class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mt-6 pt-5 border-t border-gray-100 dark:border-gray-800">
                            <div class="flex items-center space-x-3">
                                {{-- Placeholder สำหรับรูปลูกทีม (Mockup) --}}
                                <div class="flex -space-x-2.5">
                                    <div
                                        class="w-9 h-9 rounded-xl border-2 border-white dark:border-[#161616] bg-indigo-50 dark:bg-indigo-900/40 flex items-center justify-center text-[10px] font-semibold text-indigo-600 dark:text-indigo-300 z-20">
                                        A</div>
                                    <div
                                        class="w-9 h-9 rounded-xl border-2 border-white dark:border-[#161616] bg-blue-50 dark:bg-blue-900/40 flex items-center justify-center text-[10px] font-semibold text-blue-600 dark:text-blue-300 z-10">
                                        B</div>
                                    <div
                                        class="w-9 h-9 rounded-xl border-2 border-white dark:border-[#161616] bg-gray-50 dark:bg-gray-800/50 flex items-center justify-center text-[10px] font-semibold text-gray-600 dark:text-gray-400 z-0">
                                        +</div>
                                </div>
                                <span
                                    class="text-sm text-gray-500 dark:text-gray-400 font-medium">เปิดรับสมัครแล้ว</span>
                            </div>

                            <a href="{{ route('admin.competitions.show', $comp->id) }}"
                                class="w-full sm:w-auto px-6 py-3 bg-gray-900 dark:bg-white dark:text-black text-white text-sm font-semibold rounded-xl hover:bg-blue-600 dark:hover:bg-blue-500 transition-colors flex items-center justify-center gap-2 group/btn shadow-sm">
                                ดูรายละเอียด & สมัคร
                                <i
                                    class="fas fa-arrow-right text-xs transform group-hover/btn:translate-x-1 transition-transform"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{--  Empty State --}}
        <div x-show="items.filter(i => i.name.toLowerCase().includes(search.toLowerCase()) && (filterStatus === 'all' || i.status === filterStatus)).length === 0"
            style="display: none;"
            class="py-24 text-center bg-white dark:bg-[#161616] rounded-2xl border border-gray-200 dark:border-gray-800 mt-6 shadow-sm">
            <div class="w-20 h-20 bg-gray-50 dark:bg-[#111] rounded-full flex items-center justify-center mx-auto mb-5">
                <i class="fas fa-search-minus text-3xl text-gray-300 dark:text-gray-600"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2 tracking-tight">
                ไม่พบการแข่งขันที่ตรงกับเงื่อนไข</h3>
            <p class="text-gray-500 dark:text-gray-400 text-base font-normal">ลองเปลี่ยนคำค้นหา
                หรือเลือกสถานะอื่นดูนะครับ</p>
            <button @click="search = ''; filterStatus = 'all'"
                class="mt-6 px-6 py-2.5 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/40 rounded-xl font-semibold text-sm transition-colors focus:outline-none">
                ล้างการค้นหา
            </button>
        </div>
    </div>
</x-user-layout>
