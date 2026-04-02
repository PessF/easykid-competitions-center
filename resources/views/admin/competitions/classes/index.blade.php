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
            min_members: '', // 🚀 เพิ่ม min_members
            max_members: '',
            max_teams: '',
            game_type_name: '',
            robot_model_id: '',
            robot_name: '',
            robot_weight: '',
            master_robot_image_url: '',
            allowed_category: ''
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
                            @if($class->robot_image_url)
                                <img src="{{ str_starts_with($class->robot_image_url, 'http') ? $class->robot_image_url : asset('storage/' . $class->robot_image_url) }}"
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
                            <span class="font-semibold text-gray-900 dark:text-white line-clamp-1 ml-2 text-right flex-1">{{ $class->robot_name }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <div class="flex items-center">
                                <i class="fas fa-weight-hanging w-5 text-center mr-2 text-gray-400"></i>
                                <span>น้ำหนัก:</span>
                            </div>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ $class->robot_weight ? number_format($class->robot_weight, 2) . ' Kg.' : 'ไม่จำกัด' }}</span>
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
                                <span>สมาชิก (คน/ทีม):</span>
                            </div>
                            <span class="font-semibold text-gray-900 dark:text-white">
                                {{-- 🚀 โชว์ข้อมูลให้ถูกว่า เป็นช่วง หรือ คงที่ --}}
                                {{ $class->min_members == $class->max_members ? $class->max_members : $class->min_members . '-' . $class->max_members }} คน
                            </span>
                        </div>
                    </div>

                    {{-- Footer Actions --}}
                    <div class="mt-auto flex flex-col gap-3">
                        @if ($class->rules_url)
                            <a href="{{ route('competitions.classes.rule', [$competition->id, $class->id]) }}"
                                target="_blank"
                                class="flex justify-center items-center py-2.5 text-sm font-semibold text-purple-600 dark:text-purple-400 bg-purple-50 hover:bg-purple-100 dark:bg-purple-900/20 dark:hover:bg-purple-900/40 rounded-xl transition-colors border border-transparent hover:border-purple-200 dark:hover:border-purple-800">
                                <i class="fas fa-file-pdf mr-2 text-lg"></i>
                                ดูกติกาการแข่งขัน (PDF)
                            </a>
                        @endif

                        <div class="grid grid-cols-2 gap-3 pt-4 border-t border-gray-100 dark:border-gray-800">
                            <button @click="editClass = {
                                    id: '{{ $class->id }}', 
                                    name: {{ Js::from($class->name) }}, 
                                    entry_fee: '{{ (float)$class->entry_fee }}',
                                    min_members: '{{ $class->min_members ?? 1 }}', {{-- 🚀 ดึง min_members --}}
                                    max_members: '{{ $class->max_members }}', 
                                    max_teams: '{{ $class->max_teams ?? '' }}', 
                                    game_type_name: {{ Js::from($class->game_type_name) }},
                                    robot_model_id: '{{ $class->robot_model_id ?? '' }}',
                                    robot_name: {{ Js::from($class->robot_name) }}, 
                                    robot_weight: '{{ $class->robot_weight ?? '' }}', 
                                    master_robot_image_url: '{{ $class->robot_image_url ?? '' }}',
                                    allowed_category: '{{ !empty($class->allowed_categories) ? $class->allowed_categories[0]['name'] : '' }}'
                                }; $dispatch('open-modal', 'edit-class')"
                                class="py-2.5 text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/20 dark:hover:bg-blue-900/40 rounded-xl text-sm font-semibold flex justify-center items-center transition-colors">
                                <i class="fas fa-pen mr-2 text-[11px]"></i> แก้ไข
                            </button>

                            <button onclick="confirmDelete('{{ route('admin.competitions.classes.destroy', [$competition->id, $class->id]) }}', {{ Js::from($class->name) }})"
                                class="py-2.5 text-red-500 hover:text-red-600 bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/40 rounded-xl text-sm font-semibold flex justify-center items-center transition-colors">
                                <i class="fas fa-trash-alt mr-2 text-[11px]"></i> ลบ
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-24 flex flex-col items-center justify-center border-2 border-dashed border-gray-200 dark:border-gray-800 rounded-2xl bg-white/50 dark:bg-[#1a1a1a]/50">
                    <i class="fas fa-layer-group text-4xl text-gray-300 mb-4"></i>
                    <p class="text-lg font-semibold text-gray-700 dark:text-gray-300">ยังไม่มีรุ่นการแข่งขันในงานนี้</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination Links --}}
        <div class="mt-8 flex justify-center">
            {{ $classes->links() }}
        </div>

        {{-- ===== MODAL: ADD ===== --}}
        <x-modal name="add-class" focusable maxWidth="3xl">
            <div class="bg-white dark:bg-[#1a1a1a] flex flex-col h-[90vh] overflow-hidden rounded-2xl">
                <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between">
                    <h2 class="text-xl font-semibold dark:text-white text-blue-600">เพิ่มรุ่นการแข่งขันใหม่</h2>
                    <button @click="$dispatch('close-modal', 'add-class')" class="text-gray-400 hover:text-red-500 transition-colors"><i class="fas fa-times"></i></button>
                </div>

                <form method="POST" action="{{ route('admin.competitions.classes.store', $competition->id) }}" enctype="multipart/form-data" class="flex flex-col flex-1 overflow-hidden">
                    @csrf
                    <div class="p-6 sm:p-8 overflow-y-auto custom-scrollbar flex-1 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2 space-y-2">
                                <label class="block text-sm font-semibold dark:text-gray-300">ชื่อรุ่นการแข่งขัน <span class="text-red-500">*</span></label>
                                <input type="text" name="name" required class="w-full px-4 py-3 border-gray-200 dark:border-gray-700 dark:bg-black/20 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500/20 outline-none">
                            </div>

                            {{-- Robot Config Box --}}
                            <div class="md:col-span-2 p-6 bg-blue-50/50 dark:bg-blue-900/10 rounded-2xl border border-blue-100 dark:border-blue-900/30 grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div class="md:col-span-2 flex items-center gap-2 border-b border-blue-100 dark:border-blue-900/30 pb-3">
                                    <i class="fas fa-robot text-blue-500"></i>
                                    <h3 class="text-base font-semibold text-blue-700 dark:text-blue-400">ตั้งค่าหุ่นยนต์</h3>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-xs font-semibold text-gray-500">เลือกแม่แบบหุ่นยนต์ (Auto-fill)</label>
                                    <select x-model="newClass.robot_model_id" @change="fillRobotData('new')" name="robot_model_id" class="w-full px-4 py-3 bg-white dark:bg-[#111] border-gray-200 dark:border-gray-700 rounded-xl text-sm dark:text-white">
                                        <option value="">-- กำหนดเอง --</option>
                                        @foreach ($robotModels as $model)
                                            <option value="{{ $model->id }}">{{ $model->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-xs font-semibold text-gray-500">ประเภทเกมแข่งขัน <span class="text-red-500">*</span></label>
                                    <select name="game_type_name" required class="w-full px-4 py-3 bg-white dark:bg-[#111] border-gray-200 dark:border-gray-700 rounded-xl text-sm dark:text-white">
                                        <option value="">-- เลือกประเภท --</option>
                                        @foreach ($gameTypes as $game)
                                            <option value="{{ $game->name }}">{{ $game->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-xs font-semibold text-gray-500">ชื่อหุ่นยนต์ที่ใช้แข่ง <span class="text-red-500">*</span></label>
                                    <input type="text" name="robot_name" x-model="newClass.robot_name" required class="w-full px-4 py-3 bg-white dark:bg-[#111] border-gray-200 dark:border-gray-700 rounded-xl text-sm dark:text-white">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-xs font-semibold text-gray-500">พิกัดน้ำหนัก (Kg.)</label>
                                    <input type="number" step="0.01" name="robot_weight" x-model="newClass.robot_weight" class="w-full px-4 py-3 bg-white dark:bg-[#111] border-gray-200 dark:border-gray-700 rounded-xl text-sm dark:text-white">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-semibold text-gray-500 mb-2">อัปโหลดรูปหุ่นยนต์ (Snapshot)</label>
                                    <input type="file" name="robot_image" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:bg-blue-100 dark:file:bg-blue-900/30 file:text-blue-700">
                                    <input type="hidden" name="master_robot_image_url" x-model="newClass.master_robot_image_url">
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-semibold dark:text-gray-300">ค่าสมัคร (บาท) <span class="text-red-500">*</span></label>
                                <input type="number" name="entry_fee" value="0" min="0" required class="w-full px-4 py-3 border-gray-200 dark:border-gray-700 dark:bg-black/20 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500/20 outline-none">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold dark:text-gray-300">ไฟล์กติกา PDF</label>
                                <input type="file" name="rule_pdf" accept="application/pdf" class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:bg-purple-50 dark:file:bg-purple-900/20 file:text-purple-600">
                            </div>
                            
                            {{-- 🚀 แก้ไข: จำนวนสมาชิก --}}
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold dark:text-gray-300">จำนวนสมาชิก (คน/ทีม) <span class="text-red-500">*</span></label>
                                <div class="flex items-center gap-2">
                                    <input type="number" name="min_members" value="1" min="1" placeholder="ขั้นต่ำ" required class="w-full px-4 py-3 border-gray-200 dark:border-gray-700 dark:bg-black/20 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500/20 outline-none">
                                    <span class="text-gray-400 font-bold">-</span>
                                    <input type="number" name="max_members" value="1" min="1" placeholder="สูงสุด" required class="w-full px-4 py-3 border-gray-200 dark:border-gray-700 dark:bg-black/20 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500/20 outline-none">
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-semibold dark:text-gray-300">จำนวนทีมที่รับ</label>
                                <input type="number" name="max_teams" min="1" placeholder="ไม่จำกัด" class="w-full px-4 py-3 border-gray-200 dark:border-gray-700 dark:bg-black/20 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500/20 outline-none">
                            </div>

                            <div class="md:col-span-2 space-y-3">
                                <label class="block text-sm font-semibold dark:text-gray-300">หมวดหมู่อายุที่ลงแข่งได้ <span class="text-red-500">*</span></label>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-3" x-data="{ selectedAge: '' }">
                                    @foreach ($categories as $category)
                                        <label class="flex items-center p-3 border-2 rounded-xl cursor-pointer hover:bg-gray-50 dark:hover:bg-white/5 transition-all"
                                               :class="selectedAge === '{{ $category->name }}' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-100 dark:border-gray-800 bg-white dark:bg-[#111]'">
                                            <input type="radio" name="allowed_category" value="{{ $category->name }}" required x-model="selectedAge" class="w-4 h-4 text-blue-600 mr-3">
                                            <div class="flex flex-col">
                                                <span class="text-sm font-bold dark:text-white">{{ $category->name }}</span>
                                                <span class="text-[10px] text-gray-500">{{ $category->min_age }}-{{ $category->max_age }} ปี</span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 dark:bg-[#111] border-t border-gray-200 dark:border-gray-800 flex justify-end gap-3">
                        <button type="button" @click="$dispatch('close-modal', 'add-class')" class="px-6 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl text-sm font-semibold">ยกเลิก</button>
                        <button type="submit" class="px-8 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-semibold shadow-md">บันทึกข้อมูล</button>
                    </div>
                </form>
            </div>
        </x-modal>

        {{-- ===== MODAL: EDIT ===== --}}
        <x-modal name="edit-class" focusable maxWidth="3xl">
            <div class="bg-white dark:bg-[#1a1a1a] flex flex-col h-[90vh] overflow-hidden rounded-2xl">
                <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between">
                    <h2 class="text-xl font-semibold dark:text-white text-blue-600">แก้ไขรุ่นการแข่งขัน</h2>
                    <button @click="$dispatch('close-modal', 'edit-class')" class="text-gray-400 hover:text-red-500 transition-colors"><i class="fas fa-times"></i></button>
                </div>

                <form method="POST" :action="`{{ url('/admin/competitions/' . $competition->id . '/classes') }}/${editClass.id}`" enctype="multipart/form-data" class="flex flex-col flex-1 overflow-hidden">
                    @csrf @method('PUT')
                    <div class="p-6 sm:p-8 overflow-y-auto custom-scrollbar flex-1 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2 space-y-2">
                                <label class="block text-sm font-semibold dark:text-gray-300">ชื่อรุ่นการแข่งขัน <span class="text-red-500">*</span></label>
                                <input type="text" name="name" x-model="editClass.name" required class="w-full px-4 py-3 border-gray-200 dark:border-gray-700 dark:bg-black/20 dark:text-white rounded-xl">
                            </div>

                            <div class="md:col-span-2 p-6 bg-blue-50/50 dark:bg-blue-900/10 rounded-2xl border border-blue-100 dark:border-blue-900/30 grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div class="md:col-span-2 flex items-center gap-4">
                                    <div class="w-16 h-16 rounded-xl border-2 border-white dark:border-gray-700 overflow-hidden bg-gray-100 shadow-sm shrink-0">
                                        <template x-if="editClass.master_robot_image_url">
                                            <img :src="editClass.master_robot_image_url.startsWith('http') ? editClass.master_robot_image_url : '/storage/' + editClass.master_robot_image_url" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="!editClass.master_robot_image_url">
                                            <div class="w-full h-full flex items-center justify-center text-gray-300"><i class="fas fa-robot text-xl"></i></div>
                                        </template>
                                    </div>
                                    <h3 class="text-base font-semibold text-blue-700 dark:text-blue-400">ตั้งค่าหุ่นยนต์</h3>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-xs font-semibold text-gray-500">แม่แบบหุ่นยนต์</label>
                                    <select x-model="editClass.robot_model_id" @change="fillRobotData('edit')" name="robot_model_id" class="w-full px-4 py-3 bg-white dark:bg-[#111] border-gray-200 dark:border-gray-700 rounded-xl text-sm dark:text-white">
                                        <option value="">-- กำหนดเอง --</option>
                                        @foreach ($robotModels as $model)
                                            <option value="{{ $model->id }}">{{ $model->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-xs font-semibold text-gray-500">ประเภทเกมแข่งขัน <span class="text-red-500">*</span></label>
                                    <select x-model="editClass.game_type_name" name="game_type_name" required class="w-full px-4 py-3 bg-white dark:bg-[#111] border-gray-200 dark:border-gray-700 rounded-xl text-sm dark:text-white">
                                        @foreach ($gameTypes as $game)
                                            <option value="{{ $game->name }}">{{ $game->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-xs font-semibold text-gray-500">ชื่อหุ่นยนต์ <span class="text-red-500">*</span></label>
                                    <input type="text" name="robot_name" x-model="editClass.robot_name" required class="w-full px-4 py-3 bg-white dark:bg-[#111] border-gray-200 dark:border-gray-700 rounded-xl text-sm dark:text-white font-bold text-blue-600">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-xs font-semibold text-gray-500">พิกัดน้ำหนัก (Kg.)</label>
                                    <input type="number" step="0.01" name="robot_weight" x-model="editClass.robot_weight" class="w-full px-4 py-3 bg-white dark:bg-[#111] border-gray-200 dark:border-gray-700 rounded-xl text-sm dark:text-white font-bold text-blue-600">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-semibold text-gray-500 mb-2">อัปโหลดรูปหุ่นยนต์ใหม่ (ข้ามได้)</label>
                                    <input type="file" name="robot_image" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:bg-gray-100">
                                    <input type="hidden" name="master_robot_image_url" x-model="editClass.master_robot_image_url">
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-semibold dark:text-gray-300">ค่าสมัคร (บาท) <span class="text-red-500">*</span></label>
                                <input type="number" name="entry_fee" x-model="editClass.entry_fee" min="0" required class="w-full px-4 py-3 border-gray-200 dark:border-gray-700 dark:bg-black/20 dark:text-white rounded-xl">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold dark:text-gray-300">อัปเดตกติกา PDF</label>
                                <input type="file" name="rule_pdf" accept="application/pdf" class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:bg-purple-50">
                            </div>

                            {{-- 🚀 แก้ไข: จำนวนสมาชิก --}}
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold dark:text-gray-300">จำนวนสมาชิก (คน/ทีม) <span class="text-red-500">*</span></label>
                                <div class="flex items-center gap-2">
                                    <input type="number" name="min_members" x-model="editClass.min_members" min="1" placeholder="ขั้นต่ำ" required class="w-full px-4 py-3 border-gray-200 dark:border-gray-700 dark:bg-black/20 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500/20 outline-none">
                                    <span class="text-gray-400 font-bold">-</span>
                                    <input type="number" name="max_members" x-model="editClass.max_members" min="1" placeholder="สูงสุด" required class="w-full px-4 py-3 border-gray-200 dark:border-gray-700 dark:bg-black/20 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500/20 outline-none">
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-semibold dark:text-gray-300">จำนวนทีมที่รับ</label>
                                <input type="number" name="max_teams" x-model="editClass.max_teams" min="1" class="w-full px-4 py-3 border-gray-200 dark:border-gray-700 dark:bg-black/20 dark:text-white rounded-xl">
                            </div>

                            <div class="md:col-span-2 space-y-3">
                                <label class="block text-sm font-semibold dark:text-gray-300">หมวดหมู่อายุ <span class="text-red-500">*</span></label>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                    @foreach ($categories as $category)
                                        <label class="flex items-center p-3 border-2 rounded-xl cursor-pointer transition-all"
                                               :class="editClass.allowed_category === '{{ $category->name }}' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-100 dark:border-gray-800 bg-white dark:bg-[#111]'">
                                            <input type="radio" name="allowed_category" value="{{ $category->name }}" required x-model="editClass.allowed_category" class="w-4 h-4 text-blue-600 mr-3">
                                            <div class="flex flex-col">
                                                <span class="text-sm font-bold dark:text-white">{{ $category->name }}</span>
                                                <span class="text-[10px] text-gray-500">{{ $category->min_age }}-{{ $category->max_age }} ปี</span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 dark:bg-[#111] border-t border-gray-200 dark:border-gray-800 flex justify-end gap-3">
                        <button type="button" @click="$dispatch('close-modal', 'edit-class')" class="px-6 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl text-sm font-semibold">ยกเลิก</button>
                        <button type="submit" class="px-8 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-semibold">บันทึกการแก้ไข</button>
                    </div>
                </form>
            </div>
        </x-modal>

    </div>

    <form id="delete-form" method="POST" class="hidden">@csrf @method('DELETE')</form>

</x-admin-layout>