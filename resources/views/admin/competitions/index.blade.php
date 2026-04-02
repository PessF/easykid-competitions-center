<x-admin-layout>
    <x-slot name="title">จัดการการแข่งขัน | Competitions</x-slot>

    <div x-data="{
        editComp: {
            id: '', name: '', location: '', description: '', status: '',
            regis_start_date: '', regis_end_date: '', event_start_date: '', event_end_date: '',
            latitude: '', longitude: ''
        },
        maps: {},
        markers: {},
        initMap(mapId, latInputId, lngInputId, initLat = 18.7883, initLng = 98.9853) {
            setTimeout(() => {
                let latInput = document.getElementById(latInputId);
                let lngInput = document.getElementById(lngInputId);
                let lat = latInput.value ? parseFloat(latInput.value) : initLat;
                let lng = lngInput.value ? parseFloat(lngInput.value) : initLng;
    
                if (this.maps[mapId]) {
                    this.maps[mapId].invalidateSize();
                    this.maps[mapId].setView([lat, lng], 13);
                    this.markers[mapId].setLatLng([lat, lng]);
                } else {
                    this.maps[mapId] = L.map(mapId).setView([lat, lng], 13);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '© OpenStreetMap'
                    }).addTo(this.maps[mapId]);
    
                    this.markers[mapId] = L.marker([lat, lng], { draggable: true }).addTo(this.maps[mapId]);
    
                    this.markers[mapId].on('dragend', (e) => {
                        let pos = e.target.getLatLng();
                        latInput.value = pos.lat.toFixed(8);
                        lngInput.value = pos.lng.toFixed(8);
                        latInput.dispatchEvent(new Event('change'));
                        lngInput.dispatchEvent(new Event('change'));
                    });
    
                    setTimeout(() => { this.maps[mapId].invalidateSize(); }, 100);
                }
            }, 500);
        }
    }">

        {{-- ===== HEADER ===== --}}
        <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-xl flex items-center justify-center shadow-sm shrink-0">
                    <i class="fas fa-trophy text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white tracking-tight">จัดการการแข่งขัน</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 font-normal">สร้างและบริหารจัดการรายการแข่งขันหลักทั้งหมดในระบบ</p>
                </div>
            </div>
            <button @click="$dispatch('open-modal', 'add-competition'); initMap('map-add', 'add_lat', 'add_lng')"
                class="group px-6 py-3 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-sm font-semibold rounded-xl transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                <i class="fas fa-plus-circle text-base transition-transform duration-200 group-hover:rotate-90"></i>
                สร้างงานแข่งใหม่
            </button>
        </div>

        {{-- ===== GRID CARDS ===== --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @forelse($competitions as $comp)
                <div class="bg-white dark:bg-[#1a1a1a] border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm hover:shadow-xl hover:border-blue-300 dark:hover:border-gray-600 transition-all duration-300 overflow-hidden flex flex-col group">

                    {{-- Image Banner & Status --}}
                    <div class="relative aspect-[21/9] sm:aspect-video bg-gray-100 dark:bg-[#111] overflow-hidden border-b border-gray-100 dark:border-gray-800 shrink-0">
                        @if ($comp->banner_url)
                            <img src="{{ asset('storage/' . $comp->banner_url) }}" alt="{{ $comp->name }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center text-gray-400 dark:text-gray-600">
                                <i class="fas fa-image text-4xl mb-2 opacity-30"></i>
                                <span class="text-xs font-semibold uppercase tracking-widest opacity-50">ไม่มีภาพแบนเนอร์</span>
                            </div>
                        @endif

                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent z-10"></div>

                        <div class="absolute top-4 right-4 z-20 flex flex-col items-end gap-2">
                            @php
                                $now = now();
                                $dbStatus = $comp->status; 
                                
                                $regisStart = $comp->regis_start_date ? \Carbon\Carbon::parse($comp->regis_start_date) : null;
                                $regisEnd = $comp->regis_end_date ? \Carbon\Carbon::parse($comp->regis_end_date) : null;
                                $eventStart = $comp->event_start_date ? \Carbon\Carbon::parse($comp->event_start_date) : null;
                                $eventEnd = $comp->event_end_date ? \Carbon\Carbon::parse($comp->event_end_date) : null;

                                $computedStatus = 'draft';

                                if ($dbStatus === 'draft') {
                                    $computedStatus = 'draft';
                                } elseif ($dbStatus === 'cancelled') {
                                    $computedStatus = 'cancelled';
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

                                $statusConfig = [
                                    'draft' => ['bg' => 'bg-gray-100/90 text-gray-700 backdrop-blur-sm', 'icon' => 'fa-file-alt', 'label' => 'ร่างงาน (ซ่อน)'],
                                    'published' => ['bg' => 'bg-indigo-500 text-white shadow-lg shadow-indigo-500/30', 'icon' => 'fa-globe', 'label' => 'เผยแพร่แล้ว'],
                                    'coming_soon' => ['bg' => 'bg-amber-500 text-white shadow-lg shadow-amber-500/30', 'icon' => 'fa-clock', 'label' => 'เร็วๆ นี้'],
                                    'open' => ['bg' => 'bg-green-500 text-white shadow-lg shadow-green-500/30 animate-pulse', 'icon' => 'fa-door-open', 'label' => 'เปิดรับสมัคร'],
                                    'registration_closed' => ['bg' => 'bg-orange-500 text-white shadow-lg shadow-orange-500/30', 'icon' => 'fa-lock', 'label' => 'ปิดรับสมัคร'],
                                    'ongoing' => ['bg' => 'bg-blue-500 text-white shadow-lg shadow-blue-500/30', 'icon' => 'fa-play-circle', 'label' => 'กำลังแข่ง'],
                                    'ended' => ['bg' => 'bg-gray-800 text-white border border-gray-600', 'icon' => 'fa-flag-checkered', 'label' => 'จบงานแล้ว'],
                                    'cancelled' => ['bg' => 'bg-red-600 text-white shadow-lg shadow-red-600/30', 'icon' => 'fa-ban', 'label' => 'ถูกยกเลิก'],
                                ];
                                
                                $config = $statusConfig[$computedStatus] ?? $statusConfig['draft'];
                                
                                // 🚀 คำนวณเวลาที่เหลือให้แม่นยำระดับนาที
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
                                    } elseif ($daysLeft == 0 && $diffHours == 0 && $diffMinutes > 0) {
                                        $timeLeftText = "เหลือ {$diffMinutes} นาที!";
                                        $showWarning = true;
                                        $isUrgent = true; // ต่ำกว่า 1 ชั่วโมงให้เป็นสถานะด่วน
                                    }
                                }
                            @endphp

                            <span class="{{ $config['bg'] }} text-[11px] font-semibold px-3 py-1.5 rounded-lg flex items-center gap-1.5 border border-white/20">
                                <i class="fas {{ $config['icon'] }}"></i> {{ $config['label'] }}
                            </span>

                            @if ($showWarning)
                                <span class="bg-black/70 backdrop-blur-md text-[10px] font-semibold px-2.5 py-1 rounded-lg border border-white/10 flex items-center gap-1.5 {{ $isUrgent ? 'text-red-400 animate-pulse border-red-500/50' : 'text-yellow-400' }}">
                                    <i class="fas {{ $isUrgent ? 'fa-exclamation-circle' : 'fa-clock' }}"></i> {{ $timeLeftText }}
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="p-6 flex-1 flex flex-col">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2 line-clamp-2 leading-snug" title="{{ $comp->name }}">
                            {{ $comp->name }}
                        </h3>

                        @if ($comp->location)
                            <a href="https://www.google.com/maps/search/?api=1&query={{ $comp->latitude }},{{ $comp->longitude }}" target="_blank"
                                class="inline-flex items-start text-sm text-gray-500 dark:text-gray-400 hover:text-blue-500 transition-colors mb-4 group/map">
                                <i class="fas fa-map-marker-alt text-red-500 mt-1 mr-2 w-4 text-center shrink-0 group-hover/map:animate-bounce"></i>
                                <span class="font-normal line-clamp-1 underline-offset-4 group-hover/map:underline">{{ $comp->location }}</span>
                            </a>
                        @else
                            <div class="mb-4"></div>
                        @endif

                        <div class="grid grid-cols-2 gap-3 mb-6 mt-auto">
                            <div class="bg-blue-50 dark:bg-blue-900/10 p-3 rounded-xl border border-blue-100 dark:border-blue-900/30">
                                <div class="flex items-center gap-1.5 mb-1 text-blue-600 dark:text-blue-400">
                                    <i class="far fa-calendar-plus text-xs"></i>
                                    <span class="text-[10px] font-semibold uppercase tracking-wide">ช่วงรับสมัคร</span>
                                </div>
                                <div class="text-xs font-medium text-gray-800 dark:text-gray-300">
                                    @if ($comp->regis_start_date && $comp->regis_end_date)
                                        <span class="text-gray-400 font-normal">เริ่ม</span> {{ \Carbon\Carbon::parse($comp->regis_start_date)->format('d/m/y H:i') }} น.<br>
                                        <span class="text-gray-400 font-normal">ถึง</span> {{ \Carbon\Carbon::parse($comp->regis_end_date)->format('d/m/y H:i') }} น.
                                    @else
                                        <span class="text-gray-400">ไม่ได้กำหนด</span>
                                    @endif
                                </div>
                            </div>

                            <div class="bg-purple-50 dark:bg-purple-900/10 p-3 rounded-xl border border-purple-100 dark:border-purple-900/30">
                                <div class="flex items-center gap-1.5 mb-1 text-purple-600 dark:text-purple-400">
                                    <i class="far fa-calendar-check text-xs"></i>
                                    <span class="text-[10px] font-semibold uppercase tracking-wide">วันแข่งจริง</span>
                                </div>
                                <div class="text-xs font-medium text-gray-800 dark:text-gray-300">
                                    @if ($comp->event_start_date && $comp->event_end_date)
                                        <span class="text-gray-400 font-normal">เริ่ม</span> {{ \Carbon\Carbon::parse($comp->event_start_date)->format('d/m/y') }}<br>
                                        <span class="text-gray-400 font-normal">ถึง</span> {{ \Carbon\Carbon::parse($comp->event_end_date)->format('d/m/y') }}
                                    @else
                                        <span class="text-gray-400">ไม่ได้กำหนด</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Footer Actions --}}
                        <div class="flex items-center justify-between gap-3 pt-4 border-t border-gray-100 dark:border-gray-800">
                            <a href="{{ route('admin.competitions.classes.index', $comp->id) }}"
                                class="flex-1 flex items-center justify-center px-4 py-2.5 bg-gray-100 dark:bg-gray-800 hover:bg-blue-600 dark:hover:bg-blue-600 hover:text-white text-gray-700 dark:text-gray-300 rounded-xl text-sm font-semibold transition-colors">
                                <i class="fas fa-list-ul mr-2"></i> จัดการคลาสแข่ง
                            </a>

                            <div class="flex items-center gap-2">
                                <button
                                    @click="editComp = { 
                                        id: '{{ $comp->id }}', 
                                        name: {{ Js::from($comp->name) }}, 
                                        status: '{{ $comp->status }}',
                                        location: {{ Js::from($comp->location) }}, 
                                        description: {{ Js::from($comp->description) }},
                                        latitude: '{{ $comp->latitude }}', longitude: '{{ $comp->longitude }}',
                                        regis_start_date: '{{ $comp->regis_start_date ? \Carbon\Carbon::parse($comp->regis_start_date)->format('Y-m-d\TH:i') : '' }}',
                                        regis_end_date: '{{ $comp->regis_end_date ? \Carbon\Carbon::parse($comp->regis_end_date)->format('Y-m-d\TH:i') : '' }}',
                                        event_start_date: '{{ $comp->event_start_date ? \Carbon\Carbon::parse($comp->event_start_date)->format('Y-m-d') : '' }}',
                                        event_end_date: '{{ $comp->event_end_date ? \Carbon\Carbon::parse($comp->event_end_date)->format('Y-m-d') : '' }}'
                                    }; $dispatch('open-modal', 'edit-competition'); initMap('map-edit', 'edit_lat', 'edit_lng', editComp.latitude, editComp.longitude)"
                                    class="w-10 h-10 flex items-center justify-center text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-xl transition-colors border border-transparent hover:border-blue-100 dark:hover:border-blue-800" title="แก้ไข">
                                    <i class="fas fa-pen text-sm"></i>
                                </button>
                                <button onclick="confirmDelete('{{ route('admin.competitions.destroy', $comp->id) }}', {{ Js::from($comp->name) }})"
                                    class="w-10 h-10 flex items-center justify-center text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl transition-colors border border-transparent hover:border-red-100 dark:hover:border-red-800" title="ลบ">
                                    <i class="fas fa-trash-alt text-sm"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-24 flex flex-col items-center justify-center bg-white dark:bg-[#1a1a1a] border-2 border-dashed border-gray-200 dark:border-gray-800 rounded-2xl">
                    <div class="w-20 h-20 bg-gray-50 dark:bg-[#111] rounded-full flex items-center justify-center mb-5">
                        <i class="fas fa-folder-open text-4xl text-gray-300 dark:text-gray-600"></i>
                    </div>
                    <p class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-1">ยังไม่มีรายการแข่งขัน</p>
                    <p class="text-sm font-normal text-gray-500 dark:text-gray-400 mb-6">สร้างรายการแรกของคุณเพื่อเริ่มต้นระบบ</p>
                    <button @click="$dispatch('open-modal', 'add-competition'); initMap('map-add', 'add_lat', 'add_lng')"
                        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-md transition-colors flex items-center gap-2">
                        <i class="fas fa-plus"></i> สร้างรายการแข่งขัน
                    </button>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-8 flex justify-center">
            {{ $competitions->links() }}
        </div>

        {{-- ===== MODAL: CREATE ===== --}}
        <x-modal name="add-competition" focusable maxWidth="3xl">
            <div class="bg-white dark:bg-[#1a1a1a] flex flex-col h-[90vh] overflow-hidden rounded-2xl">
                <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-800 shrink-0 flex items-center justify-between bg-white dark:bg-[#1a1a1a] z-20">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-xl flex items-center justify-center">
                            <i class="fas fa-plus-circle text-lg"></i>
                        </div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">สร้างงานแข่งขันใหม่</h2>
                    </div>
                    <button @click="$dispatch('close-modal', 'add-competition')" type="button" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors focus:outline-none">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.competitions.store') }}" enctype="multipart/form-data" class="flex flex-col flex-1 min-h-0">
                    @csrf
                    <div class="p-6 sm:p-8 overflow-y-auto custom-scrollbar flex-1 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Name --}}
                            <div class="md:col-span-2 space-y-2">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">ชื่อรายการแข่งขัน <span class="text-red-500">*</span></label>
                                <input type="text" name="name" required placeholder="เช่น Easykids Competitions 2026"
                                    class="w-full px-4 py-3 text-sm border-gray-200 dark:border-gray-700 dark:bg-black/20 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                            </div>

                            {{-- Location --}}
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">สถานที่จัดงาน</label>
                                <input type="text" name="location" placeholder="เช่น หอประชุมมหาวิทยาลัยเชียงใหม่"
                                    class="w-full px-4 py-3 text-sm border-gray-200 dark:border-gray-700 dark:bg-black/20 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                            </div>

                            {{-- Status --}}
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">สถานะงาน <span class="text-red-500">*</span></label>
                                <div x-data="{ open: false, selected: 'draft', options: { 'draft': 'ร่างงาน (ซ่อนจากหน้าเว็บ)', 'published': 'เผยแพร่ (ระบบเวลาทำงานอัตโนมัติ)', 'cancelled': 'ยกเลิกงานแข่ง' } }" class="relative" @click.outside="open = false">
                                    <button @click="open = !open" type="button" class="w-full px-4 py-3 bg-white dark:bg-black/20 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-left flex items-center justify-between focus:ring-2 focus:ring-blue-500/20 outline-none">
                                        <span x-text="options[selected]" class="text-gray-900 dark:text-white"></span>
                                        <i class="fas fa-chevron-down text-xs text-gray-400" :class="open && 'rotate-180'"></i>
                                    </button>
                                    <div x-show="open" style="display:none" class="absolute z-[60] w-full mt-1 bg-white dark:bg-[#222] shadow-xl rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                                        <template x-for="(label, val) in options" :key="val">
                                            <div @click="selected = val; open = false" class="px-4 py-3 text-sm cursor-pointer hover:bg-blue-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300" x-text="label"></div>
                                        </template>
                                    </div>
                                    <input type="hidden" name="status" x-model="selected">
                                </div>
                            </div>

                            {{-- Map Segment --}}
                            <div class="md:col-span-2 p-5 bg-gray-50 dark:bg-[#111] rounded-2xl border border-gray-200 dark:border-gray-800">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center">
                                        <i class="fas fa-map-marker-alt text-red-500 mr-2 text-lg"></i>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">ปักหมุดสถานที่จัดงาน (ลากหมุดสีน้ำเงิน)</label>
                                    </div>
                                    <div class="flex space-x-3 text-xs text-gray-500 bg-white dark:bg-[#1a1a1a] px-3 py-1.5 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                                        <span>Lat: <span id="add_lat_display" class="font-mono text-blue-600 dark:text-blue-400">18.7883</span></span>
                                        <span>Lng: <span id="add_lng_display" class="font-mono text-blue-600 dark:text-blue-400">98.9853</span></span>
                                    </div>
                                </div>
                                <div id="map-add" class="w-full h-64 rounded-xl border border-gray-200 dark:border-gray-700 relative" style="z-index: 1;"></div>
                                <input type="hidden" name="latitude" id="add_lat" onchange="document.getElementById('add_lat_display').innerText = this.value">
                                <input type="hidden" name="longitude" id="add_lng" onchange="document.getElementById('add_lng_display').innerText = this.value">
                            </div>

                            {{-- Description --}}
                            <div class="md:col-span-2 space-y-2">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">รายละเอียดงาน / คำอธิบาย</label>
                                <textarea name="description" rows="3" placeholder="อธิบายภาพรวมของงาน กติกาคร่าวๆ หรือลิงก์สำคัญ..."
                                    class="w-full px-4 py-3 text-sm border-gray-200 dark:border-gray-700 dark:bg-black/20 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none"></textarea>
                            </div>

                            {{-- Banner --}}
                            <div class="md:col-span-2 space-y-2">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">แบนเนอร์งาน (PNG/JPG)</label>
                                <input type="file" name="banner" accept="image/*"
                                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-50 dark:file:bg-blue-900/20 file:text-blue-600 dark:file:text-blue-400 hover:file:bg-blue-100 transition-colors cursor-pointer">
                            </div>

                            {{-- Registration Dates --}}
                            <div class="md:col-span-2 p-5 bg-blue-50/50 dark:bg-blue-900/10 rounded-2xl grid grid-cols-1 md:grid-cols-2 gap-5 border border-blue-100 dark:border-blue-900/30">
                                <div class="md:col-span-2 flex items-center border-b border-blue-100 dark:border-blue-900/30 pb-3">
                                    <i class="far fa-calendar-plus text-blue-500 mr-2 text-lg"></i>
                                    <span class="text-sm font-semibold text-blue-700 dark:text-blue-400">ช่วงเวลาเปิดรับสมัคร</span>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400">วันเริ่มรับสมัคร</label>
                                    <input type="datetime-local" name="regis_start_date"
                                        class="w-full px-4 py-2.5 text-sm border-gray-200 dark:border-gray-700 bg-white dark:bg-black/20 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500/20 outline-none [color-scheme:light] dark:[color-scheme:dark] cursor-pointer">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400">วันสิ้นสุดรับสมัคร</label>
                                    <input type="datetime-local" name="regis_end_date"
                                        class="w-full px-4 py-2.5 text-sm border-gray-200 dark:border-gray-700 bg-white dark:bg-black/20 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500/20 outline-none [color-scheme:light] dark:[color-scheme:dark] cursor-pointer">
                                </div>
                            </div>

                            {{-- Event Dates --}}
                            <div class="md:col-span-2 p-5 bg-purple-50/50 dark:bg-purple-900/10 rounded-2xl grid grid-cols-1 md:grid-cols-2 gap-5 border border-purple-100 dark:border-purple-900/30">
                                <div class="md:col-span-2 flex items-center border-b border-purple-100 dark:border-purple-900/30 pb-3">
                                    <i class="far fa-calendar-check text-purple-500 mr-2 text-lg"></i>
                                    <span class="text-sm font-semibold text-purple-700 dark:text-purple-400">วันจัดงานแข่งขันจริง</span>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400">วันเริ่มงาน</label>
                                    <input type="date" name="event_start_date"
                                        class="w-full px-4 py-2.5 text-sm border-gray-200 dark:border-gray-700 bg-white dark:bg-black/20 dark:text-white rounded-xl focus:ring-2 focus:ring-purple-500/20 outline-none [color-scheme:light] dark:[color-scheme:dark] cursor-pointer">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400">วันจบงาน</label>
                                    <input type="date" name="event_end_date"
                                        class="w-full px-4 py-2.5 text-sm border-gray-200 dark:border-gray-700 bg-white dark:bg-black/20 dark:text-white rounded-xl focus:ring-2 focus:ring-purple-500/20 outline-none [color-scheme:light] dark:[color-scheme:dark] cursor-pointer">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="px-6 py-4 bg-gray-50 dark:bg-[#111] border-t border-gray-200 dark:border-gray-800 shrink-0 flex justify-end gap-3 z-20">
                        <button type="button" @click="$dispatch('close-modal', 'add-competition')" class="px-6 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-semibold transition-colors focus:outline-none shadow-sm">ยกเลิก</button>
                        <button type="submit" class="px-8 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-semibold shadow-md flex items-center gap-2 transition-all">
                            <i class="fas fa-save"></i> สร้างงานแข่ง
                        </button>
                    </div>
                </form>
            </div>
        </x-modal>

        {{-- ===== MODAL: EDIT ===== --}}
        <x-modal name="edit-competition" focusable maxWidth="3xl">
            <div class="bg-white dark:bg-[#1a1a1a] flex flex-col h-[90vh] overflow-hidden rounded-2xl">
                <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-800 shrink-0 flex items-center justify-between bg-white dark:bg-[#1a1a1a] z-20">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-yellow-50 dark:bg-yellow-900/20 text-yellow-600 dark:text-yellow-400 rounded-xl flex items-center justify-center">
                            <i class="fas fa-pen text-lg"></i>
                        </div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">แก้ไขข้อมูลงานแข่งขัน</h2>
                    </div>
                    <button @click="$dispatch('close-modal', 'edit-competition')" type="button" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors focus:outline-none">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <form method="POST" :action="`/admin/competitions/${editComp.id}`" enctype="multipart/form-data" class="flex flex-col flex-1 min-h-0">
                    @csrf @method('PUT')
                    <div class="p-6 sm:p-8 overflow-y-auto custom-scrollbar flex-1 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Name --}}
                            <div class="md:col-span-2 space-y-2">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">ชื่อรายการแข่งขัน <span class="text-red-500">*</span></label>
                                <input type="text" name="name" x-model="editComp.name" required
                                    class="w-full px-4 py-3 text-sm border-gray-200 dark:border-gray-700 dark:bg-black/20 dark:text-white rounded-xl focus:ring-2 focus:ring-yellow-500/20 focus:border-yellow-500 transition-all outline-none">
                            </div>

                            {{-- Location --}}
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">สถานที่จัดงาน</label>
                                <input type="text" name="location" x-model="editComp.location"
                                    class="w-full px-4 py-3 text-sm border-gray-200 dark:border-gray-700 dark:bg-black/20 dark:text-white rounded-xl focus:ring-2 focus:ring-yellow-500/20 focus:border-yellow-500 transition-all outline-none">
                            </div>

                            {{-- Status --}}
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">สถานะงาน <span class="text-red-500">*</span></label>
                                <div x-data="{ open: false, options: { 'draft': 'ร่างงาน (ซ่อนจากหน้าเว็บ)', 'published': 'เผยแพร่ (ระบบเวลาทำงานอัตโนมัติ)', 'cancelled': 'ยกเลิกงานแข่ง' } }" class="relative" @click.outside="open = false">
                                    <button @click="open = !open" type="button" class="w-full px-4 py-3 bg-white dark:bg-black/20 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-left flex items-center justify-between focus:ring-2 focus:ring-yellow-500/20 outline-none">
                                        <span x-text="options[editComp.status] ? options[editComp.status] : (editComp.status === 'registration' || editComp.status === 'ongoing' || editComp.status === 'completed' ? 'เผยแพร่ (ระบบเวลาทำงานอัตโนมัติ)' : editComp.status)" class="text-gray-900 dark:text-white"></span>
                                        <i class="fas fa-chevron-down text-xs text-gray-400" :class="open && 'rotate-180'"></i>
                                    </button>
                                    <div x-show="open" style="display:none" class="absolute z-[60] w-full mt-1 bg-white dark:bg-[#222] shadow-xl rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                                        <template x-for="(label, val) in options" :key="val">
                                            <div @click="editComp.status = val; open = false" class="px-4 py-3 text-sm cursor-pointer hover:bg-yellow-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300" x-text="label"></div>
                                        </template>
                                    </div>
                                    <input type="hidden" name="status" :value="(editComp.status === 'registration' || editComp.status === 'ongoing' || editComp.status === 'completed') ? 'published' : editComp.status">
                                </div>
                            </div>

                            {{-- Map Segment --}}
                            <div class="md:col-span-2 p-5 bg-gray-50 dark:bg-[#111] rounded-2xl border border-gray-200 dark:border-gray-800">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center">
                                        <i class="fas fa-map-marker-alt text-red-500 mr-2 text-lg"></i>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">ปักหมุดสถานที่จัดงาน</label>
                                    </div>
                                    <div class="flex space-x-3 text-xs text-gray-500 bg-white dark:bg-[#1a1a1a] px-3 py-1.5 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                                        <span>Lat: <span id="edit_lat_display" class="font-mono text-yellow-600 dark:text-yellow-500" x-text="editComp.latitude || '18.7883'"></span></span>
                                        <span>Lng: <span id="edit_lng_display" class="font-mono text-yellow-600 dark:text-yellow-500" x-text="editComp.longitude || '98.9853'"></span></span>
                                    </div>
                                </div>
                                <div id="map-edit" class="w-full h-64 rounded-xl border border-gray-200 dark:border-gray-700 relative" style="z-index: 1;"></div>
                                <input type="hidden" name="latitude" id="edit_lat" x-model="editComp.latitude" onchange="document.getElementById('edit_lat_display').innerText = this.value">
                                <input type="hidden" name="longitude" id="edit_lng" x-model="editComp.longitude" onchange="document.getElementById('edit_lng_display').innerText = this.value">
                            </div>

                            {{-- Description --}}
                            <div class="md:col-span-2 space-y-2">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">รายละเอียดงาน / คำอธิบาย</label>
                                <textarea name="description" x-model="editComp.description" rows="3" class="w-full px-4 py-3 text-sm border-gray-200 dark:border-gray-700 dark:bg-black/20 dark:text-white rounded-xl focus:ring-2 focus:ring-yellow-500/20 focus:border-yellow-500 transition-all outline-none"></textarea>
                            </div>

                            {{-- Banner --}}
                            <div class="md:col-span-2 space-y-2">
                                <label class="text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center justify-between">
                                    <span>เปลี่ยนแบนเนอร์ใหม่ <span class="text-gray-400 font-normal ml-1">(ข้ามหากไม่ต้องการเปลี่ยน)</span></span>
                                </label>
                                <input type="file" name="banner" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-yellow-50 dark:file:bg-yellow-900/20 file:text-yellow-600 dark:file:text-yellow-500 hover:file:bg-yellow-100 transition-colors cursor-pointer">
                            </div>

                            {{-- Registration Dates --}}
                            <div class="md:col-span-2 p-5 bg-yellow-50/50 dark:bg-yellow-900/10 rounded-2xl grid grid-cols-1 md:grid-cols-2 gap-5 border border-yellow-100 dark:border-yellow-900/30">
                                <div class="md:col-span-2 flex items-center border-b border-yellow-100 dark:border-yellow-900/30 pb-3">
                                    <i class="far fa-calendar-plus text-yellow-500 mr-2 text-lg"></i>
                                    <span class="text-sm font-semibold text-yellow-700 dark:text-yellow-400">ช่วงเวลาเปิดรับสมัคร</span>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400">วันเริ่มรับสมัคร</label>
                                    <input type="datetime-local" name="regis_start_date" x-model="editComp.regis_start_date" class="w-full px-4 py-2.5 text-sm border-gray-200 dark:border-gray-700 bg-white dark:bg-black/20 dark:text-white rounded-xl focus:ring-2 focus:ring-yellow-500/20 outline-none [color-scheme:light] dark:[color-scheme:dark] cursor-pointer">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400">วันสิ้นสุดรับสมัคร</label>
                                    <input type="datetime-local" name="regis_end_date" x-model="editComp.regis_end_date" class="w-full px-4 py-2.5 text-sm border-gray-200 dark:border-gray-700 bg-white dark:bg-black/20 dark:text-white rounded-xl focus:ring-2 focus:ring-yellow-500/20 outline-none [color-scheme:light] dark:[color-scheme:dark] cursor-pointer">
                                </div>
                            </div>

                            {{-- Event Dates --}}
                            <div class="md:col-span-2 p-5 bg-purple-50/50 dark:bg-purple-900/10 rounded-2xl grid grid-cols-1 md:grid-cols-2 gap-5 border border-purple-100 dark:border-purple-900/30">
                                <div class="md:col-span-2 flex items-center border-b border-purple-100 dark:border-purple-900/30 pb-3">
                                    <i class="far fa-calendar-check text-purple-500 mr-2 text-lg"></i>
                                    <span class="text-sm font-semibold text-purple-700 dark:text-purple-400">วันจัดงานแข่งขันจริง</span>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400">วันเริ่มงาน</label>
                                    <input type="date" name="event_start_date" x-model="editComp.event_start_date" class="w-full px-4 py-2.5 text-sm border-gray-200 dark:border-gray-700 bg-white dark:bg-black/20 dark:text-white rounded-xl focus:ring-2 focus:ring-purple-500/20 outline-none [color-scheme:light] dark:[color-scheme:dark] cursor-pointer">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400">วันจบงาน</label>
                                    <input type="date" name="event_end_date" x-model="editComp.event_end_date" class="w-full px-4 py-2.5 text-sm border-gray-200 dark:border-gray-700 bg-white dark:bg-black/20 dark:text-white rounded-xl focus:ring-2 focus:ring-purple-500/20 outline-none [color-scheme:light] dark:[color-scheme:dark] cursor-pointer">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="px-6 py-4 bg-gray-50 dark:bg-[#111] border-t border-gray-200 dark:border-gray-800 shrink-0 flex justify-end gap-3 z-20">
                        <button type="button" @click="$dispatch('close-modal', 'edit-competition')" class="px-6 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-semibold transition-colors focus:outline-none shadow-sm">ยกเลิก</button>
                        <button type="submit" class="px-8 py-2.5 bg-yellow-500 hover:bg-yellow-600 text-white rounded-xl text-sm font-semibold shadow-md flex items-center gap-2 transition-all">
                            <i class="fas fa-save"></i> บันทึกการแก้ไข
                        </button>
                    </div>
                </form>
            </div>
        </x-modal>
    </div>
</x-admin-layout>