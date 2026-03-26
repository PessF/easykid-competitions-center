<x-admin-layout>
    <x-slot name="title">จัดการรายการย่อย | {{ $competition->name }}</x-slot>

    <div x-data="{
        robotModels: {{ Js::from($robotModels) }},
    
        newClass: {
            robot_model_id: '',
            robot_name: '',
            robot_weight: '',
            master_robot_image_url: ''
        },
    
        editClass: {
            id: '',
            name: '',
            entry_fee: '',
            max_members: '',
            max_teams: '',
            game_type_name: '',
            robot_model_id: '',
            robot_name: '',
            robot_weight: '',
            master_robot_image_url: '',
            allowed_category: '',
            allowed_categories: []
        },
    
        fillRobotData(type) {
            let modelId = type === 'new' ? this.newClass.robot_model_id : this.editClass.robot_model_id;
            let selected = this.robotModels.find(r => r.id == modelId);
    
            if (selected) {
                if (type === 'new') {
                    this.newClass.robot_name = selected.name;
                    this.newClass.robot_weight = selected.standard_weight;
                    this.newClass.master_robot_image_url = selected.image_url;
                } else {
                    this.editClass.robot_name = selected.name;
                    this.editClass.robot_weight = selected.standard_weight;
                    this.editClass.master_robot_image_url = selected.image_url;
                }
            }
        }
    }">

        {{-- ===== HEADER ===== --}}
        <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.competitions.index') }}"
                    class="w-12 h-12 bg-white dark:bg-[#1a1a1a] border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm flex items-center justify-center text-gray-500 hover:text-blue-600 dark:hover:text-blue-400 hover:border-blue-300 transition-all shrink-0">
                    <i class="fas fa-arrow-left text-lg"></i>
                </a>
                <div>
                    <div class="text-[10px] font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-widest mb-1 flex items-center gap-1.5">
                        <i class="fas fa-layer-group"></i> รายการย่อย (Classes)
                    </div>
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white line-clamp-1 tracking-tight">
                        {{ $competition->name }}
                    </h1>
                </div>
            </div>

            <button @click="$dispatch('open-modal', 'add-class')"
                class="group px-6 py-3 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-sm font-semibold rounded-xl transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                <i class="fas fa-plus-circle text-base transition-transform duration-200 group-hover:rotate-90"></i>
                เพิ่มรุ่นการแข่งขัน
            </button>
        </div>

        {{-- ===== GRID CARDS ===== --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @forelse($classes as $class)
                <div class="bg-white dark:bg-[#1a1a1a] border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm hover:shadow-xl hover:border-blue-300 dark:hover:border-gray-600 transition-all duration-300 p-6 flex flex-col group">

                    {{-- Card Header --}}
                    <div class="flex justify-between items-start mb-5">
                        <div class="flex-1 pr-4">
                            <span class="inline-flex items-center px-3 py-1 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 text-xs font-semibold rounded-lg border border-blue-100 dark:border-blue-800/30 mb-2">
                                <i class="fas fa-gamepad mr-1.5 opacity-70"></i> {{ $class->game_type_name }}
                            </span>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white leading-snug line-clamp-2" title="{{ $class->name }}">
                                {{ $class->name }}
                            </h3>
                        </div>

                        <div class="w-14 h-14 rounded-xl border-2 border-gray-100 dark:border-gray-700 overflow-hidden shrink-0 bg-gray-50 dark:bg-[#111] flex items-center justify-center shadow-sm">
                            @if($class->robot_image_url) {{-- สมมติว่ามีฟิลด์นี้ (ใน route admin.competitions.classes.picture) --}}
                                <img src="{{ route('admin.competitions.classes.picture', [$competition->id, $class->id]) }}"
                                    alt="Robot" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            @else
                                <i class="fas fa-robot text-gray-300 dark:text-gray-600 text-2xl"></i>
                            @endif
                        </div>
                    </div>

                    {{-- Age Categories Badge --}}
                    <div class="flex flex-wrap gap-2 mb-5">
                        @foreach ($class->allowed_categories as $cat)
                            <span class="px-2.5 py-1.5 bg-gray-50 dark:bg-[#111] border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-xs font-medium flex items-center shadow-sm">
                                <i class="fas fa-user-graduate text-[10px] text-gray-400 mr-1.5"></i>
                                {{ $cat['name'] }} <span class="text-gray-400 ml-1">({{ $cat['min_age'] }}-{{ $cat['max_age'] }} ปี)</span>
                            </span>
                        @endforeach
                    </div>

                    {{-- Info Box --}}
                    <div class="space-y-3 text-sm text-gray-600 dark:text-gray-400 mb-6 bg-gray-50 dark:bg-black/20 p-4 rounded-xl border border-gray-100 dark:border-gray-800 flex-1">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center">
                                <i class="fas fa-robot w-5 text-center mr-2 text-gray-400"></i>
                                <span>หุ่นยนต์:</span>
                            </div>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ $class->robot_name }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <div class="flex items-center">
                                <i class="fas fa-weight-hanging w-5 text-center mr-2 text-gray-400"></i>
                                <span>น้ำหนัก:</span>
                            </div>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ $class->robot_weight ?? '-' }} Kg.</span>
                        </div>
                        
                        <div class="border-t border-gray-200 dark:border-gray-700 my-2 pt-2"></div>
                        
                        <div class="flex justify-between items-center">
                            <div class="flex items-center">
                                <i class="fas fa-coins w-5 text-center mr-2 text-green-500"></i>
                                <span>ค่าสมัคร:</span>
                            </div>
                            <span class="font-semibold text-green-600 dark:text-green-400">
                                {{ $class->entry_fee > 0 ? number_format($class->entry_fee) . ' บาท' : 'ฟรี' }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <div class="flex items-center">
                                <i class="fas fa-users w-5 text-center mr-2 text-blue-500"></i>
                                <span>สมาชิก/ทีม:</span>
                            </div>
                            <span class="font-semibold text-gray-900 dark:text-white">ไม่เกิน {{ $class->max_members }} คน</span>
                        </div>
                    </div>

                    {{-- Footer Actions --}}
                    <div class="mt-auto flex flex-col gap-3">
                        @if ($class->rules_url)
                            <a href="{{ route('admin.competitions.classes.rule', [$competition->id, $class->id]) }}"
                                target="_blank"
                                class="flex justify-center items-center py-2.5 text-sm font-semibold text-purple-600 dark:text-purple-400 bg-purple-50 hover:bg-purple-100 dark:bg-purple-900/20 dark:hover:bg-purple-900/40 rounded-xl transition-colors border border-transparent hover:border-purple-200 dark:hover:border-purple-800">
                                <i class="fas fa-file-pdf mr-2 text-lg"></i>
                                ดูกติกาการแข่งขัน (PDF)
                            </a>
                        @endif

                        <div class="grid grid-cols-2 gap-3 pt-4 border-t border-gray-100 dark:border-gray-800">
                            <button @click="editClass = {
                                    id: '{{ $class->id }}', 
                                    name: '{{ addslashes($class->name) }}', 
                                    entry_fee: '{{ $class->entry_fee }}',
                                    max_members: '{{ $class->max_members }}', 
                                    max_teams: '{{ $class->max_teams }}', 
                                    game_type_name: '{{ addslashes($class->game_type_name) }}',
                                    robot_name: '{{ addslashes($class->robot_name) }}', 
                                    robot_weight: '{{ $class->robot_weight }}', 
                                    allowed_category: '{{ !empty($class->allowed_categories) ? $class->allowed_categories[0]['name'] : '' }}'
                                }; $dispatch('open-modal', 'edit-class')"
                                class="py-2.5 text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/20 dark:hover:bg-blue-900/40 rounded-xl text-sm font-semibold flex justify-center items-center transition-colors">
                                <i class="fas fa-pen mr-2 text-[11px]"></i> แก้ไข
                            </button>

                            <button onclick="confirmDelete('{{ route('admin.competitions.classes.destroy', [$competition->id, $class->id]) }}', '{{ addslashes($class->name) }}')"
                                class="py-2.5 text-red-500 hover:text-red-600 bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/40 rounded-xl text-sm font-semibold flex justify-center items-center transition-colors">
                                <i class="fas fa-trash-alt mr-2 text-[11px]"></i> ลบ
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-24 flex flex-col items-center justify-center border-2 border-dashed border-gray-200 dark:border-gray-800 rounded-2xl bg-white/50 dark:bg-[#1a1a1a]/50">
                    <div class="w-20 h-20 bg-gray-50 dark:bg-[#111] rounded-full flex items-center justify-center mb-5">
                        <i class="fas fa-layer-group text-4xl text-gray-300 dark:text-gray-600"></i>
                    </div>
                    <p class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-1">ยังไม่มีรุ่นการแข่งขันในงานนี้</p>
                    <p class="text-sm font-normal text-gray-500 dark:text-gray-400 mb-6">กดปุ่ม "เพิ่มรุ่นการแข่งขัน" เพื่อเริ่มต้น</p>
                    <button @click="$dispatch('open-modal', 'add-class')"
                        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-md transition-colors flex items-center gap-2">
                        <i class="fas fa-plus"></i> เพิ่มรุ่นการแข่งขัน
                    </button>
                </div>
            @endforelse
        </div>

        {{-- ========================================== --}}
        {{-- Modal: เพิ่มรุ่นการแข่งขัน (Structure Fix) --}}
        {{-- ========================================== --}}
        <x-modal name="add-class" focusable maxWidth="3xl">
            <div class="bg-white dark:bg-[#1a1a1a] flex flex-col h-[90vh] overflow-hidden rounded-2xl">
                
                {{-- Header --}}
                <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-800 shrink-0 flex items-center justify-between bg-white dark:bg-[#1a1a1a] z-20">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-xl flex items-center justify-center">
                            <i class="fas fa-plus-circle text-lg"></i>
                        </div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">เพิ่มรุ่นการแข่งขันใหม่</h2>
                    </div>
                    <button @click="$dispatch('close-modal', 'add-class')" type="button" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors focus:outline-none">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.competitions.classes.store', $competition->id) }}" enctype="multipart/form-data" class="flex flex-col flex-1 min-h-0">
                    @csrf
                    
                    {{-- Body Scrollable --}}
                    <div class="p-6 sm:p-8 overflow-y-auto custom-scrollbar flex-1 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- ชื่อรุ่น --}}
                            <div class="md:col-span-2 space-y-2">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">ชื่อรุ่นการแข่งขัน <span class="text-red-500">*</span></label>
                                <input type="text" name="name" required placeholder="เช่น Mega Sumo 3Kg - Junior"
                                    class="w-full px-4 py-3 text-sm border-gray-200 dark:border-gray-700 dark:bg-black/20 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                            </div>

                            {{-- กล่องตั้งค่าหุ่นยนต์ --}}
                            <div class="md:col-span-2 p-6 bg-blue-50/50 dark:bg-blue-900/10 rounded-2xl border border-blue-100 dark:border-blue-900/30">
                                <div class="flex items-center mb-5 border-b border-blue-100 dark:border-blue-900/30 pb-3">
                                    <i class="fas fa-robot text-blue-500 mr-2 text-lg"></i>
                                    <h3 class="text-base font-semibold text-blue-700 dark:text-blue-400">ตั้งค่าหุ่นยนต์ที่ใช้แข่ง</h3>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    {{-- เลือกแม่แบบ --}}
                                    <div class="space-y-2">
                                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400">เลือกแม่แบบหุ่นยนต์ (Auto-fill)</label>
                                        <div x-data="{ open: false }" class="relative" @click.outside="open = false">
                                            <button @click="open = !open" type="button" class="w-full px-4 py-3 bg-white dark:bg-[#111] border border-blue-200 dark:border-blue-800/50 rounded-xl text-sm text-left flex items-center justify-between focus:ring-2 focus:ring-blue-500/20 outline-none transition-colors">
                                                <span x-text="robotModels.find(r => r.id == newClass.robot_model_id)?.name || '-- เลือกแม่แบบ --'" class="text-gray-900 dark:text-white"></span>
                                                <i class="fas fa-chevron-down text-xs text-gray-400" :class="open && 'rotate-180'"></i>
                                            </button>
                                            <div x-show="open" style="display:none" class="absolute z-[60] w-full mt-1 bg-white dark:bg-[#222] shadow-xl rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden max-h-48 overflow-y-auto">
                                                <div @click="newClass.robot_model_id = ''; fillRobotData('new'); open = false" class="px-4 py-3 text-sm cursor-pointer hover:bg-blue-50 dark:hover:bg-gray-700 text-gray-500 italic">-- เลือกแม่แบบ --</div>
                                                @foreach ($robotModels as $model)
                                                    <div @click="newClass.robot_model_id = '{{ $model->id }}'; fillRobotData('new'); open = false" class="px-4 py-3 text-sm cursor-pointer hover:bg-blue-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">
                                                        {{ $model->name }} ({{ $model->standard_weight }} Kg)
                                                    </div>
                                                @endforeach
                                            </div>
                                            <input type="hidden" name="robot_model_id" x-model="newClass.robot_model_id">
                                        </div>
                                    </div>

                                    {{-- ประเภทเกม --}}
                                    <div class="space-y-2">
                                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400">ประเภทเกมแข่งขัน <span class="text-red-500">*</span></label>
                                        <div x-data="{ open: false, selected: '' }" class="relative" @click.outside="open = false">
                                            <button @click="open = !open" type="button" class="w-full px-4 py-3 bg-white dark:bg-[#111] border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-left flex items-center justify-between focus:ring-2 focus:ring-blue-500/20 outline-none">
                                                <span x-text="selected || '-- เลือกประเภท --'" class="text-gray-900 dark:text-white"></span>
                                                <i class="fas fa-chevron-down text-xs text-gray-400" :class="open && 'rotate-180'"></i>
                                            </button>
                                            <div x-show="open" style="display:none" class="absolute z-[60] w-full mt-1 bg-white dark:bg-[#222] shadow-xl rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden max-h-48 overflow-y-auto">
                                                @foreach ($gameTypes as $game)
                                                    <div @click="selected = '{{ $game->name }}'; open = false" class="px-4 py-3 text-sm cursor-pointer hover:bg-blue-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">{{ $game->name }}</div>
                                                @endforeach
                                            </div>
                                            <input type="hidden" name="game_type_name" x-model="selected" required>
                                        </div>
                                    </div>

                                    {{-- ชื่อหุ่น --}}
                                    <div class="space-y-2">
                                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400">ชื่อหุ่นยนต์ที่จะใช้แข่ง <span class="text-red-500">*</span></label>
                                        <input type="text" name="robot_name" x-model="newClass.robot_name" required
                                            class="w-full px-4 py-3 text-sm border-gray-200 dark:border-gray-700 bg-white dark:bg-[#111]  rounded-xl focus:ring-2 focus:ring-blue-500/20 font-semibold text-blue-600 dark:text-blue-400 outline-none transition-colors">
                                    </div>

                                    {{-- น้ำหนัก --}}
                                    <div class="space-y-2">
                                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400">พิกัดน้ำหนัก (Kg.)</label>
                                        <input type="number" step="0.01" name="robot_weight" x-model="newClass.robot_weight"
                                            class="w-full px-4 py-3 text-sm border-gray-200 dark:border-gray-700 bg-white dark:bg-[#111]  rounded-xl focus:ring-2 focus:ring-blue-500/20 font-semibold text-blue-600 dark:text-blue-400 outline-none transition-colors">
                                    </div>

                                    {{-- รูปหุ่น --}}
                                    <div class="md:col-span-2 mt-2">
                                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2">อัปโหลดรูปหุ่นยนต์ (ข้ามได้ถ้ารูปเดิมดีแล้ว)</label>
                                        <input type="file" name="robot_image" accept="image/*"
                                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-100 dark:file:bg-blue-900/30 file:text-blue-700 dark:file:text-blue-400 hover:file:bg-blue-200 cursor-pointer transition-colors">
                                        <input type="hidden" name="master_robot_image_url" x-model="newClass.master_robot_image_url">
                                    </div>
                                </div>
                            </div>

                            {{-- กติกา ค่าสมัคร จำนวน --}}
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">ค่าสมัคร (บาท) <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-baht-sign text-gray-400"></i>
                                    </div>
                                    <input type="number" name="entry_fee" value="0" min="0" required
                                        class="w-full pl-10 pr-4 py-3 text-sm border-gray-200 dark:border-gray-700 dark:bg-black/20 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500/20 outline-none transition-all">
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">ไฟล์กติกา PDF (เฉพาะรุ่นนี้)</label>
                                <input type="file" name="rule_pdf" accept="application/pdf"
                                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-purple-50 dark:file:bg-purple-900/20 file:text-purple-600 dark:file:text-purple-400 hover:file:bg-purple-100 cursor-pointer transition-colors">
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">สมาชิกสูงสุด (คน/ทีม) <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-users text-gray-400"></i>
                                    </div>
                                    <input type="number" name="max_members" value="1" min="1" required
                                        class="w-full pl-10 pr-4 py-3 text-sm border-gray-200 dark:border-gray-700 dark:bg-black/20 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500/20 outline-none transition-all">
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">จำนวนทีมที่รับ <span class="text-gray-400 font-normal">(เว้นว่าง = ไม่อั้น)</span></label>
                                <input type="number" name="max_teams" min="1" placeholder="ไม่จำกัด"
                                    class="w-full px-4 py-3 text-sm border-gray-200 dark:border-gray-700 dark:bg-black/20 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500/20 outline-none transition-all">
                            </div>

                            {{-- หมวดหมู่อายุ --}}
                            <div class="md:col-span-2 space-y-3 mt-2">
                                <div class="flex items-center gap-2 mb-1 border-b border-gray-100 dark:border-gray-800 pb-2">
                                    <i class="fas fa-user-graduate text-blue-500"></i>
                                    <label class="block text-base font-semibold text-gray-800 dark:text-gray-200">หมวดหมู่อายุที่ลงแข่งได้ <span class="text-red-500">*</span></label>
                                </div>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-3" x-data="{ selectedAge: '' }">
                                    @foreach ($categories as $category)
                                        <label class="flex items-center p-3.5 border-2 rounded-xl cursor-pointer transition-all duration-200"
                                               :class="selectedAge === '{{ $category->name }}' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20 shadow-sm' : 'border-gray-100 dark:border-gray-800 bg-white dark:bg-[#111] hover:border-blue-200 dark:hover:border-gray-600'">
                                            
                                            <input type="radio" name="allowed_category" value="{{ $category->name }}" required
                                                x-model="selectedAge"
                                                class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500 mr-3 mt-0.5">
                                            
                                            <div class="flex flex-col">
                                                <span class="text-sm font-semibold text-gray-900 dark:text-white leading-tight mb-0.5">{{ $category->name }}</span>
                                                <span class="text-[11px] text-gray-500 font-medium bg-gray-100 dark:bg-black/40 px-2 py-0.5 rounded-md w-fit">{{ $category->min_age }} - {{ $category->max_age }} ปี</span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="px-6 py-4 bg-gray-50 dark:bg-[#111] border-t border-gray-200 dark:border-gray-800 shrink-0 flex justify-end gap-3 z-20">
                        <button type="button" @click="$dispatch('close-modal', 'add-class')" class="px-6 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-semibold transition-colors focus:outline-none shadow-sm">ยกเลิก</button>
                        <button type="submit" class="px-8 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-semibold shadow-md flex items-center gap-2 transition-all">
                            <i class="fas fa-save"></i> บันทึกข้อมูล
                        </button>
                    </div>
                </form>
            </div>
        </x-modal>

        {{-- ========================================== --}}
        {{-- Modal: แก้ไขรุ่นการแข่งขัน (Structure Fix) --}}
        {{-- ========================================== --}}
        <x-modal name="edit-class" focusable maxWidth="3xl">
            <div class="bg-white dark:bg-[#1a1a1a] flex flex-col h-[90vh] overflow-hidden rounded-2xl">
                
                {{-- Header --}}
                <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-800 shrink-0 flex items-center justify-between bg-white dark:bg-[#1a1a1a] z-20">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-xl flex items-center justify-center">
                            <i class="fas fa-pen text-lg"></i>
                        </div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">แก้ไขรุ่นการแข่งขัน</h2>
                    </div>
                    <button @click="$dispatch('close-modal', 'edit-class')" type="button" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors focus:outline-none">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <form method="POST" :action="`{{ url('/admin/competitions/' . $competition->id . '/classes') }}/${editClass.id}`" enctype="multipart/form-data" class="flex flex-col flex-1 min-h-0">
                    @csrf @method('PUT')
                    
                    {{-- Body Scrollable --}}
                    <div class="p-6 sm:p-8 overflow-y-auto custom-scrollbar flex-1 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- ชื่อรุ่น --}}
                            <div class="md:col-span-2 space-y-2">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">ชื่อรุ่นการแข่งขัน <span class="text-red-500">*</span></label>
                                <input type="text" name="name" x-model="editClass.name" required
                                    class="w-full px-4 py-3 text-sm border-gray-200 dark:border-gray-700 dark:bg-black/20 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                            </div>

                            {{-- กล่องตั้งค่าหุ่นยนต์ --}}
                            <div class="md:col-span-2 p-6 bg-gray-50 dark:bg-[#111] rounded-2xl border border-gray-200 dark:border-gray-800">
                                <div class="flex items-center mb-5 border-b border-gray-200 dark:border-gray-700 pb-3">
                                    <i class="fas fa-robot text-gray-500 mr-2 text-lg"></i>
                                    <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200">ตั้งค่าหุ่นยนต์ที่ใช้แข่ง</h3>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    {{-- เลือกแม่แบบ --}}
                                    <div class="space-y-2">
                                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400">ดึงข้อมูลจากแม่แบบใหม่ (ข้ามได้)</label>
                                        <div x-data="{ open: false }" class="relative" @click.outside="open = false">
                                            <button @click="open = !open" type="button" class="w-full px-4 py-3 bg-white dark:bg-[#1a1a1a] border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-left flex items-center justify-between focus:ring-2 focus:ring-blue-500/20 outline-none transition-colors">
                                                <span x-text="robotModels.find(r => r.id == editClass.robot_model_id)?.name || '-- ไม่เปลี่ยนแม่แบบ --'" class="text-gray-900 dark:text-white"></span>
                                                <i class="fas fa-chevron-down text-xs text-gray-400" :class="open && 'rotate-180'"></i>
                                            </button>
                                            <div x-show="open" style="display:none" class="absolute z-[60] w-full mt-1 bg-white dark:bg-[#222] shadow-xl rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden max-h-48 overflow-y-auto">
                                                <div @click="editClass.robot_model_id = ''; open = false" class="px-4 py-3 text-sm cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-500 italic">-- ไม่เปลี่ยนแม่แบบ --</div>
                                                @foreach ($robotModels as $model)
                                                    <div @click="editClass.robot_model_id = '{{ $model->id }}'; fillRobotData('edit'); open = false" class="px-4 py-3 text-sm cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">
                                                        {{ $model->name }} ({{ $model->standard_weight }} Kg)
                                                    </div>
                                                @endforeach
                                            </div>
                                            <input type="hidden" name="robot_model_id" x-model="editClass.robot_model_id">
                                        </div>
                                    </div>

                                    {{-- ประเภทเกม --}}
                                    <div class="space-y-2">
                                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400">ประเภทเกมแข่งขัน <span class="text-red-500">*</span></label>
                                        <div x-data="{ open: false }" class="relative" @click.outside="open = false">
                                            <button @click="open = !open" type="button" class="w-full px-4 py-3 bg-white dark:bg-[#1a1a1a] border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-left flex items-center justify-between focus:ring-2 focus:ring-blue-500/20 outline-none">
                                                <span x-text="editClass.game_type_name || '-- เลือกประเภท --'" class="text-gray-900 dark:text-white"></span>
                                                <i class="fas fa-chevron-down text-xs text-gray-400" :class="open && 'rotate-180'"></i>
                                            </button>
                                            <div x-show="open" style="display:none" class="absolute z-[60] w-full mt-1 bg-white dark:bg-[#222] shadow-xl rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden max-h-48 overflow-y-auto">
                                                @foreach ($gameTypes as $game)
                                                    <div @click="editClass.game_type_name = '{{ $game->name }}'; open = false" class="px-4 py-3 text-sm cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">{{ $game->name }}</div>
                                                @endforeach
                                            </div>
                                            <input type="hidden" name="game_type_name" x-model="editClass.game_type_name" required>
                                        </div>
                                    </div>

                                    {{-- ชื่อหุ่น --}}
                                    <div class="space-y-2">
                                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400">ชื่อหุ่นยนต์ที่จะใช้แข่ง <span class="text-red-500">*</span></label>
                                        <input type="text" name="robot_name" x-model="editClass.robot_name" required
                                            class="w-full px-4 py-3 text-sm border-gray-200 dark:border-gray-700 bg-white dark:bg-[#1a1a1a]  rounded-xl focus:ring-2 focus:ring-blue-500/20 font-semibold text-gray-900 dark:text-gray-100 outline-none transition-colors">
                                    </div>

                                    {{-- น้ำหนัก --}}
                                    <div class="space-y-2">
                                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400">พิกัดน้ำหนัก (Kg.)</label>
                                        <input type="number" step="0.01" name="robot_weight" x-model="editClass.robot_weight"
                                            class="w-full px-4 py-3 text-sm border-gray-200 dark:border-gray-700 bg-white dark:bg-[#1a1a1a]  rounded-xl focus:ring-2 focus:ring-blue-500/20 font-semibold text-gray-900 dark:text-gray-100 outline-none transition-colors">
                                    </div>

                                    {{-- รูปหุ่น --}}
                                    <div class="md:col-span-2 mt-2">
                                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2">อัปโหลดรูปหุ่นยนต์ใหม่ <span class="font-normal">(ข้ามได้ถ้ารูปเดิมดีแล้ว)</span></label>
                                        <input type="file" name="robot_image" accept="image/*"
                                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-gray-100 dark:file:bg-gray-800 file:text-gray-700 dark:file:text-gray-300 hover:file:bg-gray-200 cursor-pointer transition-colors">
                                        <p class="text-[10px] text-red-500 mt-1.5 ml-1">* หากอัปโหลดรูปใหม่ รูปเดิมจะถูกแทนที่ทันที</p>
                                    </div>
                                </div>
                            </div>

                            {{-- กติกา ค่าสมัคร จำนวน --}}
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">ค่าสมัคร (บาท) <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-baht-sign text-gray-400"></i>
                                    </div>
                                    <input type="number" name="entry_fee" x-model="editClass.entry_fee" min="0" required
                                        class="w-full pl-10 pr-4 py-3 text-sm border-gray-200 dark:border-gray-700 dark:bg-black/20 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500/20 outline-none transition-all">
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">อัปเดตไฟล์กติกา PDF</label>
                                <input type="file" name="rule_pdf" accept="application/pdf"
                                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-purple-50 dark:file:bg-purple-900/20 file:text-purple-600 dark:file:text-purple-400 hover:file:bg-purple-100 cursor-pointer transition-colors">
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">สมาชิกสูงสุด (คน/ทีม) <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-users text-gray-400"></i>
                                    </div>
                                    <input type="number" name="max_members" x-model="editClass.max_members" min="1" required
                                        class="w-full pl-10 pr-4 py-3 text-sm border-gray-200 dark:border-gray-700 dark:bg-black/20 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500/20 outline-none transition-all">
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">จำนวนทีมที่รับ <span class="text-gray-400 font-normal">(เว้นว่าง = ไม่อั้น)</span></label>
                                <input type="number" name="max_teams" x-model="editClass.max_teams" min="1" placeholder="ไม่จำกัด"
                                    class="w-full px-4 py-3 text-sm border-gray-200 dark:border-gray-700 dark:bg-black/20 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500/20 outline-none transition-all">
                            </div>

                            {{-- หมวดหมู่อายุ --}}
                            <div class="md:col-span-2 space-y-3 mt-2">
                                <div class="flex items-center gap-2 mb-1 border-b border-gray-100 dark:border-gray-800 pb-2">
                                    <i class="fas fa-user-graduate text-blue-500"></i>
                                    <label class="block text-base font-semibold text-gray-800 dark:text-gray-200">หมวดหมู่อายุที่ลงแข่งได้ <span class="text-red-500">*</span></label>
                                </div>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                    @foreach ($categories as $category)
                                        <label class="flex items-center p-3.5 border-2 rounded-xl cursor-pointer transition-all duration-200"
                                               :class="editClass.allowed_category === '{{ $category->name }}' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20 shadow-sm' : 'border-gray-100 dark:border-gray-800 bg-white dark:bg-[#111] hover:border-blue-200 dark:hover:border-gray-600'">
                                            
                                            <input type="radio" name="allowed_category" value="{{ $category->name }}" required
                                                x-model="editClass.allowed_category"
                                                class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500 mr-3 mt-0.5">
                                            
                                            <div class="flex flex-col">
                                                <span class="text-sm font-semibold text-gray-900 dark:text-white leading-tight mb-0.5">{{ $category->name }}</span>
                                                <span class="text-[11px] text-gray-500 font-medium bg-gray-100 dark:bg-black/40 px-2 py-0.5 rounded-md w-fit">{{ $category->min_age }} - {{ $category->max_age }} ปี</span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="px-6 py-4 bg-gray-50 dark:bg-[#111] border-t border-gray-200 dark:border-gray-800 shrink-0 flex justify-end gap-3 z-20">
                        <button type="button" @click="$dispatch('close-modal', 'edit-class')" class="px-6 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-semibold transition-colors focus:outline-none shadow-sm">ยกเลิก</button>
                        <button type="submit" class="px-8 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-semibold shadow-md flex items-center gap-2 transition-all">
                            <i class="fas fa-save"></i> บันทึกการแก้ไข
                        </button>
                    </div>
                </form>
            </div>
        </x-modal>

    </div>

    <form id="delete-form" method="POST" class="hidden">@csrf @method('DELETE')</form>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(url, name) {
            Swal.fire({
                title: 'ลบรายการย่อย?',
                text: `ต้องการลบ "${name}" ใช่หรือไม่? (ไฟล์กติกาและรูปภาพจะถูกลบด้วย)`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'ยืนยันการลบ',
                cancelButtonText: 'ยกเลิก',
                customClass: {
                    popup: 'rounded-2xl font-kanit',
                    confirmButton: 'rounded-xl px-6 py-2.5 font-semibold',
                    cancelButton: 'rounded-xl px-6 py-2.5 font-semibold'
                },
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