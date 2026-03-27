<x-user-layout>
    <x-slot name="title">{{ $competition->name }} | Easykids</x-slot>

    <main class="flex-1 flex flex-col w-full px-4 sm:px-6 lg:px-8 py-8 mx-auto max-w-7xl font-kanit pb-24">

        {{-- ปุ่มย้อนกลับ --}}
        <div class="mb-6">
            <a href="{{ route('user.dashboard') }}"
                class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors bg-white dark:bg-[#1a1a1a] px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm hover:shadow-md">
                <i class="fas fa-arrow-left"></i> กลับไปหน้าค้นหา
            </a>
        </div>

        {{-- HERO BANNER & PRIMARY INFO --}}
        <div
            class="bg-white dark:bg-[#141414] rounded-2xl border border-gray-200 dark:border-gray-800 overflow-hidden shadow-sm mb-8">
            <div class="relative w-full h-64 sm:h-80 md:h-[26rem] bg-[#0a0a0a]">
                <img src="{{ route('user.competitions.banner', $competition->id) }}" alt="{{ $competition->name }}"
                    class="w-full h-full object-cover">

                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>

                {{-- Status Badge --}}
                <div class="absolute top-6 left-6">
                    @if ($competition->status === 'registration')
                        <span
                            class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-500 text-white text-xs font-bold uppercase rounded-lg tracking-wide shadow-md">
                            <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span> เปิดรับสมัคร
                        </span>
                    @elseif ($competition->status === 'ongoing')
                        <span
                            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-xs font-bold uppercase rounded-lg tracking-wide shadow-md">
                            <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span> กำลังแข่งขัน
                        </span>
                    @else
                        <span
                            class="inline-flex items-center gap-2 px-4 py-2 bg-gray-800/90 text-white text-xs font-bold uppercase rounded-lg tracking-wide backdrop-blur-md border border-gray-600">
                            <i class="fas fa-lock text-[10px]"></i> {{ $competition->status }}
                        </span>
                    @endif
                </div>

                {{-- Title & Location Overlay --}}
                <div class="absolute bottom-0 left-0 w-full p-6 sm:p-10">
                    <h1
                        class="text-3xl sm:text-4xl md:text-5xl font-semibold text-white mb-4 leading-tight drop-shadow-xl">
                        {{ $competition->name }}
                    </h1>
                    <div class="flex flex-wrap items-center gap-4 text-sm sm:text-base text-gray-200 font-medium">
                        <span
                            class="flex items-center gap-2 bg-black/50 backdrop-blur-md px-4 py-2 rounded-xl border border-white/10 shadow-sm">
                            <i class="fas fa-map-marker-alt text-red-400"></i> {{ $competition->location }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Timeline Bar --}}
            <div
                class="grid grid-cols-1 sm:grid-cols-2 divide-y sm:divide-y-0 sm:divide-x divide-gray-200 dark:divide-gray-800 bg-gray-50 dark:bg-[#111]">
                <div class="p-6 flex items-center gap-5">
                    <div
                        class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shrink-0 border border-emerald-200 dark:border-emerald-800/50">
                        <i class="fas fa-clipboard-list text-xl"></i>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold uppercase text-gray-500 tracking-wider mb-1">
                            ช่วงเวลาเปิดรับสมัคร</p>
                        <p class="text-sm sm:text-base font-semibold text-gray-900 dark:text-white">
                            {{ \Carbon\Carbon::parse($competition->regis_start_date)->translatedFormat('d M y') }} -
                            <span
                                class="text-red-500">{{ \Carbon\Carbon::parse($competition->regis_end_date)->translatedFormat('d M y') }}</span>
                        </p>
                    </div>
                </div>

                <div class="p-6 flex items-center gap-5">
                    <div
                        class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 shrink-0 border border-blue-200 dark:border-blue-800/50">
                        <i class="fas fa-trophy text-xl"></i>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold uppercase text-gray-500 tracking-wider mb-1">วันแข่งขันจริง</p>
                        <p class="text-sm sm:text-base font-semibold text-gray-900 dark:text-white">
                            {{ \Carbon\Carbon::parse($competition->event_start_date)->translatedFormat('d M y') }} -
                            {{ \Carbon\Carbon::parse($competition->event_end_date)->translatedFormat('d M y') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- DETAILS & MAP --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-10">
            <div
                class="lg:col-span-7 bg-white dark:bg-[#141414] p-6 sm:p-8 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm">
                <h2
                    class="text-xl font-semibold text-gray-900 dark:text-white mb-5 flex items-center gap-2.5 pb-4 border-b border-gray-100 dark:border-gray-800">
                    <i class="fas fa-info-circle text-blue-500"></i> รายละเอียดการแข่งขัน
                </h2>
                <div
                    class="prose prose-sm sm:prose-base dark:prose-invert prose-blue max-w-none text-gray-600 dark:text-gray-400 leading-relaxed">
                    {!! nl2br(e($competition->description)) !!}
                </div>
            </div>

            <div class="lg:col-span-5 bg-white dark:bg-[#141414] p-6 sm:p-8 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm flex flex-col"
                x-data="{
                    initMap() {
                        let lat = {{ $competition->latitude ?? 18.7883 }};
                        let lng = {{ $competition->longitude ?? 98.9853 }};
                        let map = L.map($refs.mapShow).setView([lat, lng], 15);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);
                        L.marker([lat, lng]).addTo(map).bindPopup('<b class=\'font-kanit\'>{{ addslashes($competition->location) }}</b>').openPopup();
                    }
                }" x-init="initMap()">
                <div class="flex items-center justify-between mb-5 pb-4 border-b border-gray-100 dark:border-gray-800">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center gap-2.5">
                        <i class="fas fa-map-marked-alt text-red-500"></i> สถานที่จัดงาน
                    </h2>
                    <a href="https://www.google.com/maps/search/?api=1&query={{ $competition->latitude }},{{ $competition->longitude }}"
                        target="_blank"
                        class="text-xs font-semibold text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/20 dark:text-blue-400 px-3 py-1.5 rounded-lg transition-colors border border-blue-100 dark:border-blue-800/50">
                        เปิดแผนที่ <i class="fas fa-external-link-alt ml-1"></i>
                    </a>
                </div>
                <div x-ref="mapShow"
                    class="w-full flex-1 min-h-[200px] rounded-xl border border-gray-200 dark:border-gray-700 z-10 relative">
                </div>
            </div>
        </div>

        {{-- CLASSES SHOWCASE & FILTERS --}}
        @php
            $uniqueGameTypes = $competition->classes->pluck('game_type_name')->filter()->unique()->values();
            $uniqueCategories = $competition->classes->pluck('allowed_categories')->flatten(1)->pluck('name')->filter()->unique()->values();

            // 🚀 อัปเกรด: ส่ง categories_details แบบเต็มๆ เข้าไปด้วย เพื่อให้ Alpine รู้ min_age, max_age
            $alpineClasses = $competition->classes->map(function($c) {
                return [
                    'id' => $c->id,
                    'name' => $c->name,
                    'searchString' => strtolower($c->name),
                    'game_type' => $c->game_type_name,
                    'max_members' => $c->max_members,
                    'entry_fee' => $c->entry_fee,
                    'categories' => collect($c->allowed_categories)->pluck('name')->toArray(),
                    'categories_details' => $c->allowed_categories // 🛡️ ส่งก้อน JSON เกณฑ์อายุไปให้ UI Shield
                ];
            });
        @endphp

        <div x-data="{ 
                searchClass: '',
                filterGameType: 'all',
                filterCategory: 'all',
                items: {{ Js::from($alpineClasses) }},
                
                isModalOpen: false,
                selectedClass: null,
                selectedTeamId: null,
                myTeams: {{ Js::from($myTeams ?? []) }},
                
                matchesFilter(index) {
                    let item = this.items[index];
                    let matchSearch = this.searchClass === '' || item.searchString.includes(this.searchClass.toLowerCase());
                    let matchGame = this.filterGameType === 'all' || item.game_type === this.filterGameType;
                    let matchCat = this.filterCategory === 'all' || item.categories.includes(this.filterCategory);
                    return matchSearch && matchGame && matchCat;
                },
                get visibleCount() {
                    return this.items.filter((_, i) => this.matchesFilter(i)).length;
                },

                openModal(index) {
                    this.selectedClass = this.items[index];
                    this.selectedTeamId = null;
                    this.isModalOpen = true;
                    document.body.style.overflow = 'hidden';
                },
                closeModal() {
                    this.isModalOpen = false;
                    setTimeout(() => { this.selectedClass = null; }, 300);
                    document.body.style.overflow = '';
                },
                
                // 🧠 THE SHIELD: ฟังก์ชันคำนวณอายุจากวันเกิดเทียบกับปัจจุบัน
                getAge(birthDateString) {
                    let birth = new Date(birthDateString);
                    let today = new Date();
                    let age = today.getFullYear() - birth.getFullYear();
                    let m = today.getMonth() - birth.getMonth();
                    if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) {
                        age--;
                    }
                    return age;
                },

                // 🛡️ THE SHIELD: เช็ค Error ทีละด่าน (ถ้ามี Error จะส่งข้อความกลับไปโชว์)
                getTeamError(team) {
                    if (!this.selectedClass) return '';

                    // ด่าน 1: เช็คโควตาจำนวนคน
                    if (this.selectedClass.max_members !== null && team.members.length > this.selectedClass.max_members) {
                        return 'จำนวนคนเกินโควตารุ่นนี้ (' + this.selectedClass.max_members + ' คน)';
                    }

                    // ด่าน 2: เช็คอายุลูกทีมทุกคน (ONE FAIL = ALL FAIL)
                    let allowedCats = this.selectedClass.categories_details || [];
                    if (allowedCats.length > 0) {
                        let minAllowed = Math.min(...allowedCats.map(c => c.min_age));
                        let maxAllowed = Math.max(...allowedCats.map(c => c.max_age));

                        for (let member of team.members) {
                            let age = this.getAge(member.birth_date);
                            // ถ้ามีเด็กแม้แต่คนเดียวอายุหลุดเกณฑ์
                            if (age < minAllowed || age > maxAllowed) {
                                return 'อายุ ' + member.first_name_th + ' (' + age + ' ปี) ไม่เข้าเกณฑ์';
                            }
                        }
                    }

                    return ''; // ถ้าว่างเปล่าแปลว่าผ่านทุกด่าน!
                },

                isTeamEligible(team) {
                    return this.getTeamError(team) === '';
                }
            }">

            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white flex items-center gap-3">
                        <i class="fas fa-layer-group text-blue-500"></i> รุ่นการแข่งขันที่เปิดรับสมัคร
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">พบ <span x-text="visibleCount"
                            class="font-semibold text-blue-600 dark:text-blue-400"></span> รุ่น จากทั้งหมด
                        {{ $competition->classes->count() }} รุ่น</p>
                </div>
            </div>

            <div
                class="bg-white/50 dark:bg-[#1a1a1a]/50 border border-gray-200 dark:border-gray-800 rounded-2xl p-3 mb-6 flex flex-col sm:flex-row gap-3 z-30 relative">
                <div class="relative flex-1 group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i
                            class="fas fa-search text-gray-400 group-focus-within:text-blue-500 transition-colors text-sm"></i>
                    </div>
                    <input type="text" x-model="searchClass" placeholder="ค้นหาชื่อรุ่น..."
                        class="w-full pl-10 pr-4 py-2.5 bg-white dark:bg-[#111] border border-gray-200 dark:border-gray-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 rounded-xl text-sm font-medium text-gray-700 dark:text-gray-200 placeholder-gray-400 outline-none transition-all shadow-sm">
                </div>

                <div x-data="{ open: false }" @click.outside="open = false" class="relative sm:w-48 shrink-0">
                    <button @click="open = !open" type="button"
                        class="w-full flex justify-between items-center px-4 py-2.5 bg-white dark:bg-[#111] border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-medium text-gray-700 dark:text-gray-200 shadow-sm focus:ring-2 focus:ring-blue-500/20">
                        <span class="truncate"
                            x-text="filterGameType === 'all' ? 'ทุกประเภทเกม' : filterGameType"></span>
                        <i class="fas fa-chevron-down text-[10px] text-gray-400 transition-transform"
                            :class="open && 'rotate-180'"></i>
                    </button>
                    <div x-show="open" style="display:none" x-transition.opacity
                        class="absolute z-[60] mt-1 w-full bg-white dark:bg-[#1a1a1a] shadow-lg rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden max-h-48 overflow-y-auto">
                        <div @click="filterGameType = 'all'; open = false"
                            class="px-4 py-2.5 text-sm cursor-pointer border-b border-gray-50 dark:border-gray-800"
                            :class="filterGameType === 'all' ?
                                'bg-blue-50 dark:bg-blue-900/20 text-blue-600 font-semibold' :
                                'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5'">
                            ทุกประเภทเกม</div>
                        @foreach ($uniqueGameTypes as $gt)
                            <div @click="filterGameType = '{{ $gt }}'; open = false"
                                class="px-4 py-2.5 text-sm cursor-pointer"
                                :class="filterGameType === '{{ $gt }}' ?
                                    'bg-blue-50 dark:bg-blue-900/20 text-blue-600 font-semibold' :
                                    'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5'">
                                {{ $gt }}</div>
                        @endforeach
                    </div>
                </div>

                <div x-data="{ open: false }" @click.outside="open = false" class="relative sm:w-48 shrink-0">
                    <button @click="open = !open" type="button"
                        class="w-full flex justify-between items-center px-4 py-2.5 bg-white dark:bg-[#111] border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-medium text-gray-700 dark:text-gray-200 shadow-sm focus:ring-2 focus:ring-blue-500/20">
                        <span class="truncate"
                            x-text="filterCategory === 'all' ? 'ทุกรุ่นอายุ' : filterCategory"></span>
                        <i class="fas fa-chevron-down text-[10px] text-gray-400 transition-transform"
                            :class="open && 'rotate-180'"></i>
                    </button>
                    <div x-show="open" style="display:none" x-transition.opacity
                        class="absolute z-[60] mt-1 w-full bg-white dark:bg-[#1a1a1a] shadow-lg rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden max-h-48 overflow-y-auto">
                        <div @click="filterCategory = 'all'; open = false"
                            class="px-4 py-2.5 text-sm cursor-pointer border-b border-gray-50 dark:border-gray-800"
                            :class="filterCategory === 'all' ?
                                'bg-blue-50 dark:bg-blue-900/20 text-blue-600 font-semibold' :
                                'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5'">
                            ทุกรุ่นอายุ</div>
                        @foreach ($uniqueCategories as $cat)
                            <div @click="filterCategory = '{{ $cat }}'; open = false"
                                class="px-4 py-2.5 text-sm cursor-pointer"
                                :class="filterCategory === '{{ $cat }}' ?
                                    'bg-blue-50 dark:bg-blue-900/20 text-blue-600 font-semibold' :
                                    'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5'">
                                {{ $cat }}</div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-5 z-0 relative">
                @forelse($competition->classes as $index => $class)
                    <div x-show="matchesFilter({{ $index }})" x-transition.opacity
                        class="bg-white dark:bg-[#141414] border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm hover:shadow-lg hover:border-blue-300 dark:hover:border-blue-800 transition-all duration-300 flex flex-col md:flex-row group overflow-hidden">

                        <div
                            class="w-full md:w-56 h-40 md:h-auto min-h-[10rem] bg-gray-50 dark:bg-[#0a0a0a] border-b md:border-b-0 md:border-r border-gray-100 dark:border-gray-800 overflow-hidden relative flex items-center justify-center shrink-0">
                            @if ($class->robot_image_url)
                                <img src="{{ route('user.competitions.classes.picture', [$competition->id, $class->id]) }}"
                                    alt="{{ $class->robot_name }}"
                                    class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <i class="fas fa-robot text-4xl text-gray-300 dark:text-gray-700 relative z-10"></i>
                            @endif

                            {{-- Badge --}}
                            <div
                                class="absolute top-3 left-3 z-10 bg-white/90 dark:bg-black/80 backdrop-blur-sm border border-gray-200 dark:border-gray-700 px-2.5 py-1 rounded-md shadow-sm">
                                <span
                                    class="text-[9px] font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400">{{ $class->game_type_name }}</span>
                            </div>
                        </div>

                        <div class="p-6 flex flex-col flex-1 relative min-w-0">
                            <h3
                                class="text-xl font-bold text-gray-900 dark:text-white leading-tight mb-4 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors line-clamp-2">
                                {{ $class->name }}
                            </h3>
                            <div class="flex flex-wrap gap-x-6 gap-y-3 text-sm mb-5">
                                <div class="flex items-center text-gray-600 dark:text-gray-400"><i
                                        class="fas fa-robot w-5 text-center mr-1 text-gray-400 dark:text-gray-600"></i><span
                                        class="truncate">{{ $class->robot_name }}</span></div>
                                <div class="flex items-center text-gray-600 dark:text-gray-400"><i
                                        class="fas fa-weight-hanging w-5 text-center mr-1 text-gray-400 dark:text-gray-600"></i><span>{{ $class->robot_weight ?? '-' }}
                                        Kg</span></div>
                                <div class="flex items-center text-gray-600 dark:text-gray-400"><i
                                        class="fas fa-users w-5 text-center mr-1 text-gray-400 dark:text-gray-600"></i><span>ไม่เกิน
                                        {{ $class->max_members }} คน/ทีม</span></div>
                                <div class="flex items-center font-semibold text-emerald-600 dark:text-emerald-500"><i
                                        class="fas fa-coins w-5 text-center mr-1 text-emerald-500/70"></i><span>{{ $class->entry_fee > 0 ? number_format($class->entry_fee) . ' ฿' : 'ฟรี' }}</span>
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-2 mt-auto">
                                @foreach ($class->allowed_categories as $cat)
                                    <span
                                        class="px-2.5 py-1 bg-gray-50 dark:bg-[#1a1a1a] border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 rounded-lg text-xs font-medium flex items-center">
                                        <i class="fas fa-user-graduate text-gray-400 mr-1.5"></i>
                                        {{ $cat['name'] }} <span
                                            class="opacity-70 ml-1">({{ $cat['min_age'] }}-{{ $cat['max_age'] }}
                                            ปี)</span>
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <div
                            class="w-full md:w-48 bg-gray-50 dark:bg-[#0a0a0a] border-t md:border-t-0 md:border-l border-gray-100 dark:border-gray-800 p-5 flex flex-row md:flex-col justify-center gap-3 shrink-0">
                            @if ($class->rules_url)
                                <a href="{{ route('user.competitions.classes.rule', [$competition->id, $class->id]) }}"
                                    target="_blank"
                                    class="flex-1 flex justify-center items-center gap-2 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-white hover:bg-gray-100 dark:bg-[#222] dark:hover:bg-[#333] rounded-xl transition-colors border border-gray-200 dark:border-gray-700 shadow-sm">
                                    <i class="fas fa-file-pdf text-red-500"></i> กติกา
                                </a>
                            @else
                                <button disabled
                                    class="flex-1 flex justify-center items-center gap-2 py-2.5 text-sm font-medium text-gray-400 bg-white dark:bg-[#111] rounded-xl cursor-not-allowed border border-gray-100 dark:border-gray-800">ไม่มีกติกา</button>
                            @endif

                            @if ($competition->status === 'registration')
                                <button @click="openModal({{ $index }})"
                                    class="flex-1 flex justify-center items-center gap-2 py-2.5 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-sm hover:shadow-md transition-all border border-transparent">
                                    สมัคร <i class="fas fa-arrow-right text-xs"></i>
                                </button>
                            @else
                                <button disabled
                                    class="flex-1 flex justify-center items-center gap-2 py-2.5 text-sm font-semibold text-gray-400 bg-gray-100 dark:bg-[#222] rounded-xl cursor-not-allowed"><i
                                        class="fas fa-lock"></i> ปิดรับ</button>
                            @endif
                        </div>
                    </div>
                @empty
                @endforelse

                <div x-show="visibleCount === 0 && {{ $competition->classes->count() }} > 0" style="display:none;"
                    class="py-12 text-center bg-white dark:bg-[#141414] border border-gray-200 dark:border-gray-800 rounded-2xl">
                    <p class="text-gray-500 dark:text-gray-400 mb-4">ไม่พบรุ่นการแข่งขันที่ตรงกับเงื่อนไข</p>
                    <button @click="searchClass = ''; filterGameType = 'all'; filterCategory = 'all'"
                        class="text-sm font-semibold text-blue-600 hover:text-blue-700 transition-colors">ล้างการค้นหา</button>
                </div>
            </div>

            {{-- REGISTRATION MODAL --}}
            <div x-show="isModalOpen" style="display: none;"
                class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 sm:p-6"
                x-transition.opacity>

                <div @click.outside="closeModal()"
                    class="bg-white dark:bg-[#141414] w-full max-w-2xl rounded-2xl shadow-2xl flex flex-col max-h-[90vh] sm:max-h-[85vh] overflow-hidden border border-gray-100 dark:border-gray-800"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-8"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0">

                    {{-- 1. Header --}}
                    <div class="shrink-0 border-b border-gray-100 dark:border-gray-800 p-5 sm:p-6 flex justify-between items-start bg-gray-50/50 dark:bg-[#1a1a1a]">
                        <div>
                            <div class="flex items-center gap-2 mb-1.5">
                                <span class="w-8 h-1 bg-blue-500 rounded-full"></span>
                                <span class="text-xs font-bold text-blue-500 uppercase tracking-wider">เลือกทีมของคุณ</span>
                            </div>
                            <h3 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white leading-tight"
                                x-text="selectedClass ? selectedClass.name : ''"></h3>
                        </div>
                        <button @click="closeModal()" type="button"
                            class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:bg-red-100 hover:text-red-500 transition-colors shrink-0">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    {{-- 2. Body (รายชื่อทีม) --}}
                    <div class="flex-1 overflow-y-auto custom-scrollbar p-5 sm:p-6">
                        <div x-show="myTeams.length > 0" class="space-y-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">คลิกเพื่อเลือกทีมที่จะใช้ในการแข่งขัน:</p>
                            
                            <template x-for="team in myTeams" :key="team.id">
                                <label
                                    :class="isTeamEligible(team) ?
                                        'cursor-pointer hover:border-blue-300 dark:hover:border-blue-700/50' :
                                        'opacity-60 cursor-not-allowed bg-gray-50 dark:bg-[#0a0a0a] grayscale'"
                                    class="relative flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 border-2 rounded-xl transition-all"
                                    :style="selectedTeamId == team.id ?
                                        'border-color: #3b82f6; background-color: rgba(59, 130, 246, 0.05);' :
                                        'border-color: var(--tw-border-gray-200);'">

                                    <div class="flex items-center gap-4 min-w-0">
                                        <div class="flex items-center justify-center w-5 h-5 rounded-full border-2 shrink-0 transition-colors"
                                            :class="selectedTeamId == team.id ? 'border-blue-500 bg-blue-500' : 'border-gray-300 dark:border-gray-600'">
                                            <i class="fas fa-check text-white text-[10px]" x-show="selectedTeamId == team.id"></i>
                                        </div>
                                        <div class="truncate">
                                            <h4 class="font-bold text-gray-900 dark:text-white text-lg truncate" x-text="team.name"></h4>
                                            <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400 mt-1 font-medium truncate">
                                                <span><i class="fas fa-users mr-1"></i>สมาชิก <span x-text="team.members.length"></span> คน</span>
                                                <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                                                <span class="truncate"><i class="fas fa-school mr-1"></i><span x-text="team.school_name"></span></span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    {{-- 🚀 THE SHIELD BADGE: ดึงข้อความแจ้งเตือนที่แม่นยำมาแสดง --}}
                                    <div x-show="!isTeamEligible(team)"
                                        class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 text-xs font-bold border border-red-100 dark:border-red-500/20 max-w-[220px]">
                                        <i class="fas fa-exclamation-triangle shrink-0"></i> 
                                        <span x-text="getTeamError(team)" class="truncate" :title="getTeamError(team)"></span>
                                    </div>

                                    <input type="radio" name="team_id" :value="team.id"
                                        x-model="selectedTeamId" class="hidden" :disabled="!isTeamEligible(team)">
                                </label>
                            </template>
                        </div>
                        
                        {{-- กรณีไม่มีทีม --}}
                        <div x-show="myTeams.length === 0" class="text-center py-10">
                            <div class="w-20 h-20 bg-gray-50 dark:bg-[#111] rounded-full flex items-center justify-center mx-auto mb-5 border border-gray-100 dark:border-gray-800">
                                <i class="fas fa-users-slash text-3xl text-gray-400 dark:text-gray-600"></i>
                            </div>
                            <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">คุณยังไม่มีทีมในระบบ</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6 max-w-sm mx-auto">กรุณาสร้างทีมและเพิ่มรายชื่อผู้เข้าแข่งขันให้เรียบร้อยก่อนทำการสมัคร</p>
                            <a href="{{ route('user.teams.index') }}" class="inline-flex items-center gap-2 bg-gray-900 dark:bg-white text-white dark:text-gray-900 hover:bg-blue-600 dark:hover:bg-blue-500 dark:hover:text-white px-6 py-3 rounded-xl font-semibold transition-all shadow-md">
                                <i class="fas fa-plus"></i> ไปหน้าจัดการทีม
                            </a>
                        </div>
                    </div>

                    {{-- 3. Footer (ปุ่มยืนยัน) --}}
                    <div class="shrink-0 border-t border-gray-100 dark:border-gray-800 p-5 sm:p-6 bg-white dark:bg-[#1a1a1a] flex flex-col-reverse sm:flex-row justify-end gap-3">
                        <button @click="closeModal()" type="button"
                            class="w-full sm:w-auto px-6 py-3 rounded-xl font-medium text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                            ยกเลิก
                        </button>
                        <form method="POST"
                            :action="selectedClass ? `{{ url('user/competitions') }}/${selectedClass.competition_id}/classes/${selectedClass.id}/register` : '#'"
                            class="w-full sm:w-auto">
                            @csrf
                            <input type="hidden" name="team_id" :value="selectedTeamId">
                            <button type="submit" :disabled="!selectedTeamId"
                                class="w-full sm:w-auto px-8 py-3 rounded-xl font-bold text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all flex items-center justify-center gap-2 shadow-md hover:shadow-blue-500/25">
                                ยืนยันการเลือกทีม <i class="fas fa-arrow-right"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </main>
</x-user-layout>
