<x-user-layout>
    <x-slot name="title">{{ $competition->name }} | Easykids</x-slot>

    @php
        $uniqueGameTypes = $competition->classes->pluck('game_type_name')->filter()->unique()->values();

        // 🚀 ดึงข้อมูลหมวดหมู่ทั้งหมดแบบไม่ซ้ำ พร้อมทั้งเก็บค่า min/max age เอาไว้ด้วย
        $uniqueCategoriesData = collect();
        $competition->classes
            ->pluck('allowed_categories')
            ->flatten(1)
            ->each(function ($cat) use ($uniqueCategoriesData) {
                if ($cat && !$uniqueCategoriesData->has($cat['name'])) {
                    $uniqueCategoriesData->put($cat['name'], $cat);
                }
            });
        $uniqueCategoriesData = $uniqueCategoriesData->values();

        $alpineClasses = $competition->classes->map(function ($c) {
            return [
                'id' => $c->id,
                'name' => $c->name,
                'searchString' => strtolower($c->name),
                'game_type' => $c->game_type_name,
                'min_members' => $c->min_members ?? 1,
                'max_members' => $c->max_members,
                'entry_fee' => $c->entry_fee,
                'robot_weight' => $c->robot_weight,
                'rules_url' => $c->rules_url,
                'categories' => collect($c->allowed_categories)->pluck('name')->toArray(),
                'categories_details' => $c->allowed_categories,
            ];
        });
    @endphp

    <style>
        [x-cloak] {
            display: none !important;
        }

        .ek-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .ek-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .ek-scrollbar::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 10px;
        }

        /* เอาลูกศรแบบเดิมออก เปลี่ยนไปใช้ SVG ในปุ่มแทนแล้ว */
        .custom-select {
            appearance: none;
        }
    </style>

    <main x-data="{
        searchQuery: '',
        filterGameType: 'all',
        filterCategory: 'all',
        classes: {{ Js::from($alpineClasses) }},
    
        isModalOpen: false,
        selectedClass: null,
        selectedTeamId: null,
        myTeams: {{ Js::from($myTeams ?? []) }},
        descExpanded: false,

        // 🚀 ดึงวันแข่งขันจากฐานข้อมูล (ถ้าไม่มีให้ใช้วันนี้)
        eventStartDate: '{{ $competition->event_start_date ? \Carbon\Carbon::parse($competition->event_start_date)->format('Y-m-d') : '' }}',
    
        get filteredClasses() {
            return this.classes.filter(c => {
                let matchSearch = this.searchQuery === '' || c.searchString.includes(this.searchQuery.toLowerCase().trim());
                let matchGame = this.filterGameType === 'all' || c.game_type === this.filterGameType;
                let matchCat = this.filterCategory === 'all' || c.categories.includes(this.filterCategory);
                return matchSearch && matchGame && matchCat;
            });
        },
    
        openRegisterModal(cls) {
            this.selectedClass = cls;
            this.selectedTeamId = null;
            this.isModalOpen = true;
            document.body.style.overflow = 'hidden';
        },
        closeModal() {
            this.isModalOpen = false;
            setTimeout(() => { this.selectedClass = null; }, 300);
            document.body.style.overflow = '';
        },
    
        // 🚀 ฟังก์ชันคำนวณอายุโดยใช้วันที่แข่งขันเป็นเกณฑ์ (แก้ Timezone Bug แล้ว)
        getAgeAtEvent(d) {
            if (!d) return 0;
            
            let baseDate = new Date();
            if (this.eventStartDate) {
                let parts = this.eventStartDate.split('-');
                baseDate = new Date(parts[0], parts[1] - 1, parts[2]);
            }
            
            // ป้องกัน Bug วันที่ที่อาจติด Timezone มาจาก DB
            let dStr = d.split('T')[0]; 
            let bParts = dStr.split('-');
            let birthDate = new Date(bParts[0], bParts[1] - 1, bParts[2]);
            
            let age = baseDate.getFullYear() - birthDate.getFullYear();
            let m = baseDate.getMonth() - birthDate.getMonth();
            if (m < 0 || (m === 0 && baseDate.getDate() < birthDate.getDate())) {
                age--;
            }
            return age >= 0 ? age : 0;
        },

        getTeamError(team) {
            if (!this.selectedClass) return '';
    
            let min = this.selectedClass.min_members;
            let max = this.selectedClass.max_members;
            let current = team.members.length;
    
            if (current < min || current > max) {
                return min === max ?
                    `ต้องมีสมาชิก ${max} คนพอดี` :
                    `ต้องมีสมาชิก ${min}-${max} คน`;
            }
    
            let cats = this.selectedClass.categories_details || [];
            if (cats.length > 0) {
                let minA = Math.min(...cats.map(c => parseInt(c.min_age)));
                let maxA = Math.max(...cats.map(c => parseInt(c.max_age)));
                
                for (let mb of team.members) {
                    if (!mb.birth_date) {
                        return `มีสมาชิกระบุวันเกิดไม่ครบ`;
                    }
                    
                    // 🚀 คำนวณอายุ ณ วันที่แข่งจริง
                    let age = this.getAgeAtEvent(mb.birth_date);
                    
                    // ถ้าอายุหลุดเกณฑ์แม้แต่คนเดียว ให้บล็อกทันที
                    if (age < minA || age > maxA) {
                        return `อายุไม่เข้าเกณฑ์ (${minA}-${maxA} ปี ณ วันแข่ง)`;
                    }
                }
            }
            return '';
        },
        isTeamEligible(team) { return this.getTeamError(team) === ''; },
    
        initMap() {
            let lat = {{ $competition->latitude ?? 18.7883 }},
                lng = {{ $competition->longitude ?? 98.9853 }};
            let map = L.map(this.$refs.mapEl, { zoomControl: false, scrollWheelZoom: false }).setView([lat, lng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
            L.marker([lat, lng]).addTo(map);
        }
    }" x-init="initMap()" class="bg-[#0a0a0a] min-h-screen font-kanit text-gray-300 pb-20">

        {{-- ─── 1. COMPACT HERO BANNER ─── --}}
        <div
            class="relative w-full h-[220px] md:h-[280px] bg-[#0a0a0a] flex flex-col justify-between border-b border-white/5 overflow-hidden">
            @if ($competition->banner_url)
                <img src="{{ asset('storage/' . $competition->banner_url) }}"
                    class="absolute inset-0 w-full h-full object-cover opacity-30">
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-[#0a0a0a] via-[#0a0a0a]/60 to-transparent">
            </div>

            {{-- Top Nav --}}
            <div class="relative z-10 px-4 md:px-8 py-6 w-full max-w-7xl mx-auto flex items-center justify-between">
                <a href="{{ route('user.dashboard') }}"
                    class="inline-flex items-center gap-2 text-sm text-gray-400 font-normal hover:text-white transition-colors drop-shadow-md">
                    <i class="fas fa-arrow-left"></i> หน้าหลัก
                </a>
            </div>

            {{-- Title Area --}}
            <div class="relative z-10 px-4 md:px-8 w-full max-w-7xl mx-auto pb-6 md:pb-10">
                <div class="flex flex-wrap items-center gap-2 mb-2.5">
                    @php
                        $statusMap = [
                            'open' => ['bg-emerald-500/10 text-emerald-400 border-emerald-500/20', 'เปิดรับสมัคร'],
                            'coming_soon' => ['bg-amber-500/10 text-amber-400 border-amber-500/20', 'เร็วๆ นี้'],
                            'registration_closed' => ['bg-red-500/10 text-red-400 border-red-500/20', 'ปิดรับสมัคร'],
                            'ongoing' => ['bg-blue-500/10 text-blue-400 border-blue-500/20', 'กำลังแข่งขัน'],
                        ];
                        $st = $statusMap[$competition->dynamic_status] ?? [
                            'bg-white/5 text-gray-400 border-white/10',
                            'จบงาน',
                        ];
                    @endphp
                    <span class="px-2.5 py-1 text-[11px] font-normal rounded-md border {{ $st[0] }}">
                        {{ $st[1] }}
                    </span>
                    <span
                        class="px-2.5 py-1 text-[11px] font-normal text-gray-300 bg-[#121212]/80 backdrop-blur-sm rounded-md border border-white/5">
                        <i class="fas fa-map-marker-alt mr-1 text-gray-500"></i>
                        {{ $competition->location ?? 'รอประกาศ' }}
                    </span>
                </div>
                <h1 class="text-2xl md:text-4xl font-normal text-white drop-shadow-sm line-clamp-2">
                    {{ $competition->name }}
                </h1>
            </div>
        </div>

        {{-- ─── 2. TWO-COLUMN LAYOUT ─── --}}
        <div class="max-w-7xl mx-auto px-4 md:px-8 mt-4 md:mt-6 grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            {{-- ■ LEFT COLUMN: CLASSES LIST (Span 8) ■ --}}
            <div class="lg:col-span-8 flex flex-col gap-6">

                {{-- 2.1 Smart Filter Bar --}}
                <div
                    class="relative z-40 bg-[#141414] border border-white/5 rounded-2xl p-3 sm:p-4 shadow-sm flex flex-col md:flex-row gap-3">

                    {{-- Search Input --}}
                    <div class="relative flex-1">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-500"></i>
                        <input type="text" x-model="searchQuery" placeholder="ค้นหาชื่อรุ่นแข่งขัน..."
                            class="w-full pl-11 pr-4 py-2.5 sm:py-3 bg-[#0a0a0a] border border-white/5 hover:border-white/10 focus:ring-1 focus:ring-blue-500/50 focus:border-blue-500 rounded-xl text-sm font-normal outline-none transition-colors text-white placeholder-gray-600">
                    </div>

                    {{-- Custom Dropdown Filters --}}
                    <div class="flex flex-col sm:flex-row gap-3 shrink-0">

                        {{-- Dropdown: ประเภทเกม --}}
                        <div x-data="{ open: false }" class="relative w-full sm:w-44" @click.outside="open = false">
                            <button @click="open = !open" type="button"
                                class="w-full flex items-center justify-between px-4 py-2.5 sm:py-3 bg-[#0a0a0a] border hover:border-white/10 rounded-xl text-sm font-normal transition-colors"
                                :class="open ? 'border-blue-500 text-blue-400' : 'border-white/5 text-gray-400'">
                                <span class="truncate"
                                    x-text="filterGameType === 'all' ? 'ทุกประเภทเกม' : filterGameType"></span>
                                <i class="fas fa-chevron-down text-gray-600 text-[10px] transition-transform duration-200"
                                    :class="open ? 'rotate-180 text-blue-500' : ''"></i>
                            </button>

                            <div x-show="open" x-transition.opacity.duration.200ms style="display: none;"
                                class="absolute z-50 w-full mt-1.5 bg-[#1a1a1a] border border-white/10 rounded-xl shadow-xl overflow-hidden">
                                <div class="max-h-60 overflow-y-auto ek-scrollbar py-1.5">
                                    <button @click="filterGameType = 'all'; open = false"
                                        class="w-full text-left px-4 py-2.5 text-sm transition-colors hover:bg-white/5"
                                        :class="filterGameType === 'all' ? 'text-blue-400 font-normal bg-blue-500/10' :
                                            'text-gray-400 hover:text-white'">
                                        ทุกประเภทเกม
                                    </button>
                                    @foreach ($uniqueGameTypes as $gt)
                                        <button @click="filterGameType = '{{ $gt }}'; open = false"
                                            class="w-full text-left px-4 py-2.5 text-sm transition-colors hover:bg-white/5"
                                            :class="filterGameType === '{{ $gt }}' ?
                                                'text-blue-400 font-normal bg-blue-500/10' :
                                                'text-gray-400 hover:text-white'">
                                            {{ $gt }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Dropdown: หมวดหมู่อายุ --}}
                        <div x-data="{ open: false }" class="relative w-full sm:w-56" @click.outside="open = false">
                            <button @click="open = !open" type="button"
                                class="w-full flex items-center justify-between px-4 py-2.5 sm:py-3 bg-[#0a0a0a] border hover:border-white/10 rounded-xl text-sm font-normal transition-colors"
                                :class="open ? 'border-blue-500 text-blue-400' : 'border-white/5 text-gray-400'">
                                <span class="truncate"
                                    x-text="filterCategory === 'all' ? 'ทุกช่วงอายุ' : filterCategory"></span>
                                <i class="fas fa-chevron-down text-gray-600 text-[10px] transition-transform duration-200"
                                    :class="open ? 'rotate-180 text-blue-500' : ''"></i>
                            </button>

                            <div x-show="open" x-transition.opacity.duration.200ms style="display: none;"
                                class="absolute z-50 sm:right-0 w-full mt-1.5 bg-[#1a1a1a] border border-white/10 rounded-xl shadow-xl overflow-hidden">
                                <div class="max-h-60 overflow-y-auto ek-scrollbar py-1.5">
                                    <button @click="filterCategory = 'all'; open = false"
                                        class="w-full text-left px-4 py-2.5 text-sm transition-colors hover:bg-white/5"
                                        :class="filterCategory === 'all' ? 'text-blue-400 font-normal bg-blue-500/10' :
                                            'text-gray-400 hover:text-white'">
                                        ทุกช่วงอายุ
                                    </button>
                                    @foreach ($uniqueCategoriesData as $cat)
                                        <button @click="filterCategory = '{{ $cat['name'] }}'; open = false"
                                            class="w-full flex items-center justify-between px-4 py-2.5 text-sm transition-colors hover:bg-white/5"
                                            :class="filterCategory === '{{ $cat['name'] }}' ?
                                                'text-blue-400 font-normal bg-blue-500/10' :
                                                'text-gray-400 hover:text-white'">
                                            <span>{{ $cat['name'] }}</span>
                                            <span
                                                class="text-[10px] opacity-50">({{ $cat['min_age'] }}-{{ $cat['max_age'] }}
                                                ปี)</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- 2.2 Results Header --}}
                <div class="flex items-center justify-between px-1">
                    <p class="text-xs sm:text-sm font-normal text-gray-500">
                        แสดงผล <span class="font-normal text-white" x-text="filteredClasses.length"></span> รายการ
                    </p>
                </div>

                {{-- 2.3 REDESIGNED CLASS CARD --}}
                <div class="flex flex-col gap-4 sm:gap-5">
                    <template x-for="cls in filteredClasses" :key="cls.id">
                        <div
                            class="bg-[#141414] border border-white/5 rounded-2xl sm:rounded-[1.25rem] p-4 md:p-5 flex flex-col md:flex-row gap-4 md:gap-5 hover:border-blue-500/30 hover:bg-[#1a1a1a] transition-all duration-300 group overflow-hidden shadow-sm">

                            {{-- Details Area --}}
                            <div class="flex-1 min-w-0 flex flex-col pt-1 md:pt-0">
                                {{-- Tags Section --}}
                                <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 mb-2.5 sm:mb-3">
                                    <span
                                        class="text-[9px] sm:text-[10px] font-normal tracking-wide text-blue-400 bg-blue-500/10 border border-blue-500/20 px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-md"
                                        x-text="cls.game_type"></span>

                                    <template x-for="cat in cls.categories_details" :key="cat.name">
                                        <span
                                            class="text-[9px] sm:text-[10px] font-normal text-gray-400 bg-white/5 border border-white/10 px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-md">
                                            <span x-text="cat.name"></span>
                                            <span class="opacity-60 ml-0.5"
                                                x-text="`(${cat.min_age}-${cat.max_age} ปี)`"></span>
                                        </span>
                                    </template>
                                </div>

                                {{-- Title --}}
                                <h3 class="text-base md:text-lg font-normal text-white leading-snug line-clamp-2 mb-3"
                                    x-text="cls.name" :title="cls.name"></h3>

                                {{-- Specs Info --}}
                                <div
                                    class="mt-auto flex flex-wrap items-center gap-x-4 sm:gap-x-5 gap-y-1.5 sm:gap-y-2 text-[11px] sm:text-xs text-gray-500 font-normal">
                                    <div class="flex items-center gap-1.5">
                                        <i class="fas fa-weight-hanging opacity-60"></i>
                                        <span
                                            x-text="cls.robot_weight ? cls.robot_weight + ' Kg' : 'ไม่จำกัดน้ำหนัก'"></span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <i class="fas fa-users opacity-60"></i>
                                        <span
                                            x-text="cls.min_members === cls.max_members ? `สมาชิก ${cls.max_members} คน/ทีม` : `สมาชิก ${cls.min_members}-${cls.max_members} คน/ทีม`"></span>
                                    </div>
                                </div>
                            </div>

                            {{-- Price & Actions Section --}}
                            <div
                                class="flex flex-row md:flex-col items-center md:items-end justify-between md:justify-center gap-3 md:w-36 border-t border-white/5 md:border-t-0 md:border-l pt-4 md:pt-0 md:pl-5 shrink-0 transition-colors">

                                {{-- Price --}}
                                <div class="text-left md:text-right w-full">
                                    <p class="text-[9px] sm:text-[10px] font-normal text-gray-500 mb-0.5 sm:mb-1">
                                        ค่าสมัคร</p>
                                    <p class="text-lg sm:text-xl font-normal text-emerald-400 leading-none"
                                        x-text="cls.entry_fee > 0 ? parseInt(cls.entry_fee).toLocaleString() + ' ฿' : 'ฟรี'">
                                    </p>
                                </div>

                                {{-- Buttons --}}
                                <div class="flex flex-row md:flex-col gap-2 w-full">
                                    @if ($competition->dynamic_status === 'open')
                                        <button @click="openRegisterModal(cls)"
                                            class="flex-[2] md:w-full py-2 sm:py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-xs sm:text-sm font-normal rounded-xl transition-colors shadow-sm">
                                            สมัครแข่ง
                                        </button>
                                    @else
                                        <button disabled
                                            class="flex-[2] md:w-full py-2 sm:py-2.5 bg-[#0a0a0a] text-gray-600 text-xs font-normal rounded-xl cursor-not-allowed border border-white/5">
                                            ปิดรับสมัคร
                                        </button>
                                    @endif

                                    <template x-if="cls.rules_url">
                                        <a :href="`/competitions/{{ $competition->id }}/classes/${cls.id}/rule`"
                                            target="_blank"
                                            class="flex-1 md:w-full flex items-center justify-center gap-1 sm:gap-1.5 py-2 sm:py-2.5 bg-[#1a1a1a] hover:bg-white/5 text-gray-300 hover:text-white text-[10px] sm:text-xs font-normal rounded-xl transition-colors border border-white/5 shrink-0"
                                            title="อ่านกติกา">
                                            <i class="far fa-file-pdf"></i> กติกา
                                        </a>
                                    </template>
                                </div>
                            </div>

                        </div>
                    </template>

                    {{-- Empty State --}}
                    <div x-show="filteredClasses.length === 0" style="display:none;"
                        class="text-center py-16 sm:py-20 bg-[#121212] border border-white/5 rounded-2xl shadow-sm">
                        <i class="far fa-folder-open text-3xl sm:text-4xl text-gray-700 mb-3 sm:mb-4"></i>
                        <p class="text-xs sm:text-sm font-normal text-gray-400">ไม่พบรุ่นการแข่งขันตามเงื่อนไขที่ค้นหา
                        </p>
                        <button @click="searchQuery=''; filterGameType='all'; filterCategory='all'"
                            class="mt-3 sm:mt-4 text-xs sm:text-sm font-normal text-blue-400 hover:text-blue-300 hover:underline">ล้างตัวกรองทั้งหมด</button>
                    </div>
                </div>
            </div>

            {{-- ■ RIGHT COLUMN: INFO & MAP (Span 4) ■ --}}
            <div class="lg:col-span-4 flex flex-col gap-6 mt-6 lg:mt-0">

                {{-- Date Timeline Card --}}
                <div class="bg-[#121212] border border-white/5 rounded-2xl p-5 sm:p-6 shadow-sm">
                    <h3
                        class="text-xs sm:text-sm font-normal text-white mb-4 sm:mb-5 flex items-center gap-2 border-b border-white/5 pb-2.5 sm:pb-3">
                        <i class="far fa-calendar text-blue-500"></i> กำหนดการ
                    </h3>
                    <div class="space-y-4 sm:space-y-5">
                        <div class="flex gap-3 sm:gap-4">
                            <div
                                class="w-1.5 h-1.5 rounded-full bg-emerald-500 mt-1.5 sm:mt-2 shrink-0 shadow-[0_0_8px_rgba(16,185,129,0.6)]">
                            </div>
                            <div>
                                <p class="text-[10px] sm:text-xs font-normal text-gray-500 mb-0.5 sm:mb-1">เปิดรับสมัคร
                                </p>
                                <p class="text-xs sm:text-sm font-normal text-gray-200">
                                    @if ($competition->regis_start_date)
                                        {{ \Carbon\Carbon::parse($competition->regis_start_date)->translatedFormat('d M y') }}
                                    @else
                                        รอประกาศ
                                    @endif
                                    - <span class="text-red-400">
                                        @if ($competition->regis_end_date)
                                            {{ \Carbon\Carbon::parse($competition->regis_end_date)->translatedFormat('d M y') }}
                                        @endif
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="flex gap-3 sm:gap-4">
                            <div
                                class="w-1.5 h-1.5 rounded-full bg-blue-500 mt-1.5 sm:mt-2 shrink-0 shadow-[0_0_8px_rgba(59,130,246,0.6)]">
                            </div>
                            <div>
                                <p class="text-[10px] sm:text-xs font-normal text-gray-500 mb-0.5 sm:mb-1">
                                    วันแข่งขันจริง</p>
                                <p class="text-xs sm:text-sm font-normal text-gray-200">
                                    @if ($competition->event_start_date)
                                        {{ \Carbon\Carbon::parse($competition->event_start_date)->translatedFormat('d M y') }}
                                    @else
                                        รอประกาศ
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Map Card --}}
                <div class="bg-[#121212] border border-white/5 rounded-2xl overflow-hidden shadow-sm">
                    <div x-ref="mapEl" class="w-full h-40 sm:h-48 z-0 relative"></div>
                    
                    <div class="p-4 sm:p-5 relative z-10">
                        <p class="text-[10px] sm:text-xs font-normal text-gray-500 mb-1">สถานที่แข่งขัน</p>
                        <p class="text-xs sm:text-sm font-normal text-white mb-3 sm:mb-4">{{ $competition->location }}
                        </p>
                        <a href="https://www.google.com/maps/search/?api=1&query={{ $competition->latitude }},{{ $competition->longitude }}"
                            target="_blank"
                            class="flex items-center justify-center w-full py-2 sm:py-2.5 bg-[#1a1a1a] hover:bg-white/5 text-gray-300 hover:text-white rounded-xl text-xs sm:text-sm font-normal transition-colors border border-white/5 shadow-sm">
                            <i class="fas fa-map-marked-alt mr-1.5 sm:mr-2 text-gray-500"></i> ดูแผนที่นำทาง
                        </a>
                    </div>
                </div>

                {{-- About Card --}}
                <div class="bg-[#121212] border border-white/5 rounded-2xl p-5 sm:p-6 shadow-sm">
                    <h3
                        class="text-xs sm:text-sm font-normal text-white mb-3 flex items-center gap-2 border-b border-white/5 pb-2.5 sm:pb-3">
                        <i class="fas fa-info-circle text-blue-500"></i> เกี่ยวกับงาน
                    </h3>
                    <div class="text-[11px] sm:text-xs font-normal text-gray-400 leading-relaxed break-words overflow-hidden w-full"
                        :class="descExpanded ? '' : 'line-clamp-4'">
                        {!! nl2br(e($competition->description)) !!}
                    </div>
                    <button @click="descExpanded = !descExpanded"
                        class="mt-2 text-[10px] sm:text-xs font-normal text-blue-400 hover:text-blue-300 hover:underline focus:outline-none">
                        <span x-text="descExpanded ? 'ซ่อนรายละเอียด' : 'อ่านเพิ่มเติม'"></span>
                    </button>
                </div>

            </div>
        </div>

        {{-- ─── 3. CLEAN MODAL (แก้ไขให้ขึ้นตรงกลาง) ─── --}}
        <div x-show="isModalOpen" x-cloak style="display:none;"
            class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6" x-transition.opacity>

            <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="closeModal()"></div>

            <div class="relative bg-[#121212] w-full max-w-md rounded-2xl sm:rounded-[2rem] shadow-2xl flex flex-col max-h-[85vh] sm:max-h-[90vh] border border-white/10"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0">

                {{-- Header --}}
                <div
                    class="px-5 sm:px-6 py-4 sm:py-5 border-b border-white/5 flex justify-between items-center bg-[#0a0a0a] rounded-t-2xl sm:rounded-t-[2rem]">
                    <div>
                        <p class="text-[10px] sm:text-xs text-gray-500 font-normal mb-0.5 sm:mb-1">ยืนยันการสมัครรุ่น
                        </p>
                        <h3 class="text-base sm:text-lg font-normal text-white line-clamp-1"
                            x-text="selectedClass ? selectedClass.name : ''"></h3>
                    </div>
                    <button @click="closeModal()"
                        class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-[#1a1a1a] border border-white/5 flex items-center justify-center text-gray-500 hover:text-white hover:bg-white/10 transition-colors shrink-0 focus:outline-none">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                {{-- Body --}}
                <div class="p-5 sm:p-6 overflow-y-auto flex-1 custom-scrollbar">
                    <div x-show="myTeams.length > 0" class="space-y-3 sm:space-y-4">
                        <p class="text-xs sm:text-sm font-normal text-gray-300 mb-2 sm:mb-3">เลือกทีมของคุณ</p>

                        <template x-for="team in myTeams" :key="team.id">
                            <label
                                :class="isTeamEligible(team) ? (selectedTeamId == team.id ?
                                        'border-blue-500/50 bg-blue-900/10' :
                                        'border-white/10 hover:bg-[#1a1a1a]') :
                                    'opacity-50 grayscale cursor-not-allowed bg-[#0a0a0a] border-white/5'"
                                class="relative flex items-center p-3 sm:p-4 border rounded-xl sm:rounded-2xl cursor-pointer transition-all duration-200">

                                <div class="w-4 h-4 sm:w-5 sm:h-5 rounded-full border flex items-center justify-center shrink-0 mr-3 sm:mr-4 transition-colors"
                                    :class="selectedTeamId == team.id ? 'border-blue-500 bg-blue-500' : 'border-gray-600'">
                                    <i class="fas fa-check text-[8px] sm:text-[10px] text-white"
                                        x-show="selectedTeamId == team.id"></i>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <p class="font-normal text-white text-xs sm:text-sm truncate" x-text="team.name">
                                    </p>
                                    <p
                                        class="text-[10px] sm:text-xs text-gray-500 font-normal mt-0.5 sm:mt-1 truncate">
                                        <span x-text="team.members.length"></span> คน • <span
                                            x-text="team.school_name"></span>
                                    </p>
                                </div>

                                <div x-show="!isTeamEligible(team)"
                                    class="text-[9px] sm:text-[10px] text-red-400 font-normal text-right ml-2 shrink-0">
                                    <span x-text="getTeamError(team)"></span>
                                </div>

                                <input type="radio" name="team_id" :value="team.id" x-model="selectedTeamId"
                                    class="hidden" :disabled="!isTeamEligible(team)">
                            </label>
                        </template>
                    </div>

                    <div x-show="myTeams.length === 0" class="text-center py-8">
                        <div
                            class="w-14 h-14 sm:w-16 sm:h-16 bg-blue-500/10 text-blue-400 border border-blue-500/20 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                            <i class="fas fa-users text-lg sm:text-xl"></i>
                        </div>
                        <p class="font-normal text-white mb-1 text-sm sm:text-base">คุณยังไม่มีทีมในระบบ</p>
                        <p class="text-[10px] sm:text-xs text-gray-500 mb-5 sm:mb-6">
                            สร้างทีมและเพิ่มชื่อสมาชิกก่อนกดสมัครนะครับ</p>
                        <a href="{{ route('user.teams.index') }}"
                            class="px-5 py-2 sm:px-6 sm:py-2.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-xs sm:text-sm font-normal transition-colors">ไปหน้าจัดการทีม</a>
                    </div>
                </div>

                {{-- Footer --}}
                <div
                    class="px-5 sm:px-6 py-4 border-t border-white/5 bg-[#0a0a0a] rounded-b-2xl sm:rounded-b-[2rem] shrink-0">
                    <form method="POST"
                        :action="selectedClass ?
                            `{{ url('competitions') }}/{{ $competition->id }}/classes/${selectedClass.id}/register` :
                            '#'">
                        @csrf
                        <input type="hidden" name="team_id" :value="selectedTeamId">
                        <button type="submit" :disabled="!selectedTeamId"
                            class="w-full py-2.5 sm:py-3 bg-blue-600 text-white rounded-xl text-xs sm:text-sm font-normal disabled:opacity-40 disabled:bg-[#1a1a1a] disabled:text-gray-500 disabled:cursor-not-allowed transition-colors hover:bg-blue-500 focus:outline-none">
                            ดำเนินการสมัครแข่งขัน
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </main>
</x-user-layout>