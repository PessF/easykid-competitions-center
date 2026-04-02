<x-user-layout>
    <x-slot name="title">ค้นหางานแข่งขัน | Easykids</x-slot>

    {{-- 🚀 รวมศูนย์ประมวลผล: คิดคำนวณทุกอย่างให้เสร็จตั้งแต่ฝั่ง Server เพื่อป้องกันบั๊ก UI --}}
    @php
        $processedComps = [];

        $statusConfig = [
            'draft' => [
                'bg' => 'bg-gray-100/90 text-gray-700 backdrop-blur-sm',
                'icon' => 'fa-file-alt',
                'label' => 'ร่างงาน (ซ่อน)',
            ],
            'published' => [
                'bg' => 'bg-indigo-500 text-white shadow-lg shadow-indigo-500/30',
                'icon' => 'fa-globe',
                'label' => 'เผยแพร่แล้ว',
            ],
            'coming_soon' => [
                'bg' => 'bg-amber-500 text-white shadow-lg shadow-amber-500/30',
                'icon' => 'fa-clock',
                'label' => 'เร็วๆ นี้',
            ],
            'open' => [
                'bg' => 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/30 animate-pulse',
                'icon' => 'fa-door-open',
                'label' => 'เปิดรับสมัคร',
            ],
            'registration_closed' => [
                'bg' => 'bg-red-500 text-white shadow-lg shadow-red-500/30',
                'icon' => 'fa-lock',
                'label' => 'ปิดรับสมัคร',
            ],
            'ongoing' => [
                'bg' => 'bg-blue-600 text-white shadow-lg shadow-blue-600/30',
                'icon' => 'fa-play-circle',
                'label' => 'กำลังแข่งขัน',
            ],
            'ended' => [
                'bg' => 'bg-gray-800 text-white border border-gray-600',
                'icon' => 'fa-flag-checkered',
                'label' => 'จบการแข่งขัน',
            ],
            'cancelled' => [
                'bg' => 'bg-red-600 text-white shadow-lg shadow-red-600/30',
                'icon' => 'fa-ban',
                'label' => 'ยกเลิกงานแข่ง',
            ],
        ];

        foreach ($competitions as $comp) {
            $now = now();
            $dbStatus = $comp->status;

            $regisStart = $comp->regis_start_date ? \Carbon\Carbon::parse($comp->regis_start_date) : null;
            $regisEnd = $comp->regis_end_date ? \Carbon\Carbon::parse($comp->regis_end_date) : null;
            $eventStart = $comp->event_start_date ? \Carbon\Carbon::parse($comp->event_start_date) : null;
            $eventEnd = $comp->event_end_date ? \Carbon\Carbon::parse($comp->event_end_date) : null;

            // 1. คำนวณสถานะ
            $computedStatus = 'draft';
            if ($dbStatus === 'draft' || $dbStatus === 'cancelled') {
                $computedStatus = $dbStatus;
            } elseif ($dbStatus === 'published') {
                if ($regisStart && $now->lt($regisStart)) {
                    $computedStatus = 'coming_soon';
                } elseif ($regisStart && $regisEnd && $now->between($regisStart, $regisEnd)) {
                    $computedStatus = 'open';
                } elseif ($regisEnd && $now->gt($regisEnd) && (!$eventStart || $now->lt($eventStart))) {
                    $computedStatus = 'registration_closed';
                } elseif ($eventStart && $eventEnd && $now->between($eventStart, $eventEnd)) {
                    $computedStatus = 'ongoing';
                } elseif ($eventEnd && $now->gt($eventEnd)) {
                    $computedStatus = 'ended';
                } else {
                    $computedStatus = 'published';
                }
            }

            $comp->dynamic_status = $computedStatus;
            $comp->ui_config = $statusConfig[$computedStatus] ?? $statusConfig['draft'];

            // 2. คำนวณเวลาถอยหลัง (Badge)
            $isRegisOpen = $computedStatus === 'open' && $regisEnd;
            $timeLeftText = '';
            $showWarning = false;
            $isUrgent = false;

            if ($isRegisOpen) {
                $diffMinutes = $now->diffInMinutes($regisEnd);
                $diffHours = floor($diffMinutes / 60);
                $daysLeft = floor($diffHours / 24);

                if ($daysLeft > 0 && $daysLeft <= 14) {
                    $timeLeftText = "เหลือ {$daysLeft} วัน";
                    $showWarning = true;
                } elseif ($daysLeft == 0 && $diffHours > 0) {
                    $timeLeftText = "เหลือ {$diffHours} ชม.";
                    $showWarning = true;
                } elseif ($daysLeft == 0 && $diffHours == 0 && $diffMinutes >= 0) {
                    $m = $diffMinutes == 0 ? 1 : $diffMinutes; // กันเหลือ 0 นาที
                    $timeLeftText = "เหลือ {$m} นาที!";
                    $showWarning = true;
                    $isUrgent = true;
                }
            }

            $comp->time_badge = (object) ['show' => $showWarning, 'text' => $timeLeftText, 'urgent' => $isUrgent];

            // 3. แพ็กข้อมูลส่งให้ JavaScript
            $processedComps[] = [
                'id' => $comp->id,
                'name' => mb_strtolower($comp->name),
                'status' => $computedStatus,
            ];
        }
    @endphp

    {{-- 🚀 แก้บั๊ก JS พังด้วยฟังก์ชัน isVisible() และ visibleCount() --}}
    <div x-data="{
        search: '',
        filterStatus: 'all',
        items: {{ Js::from($processedComps) }},
        isVisible(id) {
            let item = this.items.find(i => i.id === id);
            if (!item) return false;
            let matchSearch = this.search === '' || item.name.includes(this.search.toLowerCase().trim());
            let matchStatus = this.filterStatus === 'all' || item.status === this.filterStatus;
            return matchSearch && matchStatus;
        },
        get visibleCount() {
            return this.items.filter(i => {
                let matchSearch = this.search === '' || i.name.includes(this.search.toLowerCase().trim());
                let matchStatus = this.filterStatus === 'all' || i.status === this.filterStatus;
                return matchSearch && matchStatus;
            }).length;
        }
    }" class="font-kanit pb-16">

        {{-- ===== HERO BANNER ===== --}}
        <div class="relative w-full rounded-2xl overflow-hidden bg-[#080808] mb-6"
            style="height: clamp(14rem, 36vw, 24rem);">
            <video autoplay loop muted playsinline
                class="absolute inset-0 w-full h-full object-cover z-0 pointer-events-none opacity-50">
                <source src="{{ asset('videos/ek-videohero.mp4') }}" type="video/mp4">
            </video>
            <div class="absolute inset-0 bg-gradient-to-r from-black/85 via-black/40 to-transparent z-10"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent z-10"></div>
            <div
                class="absolute bottom-0 left-0 right-0 h-16 bg-gradient-to-t from-white dark:from-[#0a0a0a] to-transparent z-20">
            </div>

            <div class="relative z-20 h-full flex flex-col justify-end px-8 sm:px-12 pb-9">
                <div class="flex items-center gap-2.5 mb-3">
                    <div class="w-5 h-px bg-blue-400"></div>
                    <span class="text-blue-400 text-[10px] font-bold uppercase tracking-[0.2em]">Easykids
                        Competition</span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-semibold text-white leading-tight tracking-tight mb-2">
                    พร้อมที่จะท้าทายหรือยัง?</h1>
                <p class="text-gray-300/70 text-sm font-normal">สร้างทีมของคุณ แล้วพาน้องๆ ไปสู่ชัยชนะได้เลย</p>
            </div>
        </div>

        {{-- ===== SEARCH BAR & FILTER ===== --}}
        <div class="sticky top-16 z-40 mb-6">
            <div class="bg-white/95 dark:bg-[#111]/95 backdrop-blur-xl border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm flex items-center gap-3 px-5"
                style="height: 52px;">

                {{-- Text Search --}}
                <i class="fas fa-search text-gray-300 dark:text-gray-600 text-sm shrink-0"></i>
                <input type="text" x-model="search" placeholder="ค้นหาชื่อการแข่งขัน..."
                    class="min-w-0 flex-1 h-full bg-transparent border-none focus:ring-0 outline-none text-sm text-gray-700 dark:text-gray-200 placeholder-gray-300 dark:placeholder-gray-600 font-normal">

                <button x-show="search !== ''" @click="search = ''" x-cloak type="button"
                    class="w-6 h-6 flex items-center justify-center text-gray-300 hover:text-gray-500 dark:hover:text-gray-400 transition-colors focus:outline-none shrink-0 mr-1">
                    <i class="fas fa-times text-xs"></i>
                </button>

                {{-- Divider --}}
                <div class="h-6 w-px bg-gray-200 dark:bg-gray-800 shrink-0"></div>

                <div x-data="{
                    open: false,
                    options: {
                        'all': 'ทุกสถานะ',
                        'open': 'เปิดรับสมัคร',
                        'coming_soon': 'เร็วๆ นี้',
                        'registration_closed': 'ปิดรับสมัคร',
                        'ongoing': 'กำลังแข่งขัน',
                        'ended': 'จบการแข่งขัน'
                    }
                }" class="relative shrink-0 flex items-center h-full"
                    @click.outside="open = false">

                    {{-- ปุ่มกดแสดงสถานะปัจจุบัน --}}
                    <button @click="open = !open" type="button"
                        class="h-full flex items-center gap-2 pl-3 pr-2 bg-transparent border-none focus:ring-0 outline-none text-sm text-gray-600 dark:text-gray-300 font-medium transition-colors hover:text-blue-600 dark:hover:text-blue-400">
                        <span x-text="options[filterStatus]"></span>
                        <i class="fas fa-chevron-down text-[10px] text-gray-400 transition-transform duration-200"
                            :class="open ? 'rotate-180' : ''"></i>
                    </button>

                    {{-- กล่อง Dropdown ที่กางออกมา --}}
                    <div x-show="open" x-transition.opacity.duration.200ms style="display: none;"
                        class="absolute top-full right-0 mt-3 w-44 bg-white dark:bg-[#1a1a1a] border border-gray-100 dark:border-gray-800 rounded-xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.1)] dark:shadow-none overflow-hidden z-[60] py-1.5 font-kanit">

                        <template x-for="(label, val) in options" :key="val">
                            <button @click="filterStatus = val; open = false" type="button"
                                class="w-full text-left px-4 py-2.5 text-sm transition-colors flex items-center justify-between group"
                                :class="filterStatus === val ?
                                    'bg-blue-50/80 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 font-semibold' :
                                    'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5'">

                                <span x-text="label"
                                    class="group-hover:translate-x-1 transition-transform duration-200"></span>

                                {{-- โชว์ไอคอนติ๊กถูก เฉพาะอันที่ถูกเลือกอยู่ --}}
                                <i x-show="filterStatus === val" class="fas fa-check text-[10px]"></i>
                            </button>
                        </template>

                    </div>
                </div>
            </div>
        </div>

        {{-- ===== COMPETITION LIST ===== --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @forelse ($competitions as $comp)
                <div x-show="isVisible({{ $comp->id }})"
                    class="group bg-white dark:bg-[#141414] border border-gray-100 dark:border-gray-800 rounded-2xl overflow-hidden hover:border-blue-200 dark:hover:border-blue-900/50 hover:shadow-lg hover:shadow-blue-500/5 transition-all duration-300 flex flex-col">

                    {{-- ── IMAGE ── --}}
                    <div class="aspect-[21/9] relative overflow-hidden shrink-0 bg-gray-100 dark:bg-[#111]">

                        @if ($comp->banner_url)
                            <img src="{{ asset('storage/' . $comp->banner_url) }}" alt="{{ $comp->name }}"
                                class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out">
                        @else
                            <div
                                class="absolute inset-0 w-full h-full flex flex-col items-center justify-center text-gray-400 dark:text-gray-600">
                                <i class="fas fa-image text-4xl mb-2 opacity-30"></i>
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent"></div>

                        {{-- Status badge --}}
                        <div class="absolute top-4 left-4">
                            <span
                                class="{{ $comp->ui_config['bg'] }} inline-flex items-center gap-1.5 px-2.5 py-1 text-white text-[10px] font-bold uppercase rounded-lg tracking-wide shadow-lg">
                                @if ($comp->dynamic_status === 'open' || $comp->dynamic_status === 'ongoing')
                                    <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse shrink-0"></span>
                                @else
                                    <i class="fas {{ $comp->ui_config['icon'] }} shrink-0"></i>
                                @endif
                                {{ $comp->ui_config['label'] }}
                            </span>
                        </div>

                        {{-- Urgency badge --}}
                        @if ($comp->time_badge->show)
                            <div
                                class="absolute top-4 right-4 flex items-center gap-1 bg-black/55 backdrop-blur-sm border border-white/10 px-2.5 py-1 rounded-lg {{ $comp->time_badge->urgent ? 'border-red-500/50 animate-pulse' : '' }}">
                                <i
                                    class="fas {{ $comp->time_badge->urgent ? 'fa-exclamation-circle text-red-400' : 'fa-clock text-amber-400' }} text-[9px]"></i>
                                <span
                                    class="text-[10px] font-bold {{ $comp->time_badge->urgent ? 'text-red-400' : 'text-amber-400' }}">{{ $comp->time_badge->text }}</span>
                            </div>
                        @endif

                        {{-- 🚀 FIX: วันที่หลอน (ดักจับกรณีไม่ได้กำหนดวันที่แข่ง) --}}
                        <div class="absolute bottom-4 left-4">
                            <div
                                class="bg-white dark:bg-[#141414] rounded-xl px-3 py-2 text-center shadow-lg min-w-[3rem]">
                                @if ($comp->event_start_date)
                                    <span class="block text-xl font-bold leading-none text-gray-900 dark:text-white">
                                        {{ \Carbon\Carbon::parse($comp->event_start_date)->format('d') }}
                                    </span>
                                    <span
                                        class="block text-[9px] font-bold uppercase text-blue-500 tracking-widest mt-0.5">
                                        {{ \Carbon\Carbon::parse($comp->event_start_date)->translatedFormat('M') }}
                                    </span>
                                @else
                                    <span class="block text-lg font-bold leading-none text-gray-400">TBA</span>
                                    <span
                                        class="block text-[9px] font-bold uppercase text-gray-500 tracking-widest mt-0.5">รอประกาศ</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- ── CONTENT ── --}}
                    <div class="flex-1 flex flex-col p-6 min-w-0">

                        {{-- Location --}}
                        <div class="flex items-center gap-1.5 mb-2">
                            <i class="fas fa-map-marker-alt text-red-400 text-[10px] shrink-0"></i>
                            <span
                                class="text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide truncate">{{ $comp->location ?: 'รอประกาศสถานที่' }}</span>
                        </div>

                        {{-- Title --}}
                        <h3
                            class="text-xl font-semibold text-gray-900 dark:text-white leading-snug group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors line-clamp-2 mb-4">
                            {{ $comp->name }}
                        </h3>

                        {{-- 🚀 FIX: ไทม์ไลน์กันพัง กรณีไม่ได้ลงวันที่ไว้ --}}
                        <div class="flex flex-col gap-2.5 mb-4 mt-auto">
                            <div
                                class="flex items-center gap-2.5 w-full px-3.5 py-2 rounded-xl bg-emerald-50/70 dark:bg-emerald-500/5 border border-emerald-100 dark:border-emerald-500/10">
                                <i
                                    class="fas fa-pencil-alt text-emerald-600 dark:text-emerald-400 text-[10px] w-4 text-center shrink-0"></i>
                                <div class="min-w-0 flex items-center gap-2">
                                    <p
                                        class="text-[10px] font-bold text-emerald-600 dark:text-emerald-500 uppercase tracking-wider shrink-0">
                                        รับสมัคร</p>
                                    <div class="h-3 w-px bg-emerald-200 dark:bg-emerald-500/20"></div>
                                    <p class="text-xs font-medium text-gray-600 dark:text-gray-300 truncate">
                                        @if ($comp->regis_start_date && $comp->regis_end_date)
                                            {{ \Carbon\Carbon::parse($comp->regis_start_date)->translatedFormat('d M y') }}
                                            <span class="text-gray-300 dark:text-gray-600 mx-0.5">—</span> <span
                                                class="text-red-500 dark:text-red-400">{{ \Carbon\Carbon::parse($comp->regis_end_date)->translatedFormat('d M y') }}</span>
                                        @else
                                            <span class="text-gray-400 font-normal">รอประกาศกำหนดการ</span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <div
                                class="flex items-center gap-2.5 w-full px-3.5 py-2 rounded-xl bg-blue-50/70 dark:bg-blue-500/5 border border-blue-100 dark:border-blue-500/10">
                                <i
                                    class="fas fa-flag-checkered text-blue-600 dark:text-blue-400 text-[10px] w-4 text-center shrink-0"></i>
                                <div class="min-w-0 flex items-center gap-2">
                                    <p
                                        class="text-[10px] font-bold text-blue-600 dark:text-blue-500 uppercase tracking-wider shrink-0">
                                        แข่งขัน</p>
                                    <div class="h-3 w-px bg-blue-200 dark:bg-blue-500/20"></div>
                                    <p class="text-xs font-medium text-gray-600 dark:text-gray-300 truncate">
                                        @if ($comp->event_start_date)
                                            {{ \Carbon\Carbon::parse($comp->event_start_date)->translatedFormat('d M y') }}
                                            @if ($comp->event_start_date !== $comp->event_end_date && $comp->event_end_date)
                                                <span class="text-gray-300 dark:text-gray-600 mx-0.5">—</span>
                                                {{ \Carbon\Carbon::parse($comp->event_end_date)->translatedFormat('d M y') }}
                                            @endif
                                        @else
                                            <span class="text-gray-400 font-normal">รอประกาศกำหนดการ</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- ── FOOTER ── --}}
                        <div class="pt-4 border-t border-gray-50 dark:border-gray-800/70 mt-2">
                            @if (in_array($comp->dynamic_status, ['ended', 'cancelled']))
                                <div
                                    class="flex items-center justify-center gap-2 text-sm text-gray-500 dark:text-gray-400 font-normal bg-gray-50 dark:bg-white/5 px-4 py-2.5 rounded-xl border border-gray-100 dark:border-gray-800 w-full text-center">
                                    <i class="fas fa-info-circle text-gray-400 shrink-0"></i>
                                    <span>หากต้องการทราบรายละเอียด กรุณาติดต่อศูนย์ใหญ่</span>
                                </div>
                            @else
                                <a href="{{ route('user.competitions.show', $comp->id) }}"
                                    class="group/btn w-full inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 focus:outline-none
                                        @if (in_array($comp->dynamic_status, ['open', 'coming_soon'])) bg-gray-900 dark:bg-white text-white dark:text-gray-900 hover:bg-blue-600 dark:hover:bg-blue-500 shadow-sm hover:shadow-md hover:shadow-blue-500/20
                                        @else
                                            bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 @endif">

                                    @if (in_array($comp->dynamic_status, ['open', 'coming_soon']))
                                        ดูรายละเอียด & สมัคร
                                        <i
                                            class="fas fa-arrow-right text-xs transition-transform duration-200 group-hover/btn:translate-x-1"></i>
                                    @else
                                        ดูรายละเอียดงานแข่ง
                                    @endif
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                {{-- 🚀 ดัก Empty State ของ Database --}}
                <div
                    class="col-span-full py-24 flex flex-col items-center justify-center bg-white dark:bg-[#141414] border border-gray-100 dark:border-gray-800 rounded-2xl">
                    <div
                        class="w-16 h-16 bg-gray-50 dark:bg-[#111] rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-folder-open text-3xl text-gray-300 dark:text-gray-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-600 dark:text-gray-300 mb-1">
                        ยังไม่มีรายการแข่งขันเปิดรับสมัคร</h3>
                    <p class="text-sm font-normal text-gray-400 dark:text-gray-500">รอติดตามการประกาศเร็วๆ นี้นะครับ!
                    </p>
                </div>
            @endforelse
        </div>

        {{-- 🚀 ดัก Empty State ของ ตัวกรอง Filter ให้ไม่ซ้อนกัน --}}
        @if ($competitions->isNotEmpty())
            <div x-show="visibleCount === 0" x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="display:none"
                class="mt-4 py-20 text-center bg-white dark:bg-[#141414] border border-gray-100 dark:border-gray-800 rounded-2xl">
                <div
                    class="w-14 h-14 bg-gray-50 dark:bg-white/5 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-search text-xl text-gray-200 dark:text-gray-700"></i>
                </div>
                <h3 class="text-base font-semibold text-gray-600 dark:text-gray-300 mb-1">
                    ไม่พบการแข่งขันที่ตรงกับตัวกรอง</h3>
                <p class="text-sm text-gray-400 dark:text-gray-500 mb-5">ลองเปลี่ยนคำค้นหา หรือเลือกสถานะอื่นดูนะครับ
                </p>
                <button @click="search = ''; filterStatus = 'all'"
                    class="inline-flex items-center gap-2 px-5 py-2 bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 hover:bg-blue-600 hover:text-white dark:hover:bg-blue-600 dark:hover:text-white text-sm font-semibold rounded-xl transition-all focus:outline-none">
                    <i class="fas fa-redo text-xs"></i> ล้างตัวกรองทั้งหมด
                </button>
            </div>
        @endif

        {{-- ===== PAGINATION ===== --}}
        <div class="mt-8 flex justify-center">
            {{ $competitions->links() }}
        </div>

    </div>
</x-user-layout>
