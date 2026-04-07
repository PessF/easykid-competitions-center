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
                'robot_name' => $c->robot_name,
                'robot_weight' => $c->robot_weight,
                'robot_image_url' => $c->robot_image_url,
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
            width: 5px;
        }

        .ek-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .dark .ek-scrollbar::-webkit-scrollbar-thumb {
            background: #4b5563;
        }

        .custom-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239CA3AF'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1em;
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
    
        getAge(d) {
            let b = new Date(d),
                t = new Date();
            let a = t.getFullYear() - b.getFullYear();
            let m = t.getMonth() - b.getMonth();
            if (m < 0 || (m === 0 && t.getDate() < b.getDate())) a--;
            return a;
        },
        getTeamError(team) {
            if (!this.selectedClass) return '';
    
            let min = this.selectedClass.min_members;
            let max = this.selectedClass.max_members;
            let current = team.members.length;
    
            if (current < min || current > max) {
                return min === max ?
                    `จำนวนสมาชิกต้องมี ${max} คนพอดี` :
                    `จำนวนสมาชิกต้องมี ${min}-${max} คน`;
            }
    
            let cats = this.selectedClass.categories_details || [];
            if (cats.length > 0) {
                let minA = Math.min(...cats.map(c => c.min_age));
                let maxA = Math.max(...cats.map(c => c.max_age));
                for (let mb of team.members) {
                    let age = this.getAge(mb.birth_date);
                    if (age < minA || age > maxA) return `มีสมาชิกอายุไม่เข้าเกณฑ์`;
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
    }" x-init="initMap()"
        class="bg-[#f8f9fa] dark:bg-[#0a0a0a] min-h-screen font-kanit text-gray-800 dark:text-gray-200 pb-20">

        {{-- ─── 1. COMPACT HERO BANNER ─── --}}
        <div class="relative w-full h-[220px] md:h-[280px] bg-gray-900 flex flex-col justify-between">
            @if ($competition->banner_url)
                <img src="{{ asset('storage/' . $competition->banner_url) }}"
                    class="absolute inset-0 w-full h-full object-cover opacity-70">
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-[#f8f9fa] dark:from-[#0a0a0a] via-black/10 to-black/50">
            </div>

            {{-- Top Nav --}}
            <div class="relative z-10 px-4 md:px-8 py-6 w-full max-w-7xl mx-auto flex items-center justify-between">
                <a href="{{ route('user.dashboard') }}"
                    class="inline-flex items-center gap-2 text-sm text-white font-medium hover:text-blue-300 transition-colors drop-shadow-md">
                    <i class="fas fa-arrow-left"></i> หน้าหลัก
                </a>
            </div>

            {{-- Title Area --}}
            <div class="relative z-10 px-4 md:px-8 w-full max-w-7xl mx-auto pb-6 md:pb-10">
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    @php
                        $statusMap = [
                            'open' => [
                                'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400',
                                'เปิดรับสมัคร',
                            ],
                            'coming_soon' => [
                                'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400',
                                'เร็วๆ นี้',
                            ],
                            'registration_closed' => [
                                'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400',
                                'ปิดรับสมัคร',
                            ],
                            'ongoing' => [
                                'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400',
                                'กำลังแข่งขัน',
                            ],
                        ];
                        $st = $statusMap[$competition->dynamic_status] ?? [
                            'bg-gray-200 text-gray-700 dark:bg-white/10 dark:text-gray-300',
                            'จบงาน',
                        ];
                    @endphp
                    <span class="px-2.5 py-1 text-[11px] font-medium rounded-md {{ $st[0] }}">
                        {{ $st[1] }}
                    </span>
                    <span
                        class="px-2.5 py-1 text-[11px] font-medium text-white bg-black/30 backdrop-blur-sm rounded-md border border-white/10">
                        <i class="fas fa-map-marker-alt mr-1 text-gray-300"></i>
                        {{ $competition->location ?? 'รอประกาศ' }}
                    </span>
                </div>
                <h1
                    class="text-2xl md:text-4xl font-semibold text-gray-900 dark:text-white drop-shadow-sm line-clamp-2">
                    {{ $competition->name }}
                </h1>
            </div>
        </div>

        {{-- ─── 2. TWO-COLUMN LAYOUT ─── --}}
        <div class="max-w-7xl mx-auto px-4 md:px-8 mt-4 grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            {{-- ■ LEFT COLUMN: CLASSES LIST (Span 8) ■ --}}
            <div class="lg:col-span-8 flex flex-col gap-6">

                {{-- 2.1 Sticky Smart Filter Bar --}}
                <div
                    class="sticky top-4 z-40 bg-white/80 dark:bg-[#141414]/80 backdrop-blur-xl border border-gray-200 dark:border-gray-800 rounded-2xl p-3 shadow-sm flex flex-col md:flex-row gap-3">

                    {{-- Search Input --}}
                    <div class="relative flex-1">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" x-model="searchQuery" placeholder="ค้นหาชื่อรุ่นแข่งขัน..."
                            class="w-full pl-11 pr-4 py-2.5 bg-gray-50 dark:bg-[#0a0a0a] border border-transparent hover:border-gray-300 dark:hover:border-gray-700 focus:border-blue-500 rounded-xl text-sm font-normal outline-none transition-colors dark:text-white placeholder-gray-400">
                    </div>

                    {{-- Custom Dropdown Filters --}}
                    <div class="flex gap-3 shrink-0">

                        {{-- Dropdown: ประเภทเกม --}}
                        <div x-data="{ open: false }" class="relative w-full md:w-44" @click.outside="open = false">
                            <button @click="open = !open" type="button"
                                class="w-full flex items-center justify-between px-4 py-2.5 bg-gray-50 dark:bg-[#0a0a0a] border border-transparent hover:border-gray-300 dark:hover:border-gray-700 rounded-xl text-sm font-medium transition-colors"
                                :class="open ? 'border-blue-500 ring-1 ring-blue-500 text-blue-600 dark:text-blue-400' :
                                    'text-gray-700 dark:text-gray-300'">
                                <span class="truncate"
                                    x-text="filterGameType === 'all' ? 'ทุกประเภทเกม' : filterGameType"></span>
                                <i class="fas fa-chevron-down text-gray-400 text-[10px] transition-transform duration-200"
                                    :class="open ? 'rotate-180 text-blue-500' : ''"></i>
                            </button>

                            <div x-show="open" x-transition.opacity.duration.200ms style="display: none;"
                                class="absolute z-50 w-full mt-1.5 bg-white dark:bg-[#1a1a1a] border border-gray-100 dark:border-gray-800 rounded-xl shadow-xl overflow-hidden">
                                <div class="max-h-60 overflow-y-auto ek-scrollbar py-1.5">
                                    <button @click="filterGameType = 'all'; open = false"
                                        class="w-full text-left px-4 py-2 text-sm transition-colors hover:bg-gray-50 dark:hover:bg-white/5"
                                        :class="filterGameType === 'all' ?
                                            'text-blue-600 font-semibold bg-blue-50/50 dark:bg-blue-500/10' :
                                            'text-gray-700 dark:text-gray-300'">
                                        ทุกประเภทเกม
                                    </button>
                                    @foreach ($uniqueGameTypes as $gt)
                                        <button @click="filterGameType = '{{ $gt }}'; open = false"
                                            class="w-full text-left px-4 py-2 text-sm transition-colors hover:bg-gray-50 dark:hover:bg-white/5"
                                            :class="filterGameType === '{{ $gt }}' ?
                                                'text-blue-600 font-semibold bg-blue-50/50 dark:bg-blue-500/10' :
                                                'text-gray-700 dark:text-gray-300'">
                                            {{ $gt }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Dropdown: หมวดหมู่อายุ --}}
                        <div x-data="{ open: false }" class="relative w-full md:w-56" @click.outside="open = false">
                            <button @click="open = !open" type="button"
                                class="w-full flex items-center justify-between px-4 py-2.5 bg-gray-50 dark:bg-[#0a0a0a] border border-transparent hover:border-gray-300 dark:hover:border-gray-700 rounded-xl text-sm font-medium transition-colors"
                                :class="open ? 'border-blue-500 ring-1 ring-blue-500 text-blue-600 dark:text-blue-400' :
                                    'text-gray-700 dark:text-gray-300'">
                                <span class="truncate"
                                    x-text="filterCategory === 'all' ? 'ทุกช่วงอายุ' : filterCategory"></span>
                                <i class="fas fa-chevron-down text-gray-400 text-[10px] transition-transform duration-200"
                                    :class="open ? 'rotate-180 text-blue-500' : ''"></i>
                            </button>

                            <div x-show="open" x-transition.opacity.duration.200ms style="display: none;"
                                class="absolute z-50 right-0 w-full mt-1.5 bg-white dark:bg-[#1a1a1a] border border-gray-100 dark:border-gray-800 rounded-xl shadow-xl overflow-hidden">
                                <div class="max-h-60 overflow-y-auto ek-scrollbar py-1.5">
                                    <button @click="filterCategory = 'all'; open = false"
                                        class="w-full text-left px-4 py-2 text-sm transition-colors hover:bg-gray-50 dark:hover:bg-white/5"
                                        :class="filterCategory === 'all' ?
                                            'text-blue-600 font-semibold bg-blue-50/50 dark:bg-blue-500/10' :
                                            'text-gray-700 dark:text-gray-300'">
                                        ทุกช่วงอายุ
                                    </button>
                                    @foreach ($uniqueCategoriesData as $cat)
                                        <button @click="filterCategory = '{{ $cat['name'] }}'; open = false"
                                            class="w-full flex items-center justify-between px-4 py-2 text-sm transition-colors hover:bg-gray-50 dark:hover:bg-white/5"
                                            :class="filterCategory === '{{ $cat['name'] }}' ?
                                                'text-blue-600 font-semibold bg-blue-50/50 dark:bg-blue-500/10' :
                                                'text-gray-700 dark:text-gray-300'">
                                            <span>{{ $cat['name'] }}</span>
                                            <span
                                                class="text-[10px] opacity-60">({{ $cat['min_age'] }}-{{ $cat['max_age'] }}
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
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        แสดงผล <span class="font-semibold text-gray-900 dark:text-white"
                            x-text="filteredClasses.length"></span> รายการ
                    </p>
                </div>

                {{-- 2.3 🚀 REDESIGNED CLASS CARD 🚀 --}}
                <div class="flex flex-col gap-4">
                    <template x-for="cls in filteredClasses" :key="cls.id">
                        <div
                            class="bg-white dark:bg-[#141414] border border-gray-100 dark:border-gray-800 rounded-[1.25rem] p-4 md:p-5 flex flex-col md:flex-row gap-4 md:gap-5 hover:shadow-md hover:border-blue-200 dark:hover:border-blue-900 transition-all duration-300 group">

                            {{-- 🚀 Image Thumbnail (Fit & Cover) --}}
                            <div
                                class="-mx-4 -mt-4 md:m-0 w-[calc(100%+2rem)] md:w-36 h-48 md:h-32 rounded-t-[1.25rem] rounded-b-none md:rounded-xl border-b md:border-b-0 md:border border-gray-100 dark:border-gray-800 shrink-0 overflow-hidden relative bg-white dark:bg-[#1a1a1a]">
                                <template x-if="cls.robot_image_url">
                                    <img :src="cls.robot_image_url.startsWith('http') ? cls.robot_image_url : '/storage/' + cls
                                        .robot_image_url"
                                        class="absolute inset-0 w-full h-full object-cover bg-white dark:bg-[#1a1a1a] transition-transform duration-500 group-hover:scale-105">
                                </template>
                                <template x-if="!cls.robot_image_url">
                                    <div
                                        class="absolute inset-0 flex items-center justify-center bg-gray-50 dark:bg-[#1a1a1a]">
                                        <i class="fas fa-robot text-4xl text-gray-200 dark:text-gray-700"></i>
                                    </div>
                                </template>
                            </div>

                            {{-- Details Area --}}
                            <div class="flex-1 min-w-0 flex flex-col pt-1 md:pt-0">
                                {{-- Tags Section --}}
                                <div class="flex flex-wrap items-center gap-1.5 mb-2">
                                    <span
                                        class="text-[10px] font-semibold tracking-wide text-blue-600 bg-blue-50 dark:bg-blue-500/10 px-2.5 py-1 rounded-md"
                                        x-text="cls.game_type"></span>

                                    <template x-for="cat in cls.categories_details" :key="cat.name">
                                        <span
                                            class="text-[10px] font-medium text-gray-500 bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-gray-700 px-2.5 py-1 rounded-md">
                                            <span x-text="cat.name"></span>
                                            <span class="opacity-60 ml-0.5"
                                                x-text="`(${cat.min_age}-${cat.max_age} ปี)`"></span>
                                        </span>
                                    </template>
                                </div>

                                {{-- Title --}}
                                <h3 class="text-base md:text-lg font-semibold text-gray-900 dark:text-white leading-snug line-clamp-2 mb-3"
                                    x-text="cls.name" :title="cls.name"></h3>

                                {{-- Specs Info --}}
                                <div
                                    class="mt-auto flex flex-wrap items-center gap-x-5 gap-y-2 text-xs text-gray-500 dark:text-gray-400 font-medium">
                                    <div class="flex items-center gap-1.5">
                                        <i class="fas fa-robot text-gray-400"></i>
                                        <span x-text="cls.robot_name"></span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <i class="fas fa-weight-hanging text-gray-400"></i>
                                        <span
                                            x-text="cls.robot_weight ? cls.robot_weight + ' Kg' : 'ไม่จำกัดน้ำหนัก'"></span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <i class="fas fa-users text-gray-400"></i>
                                        <span
                                            x-text="cls.min_members === cls.max_members ? `สมาชิก ${cls.max_members} คน/ทีม` : `สมาชิก ${cls.min_members}-${cls.max_members} คน/ทีม`"></span>
                                    </div>
                                </div>
                            </div>

                            {{-- Price & Actions Section --}}
                            <div
                                class="flex flex-row md:flex-col items-center md:items-end justify-between md:justify-center gap-3 md:w-36 border-t md:border-t-0 md:border-l border-gray-100 dark:border-gray-800 pt-4 md:pt-0 md:pl-5 shrink-0">

                                {{-- Price --}}
                                <div class="text-left md:text-right w-full">
                                    <p class="text-[10px] font-medium text-gray-400 mb-0.5">ค่าสมัคร</p>
                                    <p class="text-xl font-semibold text-emerald-600 dark:text-emerald-400 leading-none"
                                        x-text="cls.entry_fee > 0 ? parseInt(cls.entry_fee).toLocaleString() + ' ฿' : 'ฟรี'">
                                    </p>
                                </div>

                                {{-- Buttons --}}
                                <div class="flex flex-row md:flex-col gap-2 w-full">
                                    @if ($competition->dynamic_status === 'open')
                                        <button @click="openRegisterModal(cls)"
                                            class="flex-[2] md:w-full py-2.5 bg-gray-900 hover:bg-blue-600 dark:bg-white dark:hover:bg-blue-500 text-white dark:text-gray-900 text-sm font-medium rounded-xl transition-colors">
                                            สมัครแข่ง
                                        </button>
                                    @else
                                        <button disabled
                                            class="flex-[2] md:w-full py-2.5 bg-gray-100 dark:bg-[#111] text-gray-400 text-xs font-medium rounded-xl cursor-not-allowed border border-gray-200 dark:border-gray-800">
                                            ปิดรับสมัคร
                                        </button>
                                    @endif

                                    <template x-if="cls.rules_url">
                                        <a :href="`/competitions/{{ $competition->id }}/classes/${cls.id}/rule`"
                                            target="_blank"
                                            class="flex-1 md:w-full flex items-center justify-center gap-1.5 py-2.5 bg-red-50 hover:bg-red-100 dark:bg-red-500/10 dark:hover:bg-red-500/20 text-red-600 dark:text-red-400 text-xs font-medium rounded-xl transition-colors border border-red-100 dark:border-red-500/20 shrink-0"
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
                        class="text-center py-16 bg-white dark:bg-[#141414] border border-gray-100 dark:border-gray-800 rounded-2xl">
                        <i class="far fa-folder-open text-3xl text-gray-300 dark:text-gray-700 mb-3"></i>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                            ไม่พบรุ่นการแข่งขันตามเงื่อนไขที่ค้นหา</p>
                        <button @click="searchQuery=''; filterGameType='all'; filterCategory='all'"
                            class="mt-3 text-sm font-medium text-blue-600 hover:underline">ล้างตัวกรองทั้งหมด</button>
                    </div>
                </div>
            </div>

            {{-- ■ RIGHT COLUMN: INFO & MAP (Span 4) ■ --}}
            <div class="lg:col-span-4 flex flex-col gap-6">

                {{-- Date Timeline Card --}}
                <div
                    class="bg-white dark:bg-[#141414] border border-gray-200 dark:border-gray-800 rounded-2xl p-6 shadow-sm">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <i class="far fa-calendar text-gray-400"></i> กำหนดการ
                    </h3>
                    <div class="space-y-4">
                        <div class="flex gap-4">
                            <div class="w-1.5 h-1.5 rounded-full bg-blue-500 mt-2 shrink-0"></div>
                            <div>
                                <p class="text-xs font-medium text-gray-500 mb-0.5">เปิดรับสมัคร</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    @if ($competition->regis_start_date)
                                        {{ \Carbon\Carbon::parse($competition->regis_start_date)->translatedFormat('d M y') }}
                                    @else
                                        รอประกาศ
                                    @endif
                                    - <span class="text-red-500">
                                        @if ($competition->regis_end_date)
                                            {{ \Carbon\Carbon::parse($competition->regis_end_date)->translatedFormat('d M y') }}
                                        @endif
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 mt-2 shrink-0"></div>
                            <div>
                                <p class="text-xs font-medium text-gray-500 mb-0.5">วันแข่งขันจริง</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
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
                <div
                    class="bg-white dark:bg-[#141414] border border-gray-200 dark:border-gray-800 rounded-2xl overflow-hidden shadow-sm">
                    <div x-ref="mapEl" class="w-full h-40 bg-gray-100"></div>
                    <div class="p-5">
                        <p class="text-xs font-medium text-gray-500 mb-1">สถานที่แข่งขัน</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white mb-4">{{ $competition->location }}
                        </p>
                        <a href="https://www.google.com/maps/search/?api=1&query={{ $competition->latitude }},{{ $competition->longitude }}"
                            target="_blank"
                            class="flex items-center justify-center w-full py-2.5 bg-gray-50 hover:bg-gray-100 dark:bg-white/5 dark:hover:bg-white/10 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-medium transition-colors">
                            <i class="fas fa-map-marked-alt mr-2 text-gray-400"></i> ดูแผนที่นำทาง
                        </a>
                    </div>
                </div>

                {{-- About Card --}}
                {{-- About Card --}}
                <div
                    class="bg-white dark:bg-[#141414] border border-gray-200 dark:border-gray-800 rounded-2xl p-6 shadow-sm">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">เกี่ยวกับงาน</h3>

                    {{-- 🚀 แก้ตรงนี้: เพิ่ม break-words และจัดการ overflow --}}
                    <div class="text-xs font-normal text-gray-600 dark:text-gray-400 leading-relaxed break-words overflow-hidden w-full"
                        :class="descExpanded ? '' : 'line-clamp-4'">
                        {!! nl2br(e($competition->description)) !!}
                    </div>

                    <button @click="descExpanded = !descExpanded"
                        class="mt-2 text-xs font-medium text-blue-600 hover:underline">
                        <span x-text="descExpanded ? 'ซ่อน' : 'อ่านต่อ'"></span>
                    </button>
                </div>

            </div>
        </div>

        {{-- ─── 3. CLEAN MODAL ─── --}}
        <div x-show="isModalOpen" x-cloak style="display:none;"
            class="fixed inset-0 z-[100] flex items-end md:items-center justify-center bg-gray-900/30 backdrop-blur-sm p-0 md:p-6"
            x-transition.opacity>

            <div class="absolute inset-0" @click="closeModal()"></div>

            <div class="relative bg-white dark:bg-[#141414] w-full max-w-md rounded-t-[2rem] md:rounded-3xl shadow-2xl flex flex-col max-h-[85vh] border border-gray-100 dark:border-gray-800"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0">

                {{-- Header --}}
                <div
                    class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center bg-gray-50/50 dark:bg-white/5">
                    <div>
                        <p class="text-xs text-gray-500 font-medium mb-1">ยืนยันการสมัครรุ่น</p>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white line-clamp-1"
                            x-text="selectedClass ? selectedClass.name : ''"></h3>
                    </div>
                    <button @click="closeModal()"
                        class="w-8 h-8 rounded-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 flex items-center justify-center text-gray-500 hover:text-gray-900 dark:hover:text-white transition-colors shrink-0">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                {{-- Body --}}
                <div class="p-6 overflow-y-auto flex-1 hide-scroll">
                    <div x-show="myTeams.length > 0" class="space-y-3">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">เลือกทีมของคุณ</p>

                        <template x-for="team in myTeams" :key="team.id">
                            <label
                                :class="isTeamEligible(team) ? (selectedTeamId == team.id ?
                                        'border-blue-500 bg-blue-50/50 dark:bg-blue-900/10 ring-1 ring-blue-500' :
                                        'border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-white/5') :
                                    'opacity-50 grayscale cursor-not-allowed border-gray-100 bg-gray-50 dark:bg-[#0a0a0a] dark:border-gray-800'"
                                class="relative flex items-center p-4 border rounded-xl cursor-pointer transition-all">

                                <div class="w-5 h-5 rounded-full border flex items-center justify-center shrink-0 mr-4 transition-colors"
                                    :class="selectedTeamId == team.id ? 'border-blue-500 bg-blue-500' : 'border-gray-300'">
                                    <i class="fas fa-check text-[10px] text-white"
                                        x-show="selectedTeamId == team.id"></i>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-900 dark:text-white text-sm" x-text="team.name">
                                    </p>
                                    <p class="text-xs text-gray-500 font-normal mt-0.5"><span
                                            x-text="team.members.length"></span> คน • <span
                                            x-text="team.school_name"></span></p>
                                </div>

                                <div x-show="!isTeamEligible(team)"
                                    class="text-[11px] text-red-500 font-medium text-right ml-2">
                                    <span x-text="getTeamError(team)"></span>
                                </div>

                                <input type="radio" name="team_id" :value="team.id" x-model="selectedTeamId"
                                    class="hidden" :disabled="!isTeamEligible(team)">
                            </label>
                        </template>
                    </div>

                    <div x-show="myTeams.length === 0" class="text-center py-8">
                        <div
                            class="w-16 h-16 bg-blue-50 text-blue-500 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-users text-xl"></i>
                        </div>
                        <p class="font-medium text-gray-900 dark:text-white mb-1">คุณยังไม่มีทีมในระบบ</p>
                        <p class="text-sm text-gray-500 mb-6">สร้างทีมและเพิ่มชื่อสมาชิกก่อนกดสมัครนะครับ</p>
                        <a href="{{ route('user.teams.index') }}"
                            class="px-6 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-xl text-sm font-medium">ไปหน้าจัดการทีม</a>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800 bg-white dark:bg-[#141414]">
                    <form method="POST"
                        :action="selectedClass ?
                            `{{ url('competitions') }}/{{ $competition->id }}/classes/${selectedClass.id}/register` :
                            '#'">
                        @csrf
                        <input type="hidden" name="team_id" :value="selectedTeamId">
                        <button type="submit" :disabled="!selectedTeamId"
                            class="w-full py-3 bg-blue-600 text-white rounded-xl text-sm font-medium disabled:opacity-40 disabled:cursor-not-allowed transition-colors hover:bg-blue-700">
                            ดำเนินการสมัครแข่งขัน
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </main>
</x-user-layout>
