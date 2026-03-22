<x-admin-layout>
    <x-slot name="title">จัดการการแข่งขัน | Competitions</x-slot>

    <div x-data="{
        editComp: {
            id: '',
            name: '',
            location: '',
            description: '',
            status: '',
            regis_start: '',
            regis_end: '',
            event_start: '',
            event_end: '',
            latitude: '',
            longitude: ''
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
                        attribution: '&copy; OpenStreetMap'
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

        <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center">
                <div
                    class="p-3 bg-white dark:bg-[#0f0f0f] border border-gray-100 dark:border-white/5 rounded-xl shadow-sm mr-4 text-blue-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">จัดการการแข่งขัน</h1>
                    <p class="text-sm text-gray-500 mt-1 font-normal">สร้างและบริหารจัดการรายการแข่งขันหลักทั้งหมดในระบบ
                    </p>
                </div>
            </div>
            <button @click="$dispatch('open-modal', 'add-competition'); initMap('map-add', 'add_lat', 'add_lng')"
                class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold uppercase tracking-wider rounded-xl transition-all shadow-lg shadow-blue-500/20 flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                สร้างงานแข่งใหม่
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @forelse($competitions as $comp)
                <div
                    class="bg-white dark:bg-[#0f0f0f] border border-gray-100 dark:border-white/5 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col group">

                    <div
                        class="relative aspect-video bg-gray-100 dark:bg-white/5 overflow-hidden border-b border-gray-100 dark:border-white/5">
                        @if ($comp->banner_url)
                            <img src="{{ route('admin.competitions.banner', $comp->id) }}" alt="{{ $comp->name }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center text-gray-400">
                                <svg class="w-12 h-12 mb-2 opacity-20" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span
                                    class="text-xs font-semibold uppercase tracking-widest opacity-50">ไม่มีภาพแบนเนอร์</span>
                            </div>
                        @endif

                        <div class="absolute top-4 right-4">
                            @php
                                $statusColors = [
                                    'draft' => 'bg-gray-500',
                                    'registration' => 'bg-blue-500 animate-pulse',
                                    'ongoing' => 'bg-green-500',
                                    'completed' => 'bg-red-500',
                                ];
                                $statusLabels = [
                                    'draft' => 'ร่างงาน',
                                    'registration' => 'เปิดรับสมัคร',
                                    'ongoing' => 'กำลังแข่ง',
                                    'completed' => 'จบงานแล้ว',
                                ];
                            @endphp
                            <span
                                class="{{ $statusColors[$comp->status] }} text-white text-[10px] font-semibold uppercase px-2.5 py-1 rounded-lg shadow-lg">
                                {{ $statusLabels[$comp->status] }}
                            </span>
                        </div>
                    </div>

                    <div class="p-6 flex-1 flex flex-col">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 line-clamp-1">
                            {{ $comp->name }}</h3>

                        @if ($comp->location)
                            <div class="flex items-start text-sm text-gray-500 dark:text-gray-400 mb-4">
                                <svg class="w-4 h-4 mr-2 mt-0.5 text-red-500 shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="font-normal line-clamp-1">{{ $comp->location }}</span>
                            </div>
                        @else
                            <div class="mb-4"></div>
                        @endif

                        <div class="space-y-3 mb-6">
                            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                <svg class="w-4 h-4 mr-3 text-blue-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <div class="flex flex-col">
                                    <span
                                        class="text-[10px] uppercase font-semibold text-gray-400 tracking-wider">รับสมัคร</span>
                                    <span
                                        class="font-normal">{{ optional($comp->regis_start_date)->format('d/m/Y H:i') ?? 'N/A' }}
                                        - {{ optional($comp->regis_end_date)->format('d/m/Y H:i') ?? 'N/A' }}</span>
                                </div>
                            </div>
                            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                <svg class="w-4 h-4 mr-3 text-purple-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <div class="flex flex-col">
                                    <span
                                        class="text-[10px] uppercase font-semibold text-gray-400 tracking-wider">วันแข่งจริง</span>
                                    <span
                                        class="font-normal">{{ optional($comp->event_start_date)->format('d/m/Y') ?? 'N/A' }}
                                        - {{ optional($comp->event_end_date)->format('d/m/Y') ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-auto grid grid-cols-2 gap-3 pt-4 border-t border-gray-100 dark:border-white/5">
                            <a href="{{ route('admin.competitions.classes.index', $comp->id) }}"
                                class="flex items-center justify-center px-4 py-2 bg-gray-100 dark:bg-white/5 hover:bg-blue-600 dark:hover:bg-blue-600 hover:text-white text-gray-600 dark:text-gray-300 rounded-xl text-xs font-semibold transition-all">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                </svg>
                                จัดการรายการย่อย
                            </a>
                            <div class="flex items-center justify-end space-x-2">
                                <button
                                    @click="editComp = { 
                                    id: '{{ $comp->id }}', name: '{{ addslashes($comp->name) }}', status: '{{ $comp->status }}',
                                    location: '{{ addslashes($comp->location) }}', description: '{{ addslashes(str_replace(["\r", "\n"], '', $comp->description)) }}',
                                    latitude: '{{ $comp->latitude }}', longitude: '{{ $comp->longitude }}',
                                    regis_start: '{{ $comp->regis_start_date ? $comp->regis_start_date->format('Y-m-d\TH:i') : '' }}',
                                    regis_end: '{{ $comp->regis_end_date ? $comp->regis_end_date->format('Y-m-d\TH:i') : '' }}',
                                    event_start: '{{ $comp->event_start_date ? $comp->event_start_date->format('Y-m-d') : '' }}',
                                    event_end: '{{ $comp->event_end_date ? $comp->event_end_date->format('Y-m-d') : '' }}'
                                }; $dispatch('open-modal', 'edit-competition'); initMap('map-edit', 'edit_lat', 'edit_lng', editComp.latitude, editComp.longitude)"
                                    class="p-2 text-yellow-500 hover:bg-yellow-50 dark:hover:bg-yellow-500/10 rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button
                                    onclick="confirmDelete('{{ route('admin.competitions.destroy', $comp->id) }}', '{{ addslashes($comp->name) }}')"
                                    class="p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div
                    class="col-span-full py-20 flex flex-col items-center justify-center bg-white dark:bg-[#0f0f0f] border border-gray-100 dark:border-white/5 rounded-2xl border-dashed">
                    <div class="p-4 bg-gray-50 dark:bg-white/5 rounded-full mb-4">
                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <p class="text-gray-500 dark:text-gray-400 font-normal">ยังไม่มีรายการแข่งขัน</p>
                    <button
                        @click="$dispatch('open-modal', 'add-competition'); initMap('map-add', 'add_lat', 'add_lng')"
                        class="mt-4 text-blue-500 hover:underline text-sm font-semibold tracking-widest uppercase">สร้างรายการแรกที่นี่</button>
                </div>
            @endforelse
        </div>

        {{-- Modal: เพิ่มงานแข่ง --}}
        <x-modal name="add-competition" focusable maxWidth="3xl">
            <div class="bg-white dark:bg-[#1a1a1a] flex flex-col max-h-[90vh]">

                <div
                    class="px-6 py-4 sm:px-8 sm:py-5 border-b border-gray-100 dark:border-white/5 shrink-0 bg-white dark:bg-[#1a1a1a] rounded-t-lg">
                    <h2 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white">สร้างงานแข่งขันใหม่</h2>
                </div>

                <div class="p-6 sm:p-8 overflow-y-auto">
                    <form method="POST" action="{{ route('admin.competitions.store') }}"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label
                                    class="block text-xs font-semibold uppercase tracking-widest text-gray-400 mb-2">ชื่อรายการแข่งขัน
                                    *</label>
                                <input type="text" name="name" required
                                    placeholder="เช่น Easykids Competitions 2026"
                                    class="w-full border-gray-100 dark:border-white/5 dark:bg-[#0f0f0f] dark:text-white rounded-xl focus:ring-blue-500 font-normal">
                            </div>

                            <div>
                                <label
                                    class="block text-xs font-semibold uppercase tracking-widest text-gray-400 mb-2">สถานที่จัดงาน</label>
                                <input type="text" name="location" placeholder="เช่น หอประชุมมหาวิทยาลัยเชียงใหม่"
                                    class="w-full border-gray-100 dark:border-white/5 dark:bg-[#0f0f0f] dark:text-white rounded-xl focus:ring-blue-500 font-normal">
                            </div>

                            <div>
                                <label
                                    class="block text-xs font-semibold uppercase tracking-widest text-gray-400 mb-2">สถานะงาน
                                    *</label>
                                <select name="status" required
                                    class="w-full border-gray-100 dark:border-white/5 dark:bg-[#0f0f0f] dark:text-white rounded-xl focus:ring-blue-500 font-normal">
                                    <option value="draft">ร่างงาน (Draft)</option>
                                    <option value="registration">เปิดรับสมัคร (Registration)</option>
                                    <option value="ongoing">กำลังแข่งขัน (Ongoing)</option>
                                    <option value="completed">จบการแข่งขัน (Completed)</option>
                                </select>
                            </div>

                            <div
                                class="md:col-span-2 p-4 bg-gray-50 dark:bg-white/5 rounded-2xl border border-gray-100 dark:border-white/5">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1.5 text-red-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <label
                                            class="block text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">
                                            ปักหมุดสถานที่จัดงาน (ลากหมุดสีน้ำเงิน)
                                        </label>
                                    </div>
                                    <div class="flex space-x-2 text-[10px] text-gray-400">
                                        <span>Lat: <span id="add_lat_display"
                                                class="font-mono text-blue-500">18.7883</span></span>
                                        <span>Lng: <span id="add_lng_display"
                                                class="font-mono text-blue-500">98.9853</span></span>
                                    </div>
                                </div>
                                <div id="map-add"
                                    class="w-full h-64 rounded-xl border border-gray-200 dark:border-gray-700"
                                    style="z-index: 1;"></div>

                                <input type="hidden" name="latitude" id="add_lat"
                                    onchange="document.getElementById('add_lat_display').innerText = this.value">
                                <input type="hidden" name="longitude" id="add_lng"
                                    onchange="document.getElementById('add_lng_display').innerText = this.value">
                            </div>

                            <div class="md:col-span-2">
                                <label
                                    class="block text-xs font-semibold uppercase tracking-widest text-gray-400 mb-2">รายละเอียดงาน
                                    / คำอธิบาย</label>
                                <textarea name="description" rows="3" placeholder="อธิบายภาพรวมของงาน กติกาคร่าวๆ หรือลิงก์สำคัญ..."
                                    class="w-full border-gray-100 dark:border-white/5 dark:bg-[#0f0f0f] dark:text-white rounded-xl focus:ring-blue-500 font-normal"></textarea>
                            </div>

                            <div class="md:col-span-2">
                                <label
                                    class="block text-xs font-semibold uppercase tracking-widest text-gray-400 mb-2">แบนเนอร์งาน
                                    (PNG/JPG)</label>
                                <input type="file" name="banner" accept="image/*"
                                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-blue-50 dark:file:bg-blue-900/20 dark:file:text-blue-400 font-normal">
                            </div>

                            <div
                                class="p-4 bg-blue-50 dark:bg-blue-500/5 rounded-2xl md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4 border border-blue-100 dark:border-blue-500/10">
                                <div class="md:col-span-2 flex items-center mb-2">
                                    <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span
                                        class="text-sm font-semibold text-blue-700 dark:text-blue-400">ช่วงเวลาเปิดรับสมัคร</span>
                                </div>
                                <div>
                                    <label
                                        class="block text-[10px] font-semibold uppercase text-blue-600/50 mb-1">วันเริ่มรับสมัคร</label>
                                    <input type="datetime-local" name="regis_start_date"
                                        class="w-full border-none bg-white dark:bg-black/20 dark:text-white rounded-lg focus:ring-blue-500 text-sm font-normal">
                                </div>
                                <div>
                                    <label
                                        class="block text-[10px] font-semibold uppercase text-blue-600/50 mb-1">วันสิ้นสุดรับสมัคร</label>
                                    <input type="datetime-local" name="regis_end_date"
                                        class="w-full border-none bg-white dark:bg-black/20 dark:text-white rounded-lg focus:ring-blue-500 text-sm font-normal">
                                </div>
                            </div>

                            <div
                                class="p-4 bg-purple-50 dark:bg-purple-500/5 rounded-2xl md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4 border border-purple-100 dark:border-purple-500/10">
                                <div class="md:col-span-2 flex items-center mb-2">
                                    <svg class="w-4 h-4 mr-2 text-purple-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span
                                        class="text-sm font-semibold text-purple-700 dark:text-purple-400">วันจัดงานแข่งขันจริง</span>
                                </div>
                                <div>
                                    <label
                                        class="block text-[10px] font-semibold uppercase text-purple-600/50 mb-1">วันเริ่มงาน</label>
                                    <input type="date" name="event_start_date"
                                        class="w-full border-none bg-white dark:bg-black/20 dark:text-white rounded-lg focus:ring-purple-500 text-sm font-normal">
                                </div>
                                <div>
                                    <label
                                        class="block text-[10px] font-semibold uppercase text-purple-600/50 mb-1">วันจบงาน</label>
                                    <input type="date" name="event_end_date"
                                        class="w-full border-none bg-white dark:bg-black/20 dark:text-white rounded-lg focus:ring-purple-500 text-sm font-normal">
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end space-x-3 pt-4 border-t border-gray-100 dark:border-white/5">
                            <button type="button" x-on:click="$dispatch('close')"
                                class="px-6 py-2.5 bg-gray-100 dark:bg-gray-800 text-gray-500 rounded-xl text-xs font-semibold uppercase tracking-widest transition-all">ยกเลิก</button>
                            <button type="submit"
                                class="px-6 py-2.5 bg-blue-600 text-white rounded-xl text-xs font-semibold uppercase tracking-widest shadow-lg shadow-blue-500/20 transition-all">สร้างงานแข่ง</button>
                        </div>
                    </form>
                </div>
            </div>
        </x-modal>

        {{-- Modal: แก้ไขงานแข่ง --}}
        <x-modal name="edit-competition" focusable maxWidth="3xl">
            <div class="bg-white dark:bg-[#1a1a1a] flex flex-col max-h-[90vh]">

                <div
                    class="px-6 py-4 sm:px-8 sm:py-5 border-b border-gray-100 dark:border-white/5 shrink-0 bg-white dark:bg-[#1a1a1a] rounded-t-lg">
                    <h2 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white">แก้ไขข้อมูลงานแข่งขัน
                    </h2>
                </div>

                <div class="p-6 sm:p-8 overflow-y-auto">
                    <form method="POST" :action="`{{ url('admin/competitions') }}/${editComp.id}`"
                        enctype="multipart/form-data">
                        @csrf @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label
                                    class="block text-xs font-semibold uppercase tracking-widest text-gray-400 mb-2">ชื่อรายการแข่งขัน
                                    *</label>
                                <input type="text" name="name" x-model="editComp.name" required
                                    class="w-full border-gray-100 dark:border-white/5 dark:bg-[#0f0f0f] dark:text-white rounded-xl focus:ring-yellow-500 font-normal">
                            </div>

                            <div>
                                <label
                                    class="block text-xs font-semibold uppercase tracking-widest text-gray-400 mb-2">สถานที่จัดงาน</label>
                                <input type="text" name="location" x-model="editComp.location"
                                    class="w-full border-gray-100 dark:border-white/5 dark:bg-[#0f0f0f] dark:text-white rounded-xl focus:ring-yellow-500 font-normal">
                            </div>

                            <div>
                                <label
                                    class="block text-xs font-semibold uppercase tracking-widest text-gray-400 mb-2">สถานะงาน
                                    *</label>
                                <select name="status" x-model="editComp.status" required
                                    class="w-full border-gray-100 dark:border-white/5 dark:bg-[#0f0f0f] dark:text-white rounded-xl focus:ring-yellow-500 font-normal">
                                    <option value="draft">ร่างงาน (Draft)</option>
                                    <option value="registration">เปิดรับสมัคร (Registration)</option>
                                    <option value="ongoing">กำลังแข่งขัน (Ongoing)</option>
                                    <option value="completed">จบการแข่งขัน (Completed)</option>
                                </select>
                            </div>

                            <div
                                class="md:col-span-2 p-4 bg-gray-50 dark:bg-white/5 rounded-2xl border border-gray-100 dark:border-white/5">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1.5 text-red-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <label
                                            class="block text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">
                                            ปักหมุดสถานที่จัดงาน (ลากหมุดสีน้ำเงิน)
                                        </label>
                                    </div>
                                    <div class="flex space-x-2 text-[10px] text-gray-400">
                                        <span>Lat: <span id="edit_lat_display" class="font-mono text-yellow-500"
                                                x-text="editComp.latitude || '18.7883'"></span></span>
                                        <span>Lng: <span id="edit_lng_display" class="font-mono text-yellow-500"
                                                x-text="editComp.longitude || '98.9853'"></span></span>
                                    </div>
                                </div>
                                <div id="map-edit"
                                    class="w-full h-64 rounded-xl border border-gray-200 dark:border-gray-700"
                                    style="z-index: 1;"></div>

                                <input type="hidden" name="latitude" id="edit_lat" x-model="editComp.latitude"
                                    onchange="document.getElementById('edit_lat_display').innerText = this.value">
                                <input type="hidden" name="longitude" id="edit_lng" x-model="editComp.longitude"
                                    onchange="document.getElementById('edit_lng_display').innerText = this.value">
                            </div>

                            <div class="md:col-span-2">
                                <label
                                    class="block text-xs font-semibold uppercase tracking-widest text-gray-400 mb-2">รายละเอียดงาน
                                    / คำอธิบาย</label>
                                <textarea name="description" x-model="editComp.description" rows="3"
                                    class="w-full border-gray-100 dark:border-white/5 dark:bg-[#0f0f0f] dark:text-white rounded-xl focus:ring-yellow-500 font-normal"></textarea>
                            </div>

                            <div class="md:col-span-2">
                                <label
                                    class="block text-xs font-semibold uppercase tracking-widest text-gray-400 mb-2">เปลี่ยนแบนเนอร์
                                    (ข้ามหากไม่เปลี่ยน)</label>
                                <input type="file" name="banner" accept="image/*"
                                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-yellow-50 dark:file:bg-yellow-900/20 dark:file:text-yellow-500 font-normal">
                            </div>

                            <div
                                class="p-4 bg-yellow-50 dark:bg-yellow-500/5 rounded-2xl md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4 border border-yellow-100 dark:border-yellow-500/10">
                                <div class="md:col-span-2 flex items-center mb-2"><span
                                        class="text-sm font-semibold text-yellow-700 dark:text-yellow-400">ช่วงเวลาเปิดรับสมัคร</span>
                                </div>
                                <input type="datetime-local" name="regis_start_date" x-model="editComp.regis_start"
                                    class="w-full border-none bg-white dark:bg-black/20 dark:text-white rounded-lg focus:ring-yellow-500 text-sm font-normal">
                                <input type="datetime-local" name="regis_end_date" x-model="editComp.regis_end"
                                    class="w-full border-none bg-white dark:bg-black/20 dark:text-white rounded-lg focus:ring-yellow-500 text-sm font-normal">
                            </div>

                            <div
                                class="p-4 bg-purple-50 dark:bg-purple-500/5 rounded-2xl md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4 border border-purple-100 dark:border-purple-500/10">
                                <div class="md:col-span-2 flex items-center mb-2"><span
                                        class="text-sm font-semibold text-purple-700 dark:text-purple-400">วันจัดงานแข่งขันจริง</span>
                                </div>
                                <input type="date" name="event_start_date" x-model="editComp.event_start"
                                    class="w-full border-none bg-white dark:bg-black/20 dark:text-white rounded-lg focus:ring-yellow-500 text-sm font-normal">
                                <input type="date" name="event_end_date" x-model="editComp.event_end"
                                    class="w-full border-none bg-white dark:bg-black/20 dark:text-white rounded-lg focus:ring-yellow-500 text-sm font-normal">
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end space-x-3 pt-4 border-t border-gray-100 dark:border-white/5">
                            <button type="button" x-on:click="$dispatch('close')"
                                class="px-6 py-2.5 bg-gray-100 dark:bg-gray-800 text-gray-500 rounded-xl text-xs font-semibold uppercase tracking-widest transition-all">ยกเลิก</button>
                            <button type="submit"
                                class="px-6 py-2.5 bg-yellow-500 text-white rounded-xl text-xs font-semibold uppercase tracking-widest shadow-lg shadow-yellow-500/20 transition-all">บันทึกการแก้ไข</button>
                        </div>
                    </form>
                </div>
            </div>
        </x-modal>
    </div>

    <form id="delete-form" method="POST" class="hidden">@csrf @method('DELETE')</form>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(url, name) {
            Swal.fire({
                title: 'ลบรายการแข่งขัน?',
                text: `หากลบ "${name}" ข้อมูลรายการย่อยและรายชื่อทีมทั้งหมดในงานนี้จะถูกลบไปด้วย!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'ยืนยันการลบ',
                cancelButtonText: 'ยกเลิก',
                background: document.documentElement.classList.contains('dark') ? '#1a1a1a' : '#fff',
                color: document.documentElement.classList.contains('dark') ? '#fff' : '#000',
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('delete-form');
                    form.action = url;
                    form.submit();
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ!',
                    text: "{{ session('success') }}",
                    timer: 2000,
                    showConfirmButton: false,
                    background: document.documentElement.classList.contains('dark') ? '#1a1a1a' : '#fff',
                    color: document.documentElement.classList.contains('dark') ? '#fff' : '#000'
                });
            @endif
            @if ($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด!',
                    text: "{{ $errors->first() }}",
                    background: document.documentElement.classList.contains('dark') ? '#1a1a1a' : '#fff',
                    color: document.documentElement.classList.contains('dark') ? '#fff' : '#000'
                });
            @endif
        });
    </script>
</x-admin-layout>
