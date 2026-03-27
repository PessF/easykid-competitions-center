<x-user-layout>
    <x-slot name="title">ค้นหางานแข่งขัน | Easykids</x-slot>

    <div x-data="{
        search: '',
        filterStatus: 'all',
        items: {{ Js::from($competitions) }}
    }" class="font-kanit pb-16">

        {{-- ===== HERO BANNER ===== --}}
        <div class="relative w-full rounded-2xl overflow-hidden bg-[#080808] mb-6"
             style="height: clamp(16rem, 40vw, 26rem);">
            <video autoplay loop muted playsinline
                   class="absolute inset-0 w-full h-full object-cover z-0 pointer-events-none opacity-50">
                <source src="{{ asset('videos/ek-videohero.mp4') }}" type="video/mp4">
            </video>
            <div class="absolute inset-0 bg-gradient-to-r from-black/85 via-black/40 to-transparent z-10"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent z-10"></div>
            <div class="absolute bottom-0 left-0 right-0 h-20 bg-gradient-to-t from-white dark:from-[#0a0a0a] to-transparent z-20"></div>

            <div class="relative z-20 h-full flex flex-col justify-end px-8 sm:px-14 pb-10">
                <div class="flex items-center gap-2.5 mb-3">
                    <div class="w-6 h-0.5 bg-blue-400 rounded-full"></div>
                    <span class="text-blue-400 text-[11px] font-bold uppercase tracking-[0.2em]">Easykids Competition</span>
                </div>
                <h1 class="text-3xl sm:text-5xl font-semibold text-white leading-tight tracking-tight mb-2">
                    พร้อมที่จะท้าทายหรือยัง?
                </h1>
                <p class="text-gray-300/80 text-sm sm:text-base font-normal">
                    สร้างทีมของคุณ แล้วพาน้องๆ ไปสู่ชัยชนะได้เลย
                </p>
            </div>
        </div>

        {{-- ===== SEARCH & FILTER ===== --}}
        <div class="sticky top-16 z-40 mb-7">
            <div class="bg-white/95 dark:bg-[#111]/95 backdrop-blur-xl border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm flex items-center gap-3 px-5" style="height: 52px;">
                <i class="fas fa-search text-gray-300 dark:text-gray-600 text-sm shrink-0"></i>
                <input type="text" x-model="search"
                       placeholder="ค้นหาชื่อการแข่งขัน, สถานที่..."
                       class="flex-1 h-full bg-transparent border-none focus:ring-0 outline-none text-sm text-gray-700 dark:text-gray-200 placeholder-gray-300 dark:placeholder-gray-600 font-normal">
                <button x-show="search !== ''" @click="search = ''" x-cloak
                        type="button"
                        class="w-6 h-6 flex items-center justify-center text-gray-300 hover:text-gray-500 dark:hover:text-gray-400 transition-colors focus:outline-none shrink-0">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
        </div>

        {{-- ===== COMPETITION LIST ===== --}}
        <div class="space-y-4">
            @foreach ($competitions as $comp)
                @php
                    $totalMaxTeams = $comp->classes->sum('max_teams');
                    $isUnlimited   = $comp->classes->contains(fn($c) => is_null($c->max_teams));
                    $currentTeams  = $comp->registrations_count;
                    $percent       = (!$isUnlimited && $totalMaxTeams > 0) ? ($currentTeams / $totalMaxTeams) * 100 : 0;
                    $isFull        = !$isUnlimited && $percent >= 100;
                    $isAlmostFull  = !$isUnlimited && $percent >= 80;
                    $regisEndDate  = \Carbon\Carbon::parse($comp->regis_end_date);
                    $daysLeft      = (int) now()->diffInDays($regisEndDate, false);
                @endphp

                <div x-show="(search === '' || '{{ strtolower($comp->name) }}'.includes(search.toLowerCase())) && (filterStatus === 'all' || '{{ $comp->status }}' === filterStatus)"
                     class="group bg-white dark:bg-[#141414] border border-gray-100 dark:border-gray-800 rounded-2xl overflow-hidden hover:border-blue-200 dark:hover:border-blue-900/50 hover:shadow-lg hover:shadow-blue-500/5 transition-all duration-300 flex flex-col lg:flex-row">

                    {{-- IMAGE --}}
                    <div class="lg:w-96 xl:w-[26rem] h-52 lg:h-auto relative overflow-hidden shrink-0">
                        <img src="{{ route('user.competitions.banner', $comp->id) }}"
                             alt="{{ $comp->name }}"
                             class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent"></div>

                        {{-- Status badge --}}
                        <div class="absolute top-4 left-4">
                            @if ($comp->status === 'registration')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-500 text-white text-[10px] font-bold uppercase rounded-lg tracking-wide shadow-lg">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse shrink-0"></span>
                                    เปิดรับสมัคร
                                </span>
                            @elseif ($comp->status === 'ongoing')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-600 text-white text-[10px] font-bold uppercase rounded-lg tracking-wide shadow-lg">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse shrink-0"></span>
                                    กำลังแข่งขัน
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 bg-black/50 text-white text-[10px] font-bold uppercase rounded-lg backdrop-blur-sm">
                                    {{ $comp->status }}
                                </span>
                            @endif
                        </div>

                        {{-- Days left --}}
                        @if ($comp->status === 'registration' && $daysLeft >= 0 && $daysLeft <= 14)
                            <div class="absolute top-4 right-4 bg-black/60 backdrop-blur-sm border border-white/10 px-2.5 py-1 rounded-lg">
                                <span class="text-[10px] font-bold text-amber-400">เหลือ {{ $daysLeft }} วัน</span>
                            </div>
                        @endif

                        {{-- Date chip --}}
                        <div class="absolute bottom-4 left-4">
                            <div class="bg-white dark:bg-[#141414] rounded-xl px-3 py-2 text-center shadow-lg min-w-[3rem]">
                                <span class="block text-xl font-bold leading-none text-gray-900 dark:text-white">
                                    {{ \Carbon\Carbon::parse($comp->event_start_date)->format('d') }}
                                </span>
                                <span class="block text-[9px] font-bold uppercase text-blue-500 tracking-widest mt-0.5">
                                    {{ \Carbon\Carbon::parse($comp->event_start_date)->translatedFormat('M') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- CONTENT --}}
                    <div class="flex-1 flex flex-col p-6 sm:p-7 min-w-0">

                        {{-- Location --}}
                        <div class="flex items-center gap-1.5 mb-2.5">
                            <i class="fas fa-map-marker-alt text-red-400 text-[10px] shrink-0"></i>
                            <span class="text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide truncate">{{ $comp->location }}</span>
                        </div>

                        {{-- Title --}}
                        <h3 class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white leading-snug group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors line-clamp-2 mb-4">
                            {{ $comp->name }}
                        </h3>

                        {{-- Timeline grid --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                            <div class="flex items-start gap-3 p-3.5 rounded-xl bg-emerald-50/60 dark:bg-emerald-500/5 border border-emerald-100 dark:border-emerald-500/10">
                                <div class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-500/10 flex items-center justify-center shrink-0 mt-0.5">
                                    <i class="fas fa-pencil-alt text-emerald-600 dark:text-emerald-400 text-[10px]"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-500 mb-1">รับสมัคร</p>
                                    <p class="text-xs font-medium text-gray-700 dark:text-gray-300 leading-snug">
                                        {{ \Carbon\Carbon::parse($comp->regis_start_date)->translatedFormat('d M y') }}
                                        <span class="text-gray-400 mx-0.5">—</span>
                                        <span class="text-red-500 dark:text-red-400">{{ \Carbon\Carbon::parse($comp->regis_end_date)->translatedFormat('d M y') }}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 p-3.5 rounded-xl bg-blue-50/60 dark:bg-blue-500/5 border border-blue-100 dark:border-blue-500/10">
                                <div class="w-7 h-7 rounded-lg bg-blue-100 dark:bg-blue-500/10 flex items-center justify-center shrink-0 mt-0.5">
                                    <i class="fas fa-flag-checkered text-blue-600 dark:text-blue-400 text-[10px]"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-blue-600 dark:text-blue-500 mb-1">วันแข่งขัน</p>
                                    <p class="text-xs font-medium text-gray-700 dark:text-gray-300 leading-snug">
                                        {{ \Carbon\Carbon::parse($comp->event_start_date)->translatedFormat('d M y') }}
                                        @if($comp->event_start_date !== $comp->event_end_date)
                                            <span class="text-gray-400 mx-0.5">—</span>
                                            {{ \Carbon\Carbon::parse($comp->event_end_date)->translatedFormat('d M y') }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Description --}}
                        <p class="text-sm text-gray-400 dark:text-gray-500 font-normal line-clamp-2 leading-relaxed flex-1 mb-5">
                            {{ $comp->description }}
                        </p>

                        {{-- FOOTER --}}
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-5 border-t border-gray-50 dark:border-gray-800/70">

                            {{-- Capacity --}}
                            <div class="flex items-center gap-3 flex-1 min-w-0 max-w-xs">
                                <div class="shrink-0">
                                    <div class="w-8 h-8 rounded-lg bg-gray-50 dark:bg-white/5 flex items-center justify-center">
                                        <i class="fas fa-users text-gray-400 dark:text-gray-500 text-xs"></i>
                                    </div>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-baseline gap-1.5 mb-1.5">
                                        <span class="text-[11px] text-gray-400 dark:text-gray-500 font-medium whitespace-nowrap">ความจุทีม</span>
                                        <span class="text-xs font-bold
                                            {{ $isFull ? 'text-red-500' : ($isAlmostFull ? 'text-amber-500' : 'text-gray-700 dark:text-gray-300') }} whitespace-nowrap">
                                            @if($isUnlimited)
                                                ไม่จำกัด
                                            @elseif($isFull)
                                                เต็มแล้ว!
                                            @else
                                                {{ $currentTeams }}/{{ $totalMaxTeams }} ทีม
                                            @endif
                                        </span>
                                    </div>
                                    @if(!$isUnlimited)
                                        <div class="h-1 w-full bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                                            <div class="h-full rounded-full transition-all duration-700
                                                 {{ $isFull ? 'bg-red-500' : ($isAlmostFull ? 'bg-amber-400' : 'bg-blue-500') }}"
                                                 style="width: {{ min($percent, 100) }}%"></div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- CTA --}}
                            <a href="{{ route('user.competitions.show', $comp->id) }}"
                               class="group/btn shrink-0 inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 focus:outline-none
                                      bg-gray-900 dark:bg-white text-white dark:text-gray-900
                                      hover:bg-blue-600 dark:hover:bg-blue-500 hover:text-white dark:hover:text-white
                                      shadow-sm hover:shadow-md hover:shadow-blue-500/20">
                                ดูรายละเอียด & สมัคร
                                <i class="fas fa-arrow-right text-xs transition-transform duration-200 group-hover/btn:translate-x-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ===== EMPTY STATE ===== --}}
        <div x-show="items.filter(i =>
                i.name.toLowerCase().includes(search.toLowerCase()) &&
                (filterStatus === 'all' || i.status === filterStatus)
             ).length === 0"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-98"
             x-transition:enter-end="opacity-100 scale-100"
             style="display:none"
             class="mt-4 py-20 text-center bg-white dark:bg-[#141414] border border-gray-100 dark:border-gray-800 rounded-2xl">
            <div class="w-14 h-14 bg-gray-50 dark:bg-white/5 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-search text-xl text-gray-200 dark:text-gray-700"></i>
            </div>
            <h3 class="text-base font-semibold text-gray-600 dark:text-gray-300 mb-1.5">ไม่พบการแข่งขันที่ค้นหา</h3>
            <p class="text-sm text-gray-400 dark:text-gray-500 mb-5">ลองเปลี่ยนคำค้นหา หรือเลือกสถานะใหม่</p>
            <button @click="search = ''; filterStatus = 'all'"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 hover:bg-blue-600 hover:text-white dark:hover:bg-blue-600 dark:hover:text-white text-sm font-semibold rounded-xl transition-all focus:outline-none">
                <i class="fas fa-redo text-xs"></i>
                ล้างการค้นหา
            </button>
        </div>

    </div>
</x-user-layout>