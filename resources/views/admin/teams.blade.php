<x-admin-layout>
    <div class="max-w-7xl mx-auto space-y-6">
        
        {{-- Header & Title --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-normal tracking-tight text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-users text-blue-500 mr-3"></i> รายชื่อการสมัครทั้งหมด
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    ระบบจัดการและค้นหาข้อมูลที่ส่งเข้าร่วมการแข่งขัน
                </p>
            </div>
        </div>

        {{-- ตัวแปร PHP สำหรับหาข้อความเริ่มต้นของ Dropdown (PHP 8 Match) --}}
        @php
            $compText = $compId ? ($competitions->firstWhere('id', $compId)->name ?? '-- ทุกงานแข่งขัน --') : '-- ทุกงานแข่งขัน --';
            $classText = $classId && !empty($classes) ? (collect($classes)->firstWhere('id', $classId)->name ?? '-- ทุกรุ่น --') : '-- ทุกรุ่น --';
            $statusText = match($status) {
                'approved' => 'อนุมัติแล้ว',
                'waiting_verify' => 'รอตรวจสอบสลิป',
                'pending_payment' => 'รอชำระเงิน',
                'rejected' => 'ถูกปฏิเสธ',
                default => 'ทั้งหมด',
            };
        @endphp

        {{-- Filter & Search Card --}}
        <div class="bg-white dark:bg-[#0f0f0f] p-5 lg:p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-white/5">
            <form method="GET" action="{{ route('admin.teams.index') }}" class="flex flex-col md:flex-row gap-5 items-end">
                
                {{-- 🚀 Custom Dropdown: งานแข่งขัน --}}
                <div class="w-full md:w-1/4">
                    <label class="block text-[11px] font-normal text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2 ml-1">งานแข่งขัน</label>
                    <div x-data="{ open: false, value: '{{ $compId }}', text: '{{ $compText }}' }" @click.away="open = false" class="relative">
                        <input type="hidden" name="competition_id" :value="value" x-ref="compInput">
                        <button type="button" @click="open = !open" 
                            class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm font-normal rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 flex justify-between items-center px-4 py-2.5 dark:bg-[#0a0a0a] dark:border-white/10 dark:text-white hover:border-gray-300 dark:hover:border-gray-600 transition-all duration-200 shadow-sm">
                            <span x-text="text" class="truncate pr-4"></span>
                            <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 text-gray-400 transition-transform duration-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <ul x-show="open" x-transition.opacity.duration.200ms style="display: none;" 
                            class="absolute z-50 w-full mt-1.5 bg-white border border-gray-100 rounded-xl shadow-lg dark:bg-[#1a1a1a] dark:border-white/10 max-h-60 overflow-y-auto py-1.5">
                            
                            <li @click="value=''; text='-- ทุกงานแข่งขัน --'; open=false; $nextTick(() => $refs.compInput.form.submit())" 
                                :class="value === '' ? 'bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5'" 
                                class="px-4 py-2.5 text-sm font-normal cursor-pointer transition-colors flex items-center justify-between">
                                -- ทุกงานแข่งขัน --
                                <i x-show="value === ''" class="fas fa-check text-xs"></i>
                            </li>
                            @foreach ($competitions as $comp)
                                <li @click="value='{{ $comp->id }}'; text='{{ addslashes($comp->name) }}'; open=false; $nextTick(() => $refs.compInput.form.submit())" 
                                    :class="value === '{{ $comp->id }}' ? 'bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5'" 
                                    class="px-4 py-2.5 text-sm font-normal cursor-pointer transition-colors flex items-center justify-between">
                                    {{ $comp->name }}
                                    <i x-show="value === '{{ $comp->id }}'" class="fas fa-check text-xs"></i>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                {{-- 🚀 Custom Dropdown: รุ่นการแข่งขัน --}}
                <div class="w-full md:w-1/4">
                    <label class="block text-[11px] font-normal text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2 ml-1">รุ่นการแข่งขัน</label>
                    <div x-data="{ open: false, value: '{{ $classId }}', text: '{{ $classText }}', disabled: {{ empty($classes) ? 'true' : 'false' }} }" @click.away="open = false" class="relative">
                        <input type="hidden" name="competition_class_id" :value="value" x-ref="classInput">
                        <button type="button" @click="if(!disabled) open = !open" 
                            :class="disabled ? 'opacity-50 cursor-not-allowed bg-gray-50 dark:bg-[#0a0a0a]' : 'bg-gray-50 dark:bg-[#0a0a0a] hover:border-gray-300 dark:hover:border-gray-600 cursor-pointer'"
                            class="w-full border border-gray-200 text-gray-900 text-sm font-normal rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 flex justify-between items-center px-4 py-2.5 dark:border-white/10 dark:text-white transition-all duration-200 shadow-sm">
                            <span x-text="text" class="truncate pr-4"></span>
                            <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 text-gray-400 transition-transform duration-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <ul x-show="open" x-transition.opacity.duration.200ms style="display: none;" 
                            class="absolute z-50 w-full mt-1.5 bg-white border border-gray-100 rounded-xl shadow-lg dark:bg-[#1a1a1a] dark:border-white/10 max-h-60 overflow-y-auto py-1.5">
                            
                            <li @click="value=''; text='-- ทุกรุ่น --'; open=false; $nextTick(() => $refs.classInput.form.submit())" 
                                :class="value === '' ? 'bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5'" 
                                class="px-4 py-2.5 text-sm font-normal cursor-pointer transition-colors flex items-center justify-between">
                                -- ทุกรุ่น --
                                <i x-show="value === ''" class="fas fa-check text-xs"></i>
                            </li>
                            @if(!empty($classes))
                                @foreach ($classes as $class)
                                    <li @click="value='{{ $class->id }}'; text='{{ addslashes($class->name) }}'; open=false; $nextTick(() => $refs.classInput.form.submit())" 
                                        :class="value === '{{ $class->id }}' ? 'bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5'" 
                                        class="px-4 py-2.5 text-sm font-normal cursor-pointer transition-colors flex items-center justify-between">
                                        {{ $class->name }}
                                        <i x-show="value === '{{ $class->id }}'" class="fas fa-check text-xs"></i>
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                </div>

                {{-- 🚀 Custom Dropdown: สถานะ --}}
                <div class="w-full md:w-1/5">
                    <label class="block text-[11px] font-normal text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2 ml-1">สถานะ</label>
                    <div x-data="{ open: false, value: '{{ $status }}', text: '{{ $statusText }}' }" @click.away="open = false" class="relative">
                        <input type="hidden" name="status" :value="value" x-ref="statusInput">
                        <button type="button" @click="open = !open" 
                            class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm font-normal rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 flex justify-between items-center px-4 py-2.5 dark:bg-[#0a0a0a] dark:border-white/10 dark:text-white hover:border-gray-300 dark:hover:border-gray-600 transition-all duration-200 shadow-sm">
                            <span x-text="text" class="truncate pr-4"></span>
                            <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 text-gray-400 transition-transform duration-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <ul x-show="open" x-transition.opacity.duration.200ms style="display: none;" 
                            class="absolute z-50 w-full mt-1.5 bg-white border border-gray-100 rounded-xl shadow-lg dark:bg-[#1a1a1a] dark:border-white/10 max-h-60 overflow-y-auto py-1.5">
                            
                            @foreach(['all' => 'ทั้งหมด', 'approved' => 'อนุมัติแล้ว', 'waiting_verify' => 'รอตรวจสอบสลิป', 'pending_payment' => 'รอชำระเงิน', 'rejected' => 'ถูกปฏิเสธ'] as $val => $label)
                                <li @click="value='{{ $val }}'; text='{{ $label }}'; open=false; $nextTick(() => $refs.statusInput.form.submit())" 
                                    :class="value === '{{ $val }}' ? 'bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5'" 
                                    class="px-4 py-2.5 text-sm font-normal cursor-pointer transition-colors flex items-center justify-between">
                                    {{ $label }}
                                    <i x-show="value === '{{ $val }}'" class="fas fa-check text-xs"></i>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                {{-- Search Input --}}
                <div class="w-full md:w-auto md:flex-1 relative">
                    <label class="block text-[11px] font-normal text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2 ml-1">ค้นหาในหน้านี้</label>
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pt-6 pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="รหัส, ชื่อทีม, ชื่อครู (กด Enter)" 
                        class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm font-normal rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 block pl-10 pr-4 py-2.5 dark:bg-[#0a0a0a] dark:border-white/10 dark:placeholder-gray-400 dark:text-white hover:border-gray-300 dark:hover:border-gray-600 transition-all duration-200 shadow-sm">
                </div>
                
                {{-- Reset Button --}}
                @if($compId || $classId || $status !== 'all' || !empty($search))
                    <a href="{{ route('admin.teams.index') }}" 
                        class="px-5 py-2.5 rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-[#1a1a1a] dark:text-gray-300 dark:hover:bg-[#2a2a2a] border border-transparent dark:border-white/5 text-sm font-normal transition-all duration-200 text-center shadow-sm flex items-center justify-center min-w-[120px]">
                        ล้างตัวกรอง
                    </a>
                @endif
            </form>
        </div>

        {{-- Table Section --}}
        <div class="bg-white dark:bg-[#0f0f0f] rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-[#0a0a0a] dark:text-gray-400 border-b border-gray-100 dark:border-white/5">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-normal tracking-wide">รหัส / สถานะ</th>
                            <th scope="col" class="px-6 py-4 font-normal tracking-wide">ข้อมูลทีม</th>
                            <th scope="col" class="px-6 py-4 font-normal tracking-wide">งานแข่ง / รุ่น</th>
                            <th scope="col" class="px-6 py-4 font-normal tracking-wide">ผู้ส่งสมัคร (ครู/ผู้ปกครอง)</th>
                            <th scope="col" class="px-6 py-4 font-normal tracking-wide text-center">สมาชิก</th>
                            <th scope="col" class="px-6 py-4 font-normal tracking-wide text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($registrations as $reg)
                            <tr class="border-b border-gray-50 dark:border-white/5 bg-white dark:bg-[#0f0f0f] transition-colors"
                                x-data="{ showDetailModal: false }">
                                
                                {{-- รหัสสมัคร และ สถานะ --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-gray-900 dark:text-white font-normal mb-1.5">{{ $reg->regis_no }}</div>
                                    @if($reg->status === 'approved')
                                        <span class="bg-green-100 text-green-800 text-xs font-normal px-2.5 py-0.5 rounded-md dark:bg-green-500/10 dark:text-green-400 border border-green-200 dark:border-green-500/20">อนุมัติแล้ว</span>
                                    @elseif($reg->status === 'waiting_verify')
                                        <span class="bg-blue-100 text-blue-800 text-xs font-normal px-2.5 py-0.5 rounded-md dark:bg-blue-500/10 dark:text-blue-400 border border-blue-200 dark:border-blue-500/20">รอตรวจสอบ</span>
                                    @elseif($reg->status === 'rejected')
                                        <span class="bg-red-100 text-red-800 text-xs font-normal px-2.5 py-0.5 rounded-md dark:bg-red-500/10 dark:text-red-400 border border-red-200 dark:border-red-500/20">ถูกปฏิเสธ</span>
                                    @else
                                        <span class="bg-gray-100 text-gray-800 text-xs font-normal px-2.5 py-0.5 rounded-md dark:bg-white/5 dark:text-gray-400 border border-gray-200 dark:border-white/10">รอชำระเงิน</span>
                                    @endif
                                </td>

                                {{-- ข้อมูลทีม --}}
                                <td class="px-6 py-4">
                                    <div class="text-gray-900 dark:text-white font-normal">{{ $reg->team->name ?? '-' }}</div>
                                    <div class="text-xs text-gray-500 font-normal mt-1"><i class="fas fa-school mr-1 opacity-70"></i>{{ $reg->team->school_name ?? '-' }}</div>
                                </td>

                                {{-- งานแข่ง / รุ่น --}}
                                <td class="px-6 py-4">
                                    <div class="text-gray-900 dark:text-white font-normal line-clamp-1">{{ $reg->competition->name ?? '-' }}</div>
                                    <div class="text-xs text-blue-600 dark:text-blue-400 font-normal mt-1">{{ $reg->competitionClass->name ?? '-' }}</div>
                                </td>

                                {{-- ผู้ส่งสมัคร --}}
                                <td class="px-6 py-4">
                                    <div class="text-gray-900 dark:text-white font-normal">{{ $reg->user->name ?? '-' }}</div>
                                    <div class="text-xs text-gray-500 font-normal mt-1"><i class="fas fa-phone mr-1 opacity-70"></i>{{ $reg->user->phone_number ?? '-' }}</div>
                                </td>

                                {{-- สมาชิก --}}
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-50 border border-gray-200 text-gray-700 dark:bg-[#1a1a1a] dark:border-white/10 dark:text-gray-300 font-normal text-sm shadow-sm">
                                        {{ $reg->team->members->count() ?? 0 }}
                                    </span>
                                </td>

                                {{-- จัดการ --}}
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <button type="button" @click="showDetailModal = true"
                                           class="p-2 text-gray-400 hover:text-blue-500 bg-gray-50 hover:bg-blue-50 rounded-lg dark:bg-white/5 dark:hover:bg-blue-500/10 transition-colors"
                                           title="ดูรายละเอียด">
                                            <i class="fas fa-eye w-4 h-4 flex items-center justify-center"></i>
                                        </button>
                                        
                                        {{-- 🚀 ตรวจสอบสิทธิ์: เฉพาะ Admin เท่านั้นที่เห็นปุ่มลบ --}}
                                        @if(Auth::user()->role === 'admin')
                                        <button type="button" 
                                                onclick="confirmDelete('{{ route('admin.teams.destroy', $reg->id) }}', 'ใบสมัครรหัส {{ $reg->regis_no }} ของทีม {{ addslashes($reg->team->name) }}')"
                                                class="p-2 text-gray-400 hover:text-red-500 bg-gray-50 hover:bg-red-50 rounded-lg dark:bg-white/5 dark:hover:bg-red-500/10 transition-colors"
                                                title="ลบใบสมัคร">
                                            <i class="fas fa-trash-alt w-4 h-4 flex items-center justify-center"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>

                                {{-- ========================================== --}}
                                {{-- 🚀 TELEPORT MODAL (รายละเอียดใบสมัคร) --}}
                                {{-- ========================================== --}}
                                <template x-teleport="body">
                                    <div x-show="showDetailModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6" x-trap="showDetailModal">
                                        
                                        <div x-show="showDetailModal" x-transition.opacity.duration.300ms 
                                             class="fixed inset-0 bg-black/60 backdrop-blur-sm" 
                                             @click="showDetailModal = false">
                                        </div>

                                        <div x-show="showDetailModal"
                                             x-transition:enter="transition ease-out duration-300"
                                             x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
                                             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                             x-transition:leave="transition ease-in duration-200"
                                             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                             x-transition:leave-end="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
                                             class="relative w-full max-w-5xl bg-gray-50 dark:bg-[#0a0a0a] rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
                                             
                                            <div class="px-6 py-5 border-b border-gray-200 dark:border-white/10 flex justify-between items-center bg-white dark:bg-[#0f0f0f] z-10 shrink-0">
                                                <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                                                    <h3 class="text-xl font-normal text-gray-900 dark:text-white flex items-center">
                                                        รายละเอียดใบสมัคร 
                                                        <span class="ml-3 px-3 py-1 bg-gray-100 dark:bg-[#1a1a1a] text-gray-700 dark:text-gray-300 rounded-lg text-base border border-gray-200 dark:border-white/5 font-mono">
                                                            #{{ $reg->regis_no }}
                                                        </span>
                                                    </h3>
                                                    <div>
                                                        @if($reg->status === 'approved')
                                                            <span class="bg-green-100 text-green-800 text-[11px] font-normal px-2.5 py-1 rounded-md dark:bg-green-500/10 dark:text-green-400 border border-green-200 dark:border-green-500/20"><i class="fas fa-check-circle mr-1"></i> อนุมัติแล้ว</span>
                                                        @elseif($reg->status === 'waiting_verify')
                                                            <span class="bg-blue-100 text-blue-800 text-[11px] font-normal px-2.5 py-1 rounded-md dark:bg-blue-500/10 dark:text-blue-400 border border-blue-200 dark:border-blue-500/20"><i class="fas fa-clock mr-1"></i> รอตรวจสอบ</span>
                                                        @elseif($reg->status === 'rejected')
                                                            <span class="bg-red-100 text-red-800 text-[11px] font-normal px-2.5 py-1 rounded-md dark:bg-red-500/10 dark:text-red-400 border border-red-200 dark:border-red-500/20"><i class="fas fa-times-circle mr-1"></i> ถูกปฏิเสธ</span>
                                                        @else
                                                            <span class="bg-gray-100 text-gray-800 text-[11px] font-normal px-2.5 py-1 rounded-md dark:bg-white/5 dark:text-gray-400 border border-gray-200 dark:border-white/10"><i class="fas fa-wallet mr-1"></i> รอชำระเงิน</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <button @click="showDetailModal = false" class="text-gray-400 hover:text-red-500 bg-gray-100 hover:bg-red-50 dark:bg-white/5 dark:hover:bg-red-500/10 w-8 h-8 rounded-lg flex items-center justify-center transition-colors">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>

                                            <div class="p-6 overflow-y-auto flex-1 space-y-6 custom-scrollbar">
                                                
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                    {{-- ข้อมูลทีมและงานแข่ง --}}
                                                    <div class="bg-white dark:bg-[#0f0f0f] p-5 rounded-xl shadow-sm border border-gray-100 dark:border-white/5 space-y-5">
                                                        <h2 class="text-sm text-gray-900 dark:text-white uppercase tracking-wider flex items-center border-b border-gray-100 dark:border-white/5 pb-3 font-normal">
                                                            <i class="fas fa-robot text-blue-500 mr-2"></i> ข้อมูลทีมและรุ่นการแข่งขัน
                                                        </h2>
                                                        <div class="space-y-3">
                                                            <div>
                                                                <p class="text-[11px] font-normal text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">ชื่องานแข่งขัน</p>
                                                                <p class="text-gray-900 dark:text-white text-sm font-normal">{{ $reg->competition->name ?? '-' }}</p>
                                                            </div>
                                                            <div>
                                                                <p class="text-[11px] font-normal text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">รุ่นที่สมัคร</p>
                                                                <p class="text-blue-600 dark:text-blue-400 text-sm font-normal">{{ $reg->competitionClass->name ?? '-' }}</p>
                                                            </div>
                                                            <div class="grid grid-cols-2 gap-4 pt-1">
                                                                <div>
                                                                    <p class="text-[11px] font-normal text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">ชื่อทีม</p>
                                                                    <p class="text-gray-900 dark:text-white text-base font-normal">{{ $reg->team->name ?? '-' }}</p>
                                                                </div>
                                                                <div>
                                                                    <p class="text-[11px] font-normal text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">โรงเรียน / สถาบัน</p>
                                                                    <p class="text-gray-900 dark:text-white text-sm font-normal"><i class="fas fa-school text-gray-400 text-xs mr-1 opacity-70"></i> {{ $reg->team->school_name ?? '-' }}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- ข้อมูลผู้ส่งสมัคร --}}
                                                    <div class="bg-white dark:bg-[#0f0f0f] p-5 rounded-xl shadow-sm border border-gray-100 dark:border-white/5 space-y-5">
                                                        <h2 class="text-sm text-gray-900 dark:text-white uppercase tracking-wider flex items-center border-b border-gray-100 dark:border-white/5 pb-3 font-normal">
                                                            <i class="fas fa-user-tie text-blue-500 mr-2"></i> ข้อมูลผู้ส่งสมัคร
                                                        </h2>
                                                        <div class="space-y-4">
                                                            <div class="flex items-center">
                                                                <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-[#1a1a1a] border border-gray-200 dark:border-white/10 flex items-center justify-center overflow-hidden mr-3 shrink-0">
                                                                    @if($reg->user->avatar)
                                                                        <img src="{{ Str::startsWith($reg->user->avatar, ['http://', 'https://']) ? $reg->user->avatar : route('avatar.show', $reg->user->id) }}" alt="Avatar" class="w-full h-full object-cover">
                                                                    @else
                                                                        <i class="fas fa-user text-gray-400 text-sm"></i>
                                                                    @endif
                                                                </div>
                                                                <div class="min-w-0">
                                                                    <p class="text-gray-900 dark:text-white text-base truncate font-normal">{{ $reg->user->name ?? '-' }}</p>
                                                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate font-normal">{{ $reg->user->email ?? '-' }}</p>
                                                                </div>
                                                            </div>
                                                            <div class="grid grid-cols-2 gap-4 pt-1">
                                                                <div>
                                                                    <p class="text-[11px] font-normal text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">เบอร์โทรศัพท์</p>
                                                                    <p class="text-gray-900 dark:text-white text-sm font-normal"><i class="fas fa-phone-alt text-gray-400 text-xs mr-1 opacity-70"></i> {{ $reg->user->phone_number ?? '-' }}</p>
                                                                </div>
                                                                <div>
                                                                    <p class="text-[11px] font-normal text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">วันที่ส่งสมัคร</p>
                                                                    <p class="text-gray-900 dark:text-white text-sm font-normal"><i class="fas fa-calendar-alt text-gray-400 text-xs mr-1 opacity-70"></i> {{ $reg->created_at->format('d/m/Y H:i') }}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- สมาชิกในทีม --}}
                                                <div class="bg-white dark:bg-[#0f0f0f] rounded-xl shadow-sm border border-gray-100 dark:border-white/5 overflow-hidden">
                                                    <div class="px-5 py-4 border-b border-gray-100 dark:border-white/5 flex items-center justify-between">
                                                        <h2 class="text-sm text-gray-900 dark:text-white uppercase tracking-wider flex items-center font-normal">
                                                            <i class="fas fa-users text-blue-500 mr-2"></i> สมาชิกในทีม
                                                        </h2>
                                                        <span class="bg-gray-100 dark:bg-[#1a1a1a] text-gray-700 dark:text-gray-300 text-[11px] px-2.5 py-1 rounded-md font-normal">
                                                            {{ $reg->team->members->count() }} คน
                                                        </span>
                                                    </div>
                                                    <div class="overflow-x-auto">
                                                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                                            <thead class="text-[11px] text-gray-500 uppercase bg-gray-50 dark:bg-[#0a0a0a] dark:text-gray-400 border-b border-gray-100 dark:border-white/5 tracking-wider">
                                                                <tr>
                                                                    <th scope="col" class="px-5 py-3 w-10 text-center font-normal">#</th>
                                                                    <th scope="col" class="px-5 py-3 font-normal">ชื่อ - นามสกุล (ไทย)</th>
                                                                    <th scope="col" class="px-5 py-3 font-normal">ชื่อ - นามสกุล (อังกฤษ)</th>
                                                                    <th scope="col" class="px-5 py-3 text-center font-normal">วันเกิด</th>
                                                                    <th scope="col" class="px-5 py-3 text-center font-normal">ไซส์เสื้อ</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @forelse($reg->team->members as $index => $member)
                                                                    <tr class="border-b border-gray-50 dark:border-white/5 bg-white dark:bg-[#0f0f0f]">
                                                                        <td class="px-5 py-3 text-center text-gray-400 font-normal">{{ $index + 1 }}</td>
                                                                        <td class="px-5 py-3 text-gray-900 dark:text-white font-normal">{{ $member->prefix_th }}{{ $member->first_name_th }} {{ $member->last_name_th }}</td>
                                                                        <td class="px-5 py-3 text-gray-600 dark:text-gray-300 font-normal">{{ $member->prefix_en }}{{ $member->first_name_en }} {{ $member->last_name_en }}</td>
                                                                        <td class="px-5 py-3 text-center font-normal">{{ \Carbon\Carbon::parse($member->birth_date)->format('d/m/Y') }}</td>
                                                                        <td class="px-5 py-3 text-center">
                                                                            <span class="inline-flex items-center justify-center min-w-[2rem] px-2 h-7 rounded-md bg-gray-50 dark:bg-[#1a1a1a] border border-gray-200 dark:border-white/10 text-gray-900 dark:text-white text-xs shadow-sm font-normal">
                                                                                {{ $member->shirt_size ?? '-' }}
                                                                            </span>
                                                                        </td>
                                                                    </tr>
                                                                @empty
                                                                    <tr>
                                                                        <td colspan="5" class="px-5 py-6 text-center text-gray-500 dark:text-gray-400 font-normal">ไม่มีข้อมูลสมาชิกในทีม</td>
                                                                    </tr>
                                                                @endforelse
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </template>
                                {{-- ========================================== --}}
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center text-gray-500 dark:text-gray-400 bg-white dark:bg-[#0f0f0f]">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-16 h-16 rounded-full bg-gray-50 dark:bg-[#1a1a1a] border border-gray-100 dark:border-white/5 flex items-center justify-center mb-4">
                                            <i class="fas fa-search text-2xl text-gray-300 dark:text-gray-600"></i>
                                        </div>
                                        <p class="text-base text-gray-900 dark:text-white font-normal">ไม่พบข้อมูลทีมผู้สมัคร</p>
                                        <p class="text-sm mt-1 font-normal">ลองเปลี่ยนเงื่อนไขการค้นหา หรือตัวกรองด้านบนอีกครั้ง</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Pagination --}}
            @if($registrations->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 dark:border-white/5 bg-white dark:bg-[#0f0f0f]">
                    {{ $registrations->links() }}
                </div>
            @endif
        </div>

    </div>

    {{-- สไตล์ปรับแต่ง Scrollbar --}}
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 6px; display: block !important; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #d1d5db; border-radius: 10px; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #374151; }
    </style>
</x-admin-layout>