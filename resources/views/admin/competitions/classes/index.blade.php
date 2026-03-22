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

        <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center">
                <a href="{{ route('admin.competitions.index') }}"
                    class="p-3 bg-white dark:bg-[#0f0f0f] border border-gray-100 dark:border-white/5 rounded-xl shadow-sm mr-4 text-gray-500 hover:text-blue-500 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <div class="text-[10px] font-semibold text-blue-500 uppercase tracking-widest mb-1">รายการย่อย
                        (Classes)</div>
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white line-clamp-1">
                        {{ $competition->name }}</h1>
                </div>
            </div>

            <button @click="$dispatch('open-modal', 'add-class')"
                class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold uppercase tracking-wider rounded-xl transition-all shadow-lg shadow-blue-500/20 flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                เพิ่มรุ่นการแข่งขัน
            </button>
        </div>

        {{-- @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded-r-xl">
                <div class="flex items-center text-red-800 dark:text-red-400 font-semibold mb-2">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    ระบบปฏิเสธการบันทึกข้อมูล:
                </div>
                <ul class="list-disc pl-7 text-sm text-red-600 dark:text-red-400 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif --}}

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @forelse($classes as $class)
                <div
                    class="bg-white dark:bg-[#0f0f0f] border border-gray-100 dark:border-white/5 rounded-2xl shadow-sm hover:shadow-xl transition-all p-6 flex flex-col">

                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <span
                                class="px-2.5 py-1 bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 text-[10px] font-semibold uppercase rounded-lg">
                                {{ $class->game_type_name }}
                            </span>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mt-2">{{ $class->name }}
                            </h3>
                        </div>

                        <div
                            class="w-12 h-12 rounded-xl border border-gray-100 dark:border-gray-800 overflow-hidden shrink-0 bg-white">
                            <img src="{{ route('admin.competitions.classes.picture', [$competition->id, $class->id]) }}"
                                alt="Robot" class="w-full h-full object-cover">
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2 mb-4">
                        @foreach ($class->allowed_categories as $cat)
                            <span
                                class="px-2 py-1 bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-300 rounded-lg text-xs font-semibold">
                                {{ $cat['name'] }} <span
                                    class="font-normal">({{ $cat['min_age'] }}-{{ $cat['max_age'] }} ปี)</span>
                            </span>
                        @endforeach
                    </div>

                    <div
                        class="space-y-2.5 text-sm text-gray-500 dark:text-gray-400 mb-6 bg-gray-50 dark:bg-white/5 p-4 rounded-xl font-normal">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                                </svg>
                                <span>หุ่นยนต์:</span>
                            </div>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ $class->robot_name }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                                </svg>
                                <span>น้ำหนัก:</span>
                            </div>
                            <span
                                class="font-semibold text-gray-900 dark:text-white">{{ $class->robot_weight ?? '-' }}
                                Kg.</span>
                        </div>
                        <div
                            class="flex justify-between items-center border-t border-gray-200 dark:border-gray-700 pt-2 mt-2">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>ค่าสมัคร:</span>
                            </div>
                            <span class="font-semibold text-green-600 dark:text-green-400">
                                {{ $class->entry_fee > 0 ? number_format($class->entry_fee) . ' บาท' : 'ฟรี' }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13.732 4c-.76-1.01-1.93-1.42-3.232-1.42s-2.472.41-3.232 1.42" />
                                </svg>
                                <span>สมาชิก/ทีม:</span>
                            </div>
                            <span class="font-semibold text-gray-900 dark:text-white">ไม่เกิน {{ $class->max_members }}
                                คน</span>
                        </div>
                    </div>

                    <div class="mt-auto grid grid-cols-2 gap-2">
                        @if ($class->rules_url)
                            <a href="{{ route('admin.competitions.classes.rule', [$competition->id, $class->id]) }}"
                                target="_blank"
                                class="col-span-2 flex justify-center items-center py-2 text-xs font-semibold text-purple-600 bg-purple-50 hover:bg-purple-100 dark:bg-purple-500/10 dark:hover:bg-purple-500/20 rounded-xl transition-colors mb-2">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2zM13 3v5h5M9 13h6m-6 4h6" />
                                </svg>
                                ดูกติกาการแข่งขัน (PDF)
                            </a>
                        @endif

                        <button
                            @click="editClass = {
                            id: '{{ $class->id }}', 
                            name: '{{ addslashes($class->name) }}', 
                            entry_fee: '{{ $class->entry_fee }}',
                            max_members: '{{ $class->max_members }}', 
                            max_teams: '{{ $class->max_teams }}', 
                            game_type_name: '{{ addslashes($class->game_type_name) }}',
                            robot_name: '{{ addslashes($class->robot_name) }}', 
                            
                            /* 💡 ดึงจาก robot_weight ใน DB มาใส่ให้ */
                            robot_weight: '{{ $class->robot_weight }}', 
                            
                            /* 💡 ดึงหมวดหมู่อายุตัวแรกออกมาเป็น String ให้ Radio Button */
                            allowed_category: '{{ !empty($class->allowed_categories) ? $class->allowed_categories[0]['name'] : '' }}'
                            
                        }; $dispatch('open-modal', 'edit-class')"
                            class="py-2 text-yellow-500 hover:bg-yellow-50 dark:hover:bg-yellow-500/10 rounded-xl text-xs font-semibold flex justify-center items-center transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            แก้ไข
                        </button>

                        <button
                            onclick="confirmDelete('{{ route('admin.competitions.classes.destroy', [$competition->id, $class->id]) }}', '{{ addslashes($class->name) }}')"
                            class="py-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-xl text-xs font-semibold flex justify-center items-center transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            ลบ
                        </button>
                    </div>
                </div>
            @empty
                <div
                    class="col-span-full py-20 flex flex-col items-center justify-center border-2 border-dashed border-gray-200 dark:border-gray-800 rounded-2xl">
                    <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400 font-normal">ยังไม่มีรุ่นการแข่งขันในงานนี้</p>
                </div>
            @endforelse
        </div>

        {{-- ========================================== --}}
        {{-- Modal: เพิ่มรุ่นการแข่งขัน --}}
        {{-- ========================================== --}}
        <x-modal name="add-class" focusable maxWidth="3xl">
            <div class="bg-white dark:bg-[#1a1a1a] flex flex-col max-h-[90vh]">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-white/5 shrink-0">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">เพิ่มรุ่นการแข่งขัน</h2>
                </div>

                <div class="p-6 overflow-y-auto">
                    <form method="POST" action="{{ route('admin.competitions.classes.store', $competition->id) }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <div class="md:col-span-2">
                                <label
                                    class="block text-xs font-semibold uppercase text-gray-400 mb-2">ชื่อรุ่นการแข่งขัน
                                    *</label>
                                <input type="text" name="name" 
                                    placeholder="เช่น Mega Sumo 3Kg - Junior"
                                    class="w-full border-gray-100 dark:border-white/5 dark:bg-[#0f0f0f] dark:text-white rounded-xl focus:ring-blue-500 font-normal">
                            </div>

                            <div
                                class="md:col-span-2 p-5 bg-blue-50 dark:bg-blue-900/10 rounded-2xl border border-blue-100 dark:border-blue-500/20">
                                <div class="flex items-center mb-4 text-blue-600 dark:text-blue-400">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    <span class="font-semibold">ตั้งค่าหุ่นยนต์</span>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label
                                            class="block text-xs font-semibold uppercase text-gray-500 mb-2">เลือกแม่แบบหุ่นยนต์
                                            (เพื่อ Auto-fill)</label>
                                        <select x-model="newClass.robot_model_id" @change="fillRobotData('new')"
                                            class="w-full border-gray-200 dark:border-gray-700 dark:bg-[#1a1a1a] dark:text-white rounded-xl focus:ring-blue-500 font-normal">
                                            <option value="">-- เลือกแม่แบบ --</option>
                                            @foreach ($robotModels as $model)
                                                <option value="{{ $model->id }}">{{ $model->name }}
                                                    ({{ $model->standard_weight }} Kg)</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-semibold uppercase text-gray-500 mb-2">ประเภทเกมแข่งขัน
                                            *</label>
                                        <select name="game_type_name" 
                                            class="w-full border-gray-200 dark:border-gray-700 dark:bg-[#1a1a1a] dark:text-white rounded-xl focus:ring-blue-500 font-normal">
                                            <option value="">-- เลือกประเภท --</option>
                                            @foreach ($gameTypes as $game)
                                                <option value="{{ $game->name }}">{{ $game->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-xs font-semibold uppercase text-gray-500 mb-2">ชื่อหุ่นยนต์ที่จะใช้แข่ง
                                            *</label>
                                        <input type="text" name="robot_name" x-model="newClass.robot_name"
                                            
                                            class="w-full border-gray-200 dark:border-gray-700 dark:bg-[#1a1a1a] dark:text-white rounded-xl focus:ring-blue-500 font-semibold text-blue-600">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-semibold uppercase text-gray-500 mb-2">พิกัดน้ำหนัก
                                            (Kg.)</label>
                                        <input type="number" step="0.01" name="robot_weight"
                                            x-model="newClass.robot_weight"
                                            class="w-full border-gray-200 dark:border-gray-700 dark:bg-[#1a1a1a] dark:text-white rounded-xl focus:ring-blue-500 font-semibold text-blue-600">
                                    </div>

                                    <div class="md:col-span-2">
                                        <label
                                            class="block text-xs font-semibold uppercase text-gray-500 mb-2">อัปโหลดรูปหุ่นยนต์ใหม่
                                            (ข้ามได้ถ้ารูปเดิมดีแล้ว)</label>
                                        <input type="file" name="robot_image" accept="image/*"
                                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-white dark:file:bg-gray-800 file:text-blue-500 font-normal">
                                        <input type="hidden" name="master_robot_image_url"
                                            x-model="newClass.master_robot_image_url">
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold uppercase text-gray-400 mb-2">ค่าสมัคร (บาท)
                                    *</label>
                                <input type="number" name="entry_fee"  value="0" min="0"
                                    class="w-full border-gray-100 dark:border-white/5 dark:bg-[#0f0f0f] dark:text-white rounded-xl focus:ring-blue-500 font-normal">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase text-gray-400 mb-2">สมาชิกสูงสุด
                                    (คน/ทีม) *</label>
                                <input type="number" name="max_members"  value="1" min="1"
                                    class="w-full border-gray-100 dark:border-white/5 dark:bg-[#0f0f0f] dark:text-white rounded-xl focus:ring-blue-500 font-normal">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase text-gray-400 mb-2">จำนวนทีมที่รับ
                                    (เว้นว่าง = ไม่อั้น)</label>
                                <input type="number" name="max_teams" min="1" placeholder="ไม่จำกัด"
                                    class="w-full border-gray-100 dark:border-white/5 dark:bg-[#0f0f0f] dark:text-white rounded-xl focus:ring-blue-500 font-normal">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase text-gray-400 mb-2">ไฟล์กติกา PDF
                                    (เฉพาะรุ่นนี้)</label>
                                <input type="file" name="rule_pdf" accept="application/pdf"
                                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-purple-50 dark:file:bg-purple-900/20 file:text-purple-500 font-normal">
                            </div>

                            <div class="md:col-span-2">
                                <label
                                    class="block text-xs font-semibold uppercase text-gray-400 mb-3">หมวดหมู่อายุที่ลงแข่งได้
                                    (เลือก 1 รุ่น) *</label>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                    @foreach ($categories as $category)
                                        <label
                                            class="flex items-center p-3 border border-gray-100 dark:border-white/5 rounded-xl cursor-pointer hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">

                                            <input type="radio" name="allowed_category"
                                                value="{{ $category->name }}"
                                                class="w-4 h-4 text-blue-600 border-gray-300 rounded-full focus:ring-blue-500 mr-3">

                                            <div class="flex flex-col">
                                                <span
                                                    class="text-sm font-semibold text-gray-900 dark:text-white">{{ $category->name }}</span>
                                                <span
                                                    class="text-[10px] text-gray-500 font-normal">{{ $category->min_age }}
                                                    - {{ $category->max_age }} ปี</span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                        </div>

                        <div class="mt-8 flex justify-end space-x-3 pt-4 border-t border-gray-100 dark:border-white/5">
                            <button type="button" @click="$dispatch('close')"
                                class="px-6 py-2.5 bg-gray-100 dark:bg-gray-800 text-gray-500 rounded-xl text-xs font-semibold uppercase tracking-widest transition-all">ยกเลิก</button>
                            <button type="submit"
                                class="px-6 py-2.5 bg-blue-600 text-white rounded-xl text-xs font-semibold uppercase tracking-widest shadow-lg shadow-blue-500/20 transition-all">บันทึกข้อมูล</button>
                        </div>
                    </form>
                </div>
            </div>
        </x-modal>


            {{-- ========================================== --}}
        {{-- Modal: แก้ไขรุ่นการแข่งขัน --}}
        {{-- ========================================== --}}
        <x-modal name="edit-class" focusable maxWidth="3xl">
            <div class="bg-white dark:bg-[#1a1a1a] flex flex-col max-h-[90vh]">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-white/5 shrink-0 bg-yellow-50 dark:bg-yellow-500/10">
                    <h2 class="text-xl font-semibold text-yellow-700 dark:text-yellow-500 flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        แก้ไขรุ่นการแข่งขัน
                    </h2>
                </div>

                <div class="p-6 overflow-y-auto">
                    <form method="POST" :action="`{{ url('/admin/competitions/' . $competition->id . '/classes') }}/${editClass.id}`" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold uppercase text-gray-400 mb-2">ชื่อรุ่นการแข่งขัน *</label>
                                <input type="text" name="name" x-model="editClass.name" required
                                    class="w-full border-gray-100 dark:border-white/5 dark:bg-[#0f0f0f] dark:text-white rounded-xl focus:ring-yellow-500 font-normal">
                            </div>

                            <div class="md:col-span-2 p-5 bg-gray-50 dark:bg-[#0f0f0f] rounded-2xl border border-gray-100 dark:border-white/5">
                                <div class="flex items-center mb-4 text-gray-600 dark:text-gray-400">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span class="font-semibold">ตั้งค่าหุ่นยนต์</span>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold uppercase text-gray-500 mb-2">ดึงข้อมูลจากแม่แบบใหม่ (ถ้าต้องการเปลี่ยน)</label>
                                        <select x-model="editClass.robot_model_id" @change="fillRobotData('edit')"
                                                class="w-full border-gray-200 dark:border-gray-700 dark:bg-[#1a1a1a] dark:text-white rounded-xl focus:ring-yellow-500 font-normal">
                                            <option value="">-- ไม่เปลี่ยนแม่แบบ --</option>
                                            @foreach ($robotModels as $model)
                                                <option value="{{ $model->id }}">{{ $model->name }} ({{ $model->standard_weight }} Kg)</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold uppercase text-gray-500 mb-2">ประเภทเกมแข่งขัน *</label>
                                        <select name="game_type_name" x-model="editClass.game_type_name" required
                                            class="w-full border-gray-200 dark:border-gray-700 dark:bg-[#1a1a1a] dark:text-white rounded-xl focus:ring-yellow-500 font-normal">
                                            <option value="">-- เลือกประเภท --</option>
                                            @foreach ($gameTypes as $game)
                                                <option value="{{ $game->name }}">{{ $game->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold uppercase text-gray-500 mb-2">ชื่อหุ่นยนต์ที่จะใช้แข่ง *</label>
                                        <input type="text" name="robot_name" x-model="editClass.robot_name" required
                                            class="w-full border-gray-200 dark:border-gray-700 dark:bg-[#1a1a1a] dark:text-white rounded-xl focus:ring-yellow-500 font-semibold text-gray-900">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold uppercase text-gray-500 mb-2">พิกัดน้ำหนัก (Kg.)</label>
                                        <input type="number" step="0.01" name="robot_weight" x-model="editClass.robot_weight"
                                            class="w-full border-gray-200 dark:border-gray-700 dark:bg-[#1a1a1a] dark:text-white rounded-xl focus:ring-yellow-500 font-semibold text-gray-900 ">
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-semibold uppercase text-gray-500 mb-2">อัปโหลดรูปหุ่นยนต์ใหม่ (ข้ามได้ถ้ารูปเดิมดีแล้ว)</label>
                                        <input type="file" name="robot_image" accept="image/*"
                                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-white dark:file:bg-gray-800 file:text-yellow-600 font-normal">
                                        <p class="text-[10px] text-gray-400 mt-1">* หากอัปโหลดรูปใหม่ รูปเดิมจะถูกลบทิ้งอัตโนมัติ</p>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold uppercase text-gray-400 mb-2">ค่าสมัคร (บาท) *</label>
                                <input type="number" name="entry_fee" x-model="editClass.entry_fee" required min="0"
                                    class="w-full border-gray-100 dark:border-white/5 dark:bg-[#0f0f0f] dark:text-white rounded-xl focus:ring-yellow-500 font-normal">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase text-gray-400 mb-2">สมาชิกสูงสุด (คน/ทีม) *</label>
                                <input type="number" name="max_members" x-model="editClass.max_members" required min="1"
                                    class="w-full border-gray-100 dark:border-white/5 dark:bg-[#0f0f0f] dark:text-white rounded-xl focus:ring-yellow-500 font-normal">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase text-gray-400 mb-2">จำนวนทีมที่รับ (เว้นว่าง = ไม่อั้น)</label>
                                <input type="number" name="max_teams" x-model="editClass.max_teams" min="1" placeholder="ไม่จำกัด"
                                    class="w-full border-gray-100 dark:border-white/5 dark:bg-[#0f0f0f] dark:text-white rounded-xl focus:ring-yellow-500 font-normal">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase text-gray-400 mb-2">อัปเดตไฟล์กติกา PDF (เฉพาะรุ่นนี้)</label>
                                <input type="file" name="rule_pdf" accept="application/pdf"
                                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-yellow-50 dark:file:bg-yellow-900/20 file:text-yellow-600 font-normal">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold uppercase text-gray-400 mb-3">หมวดหมู่อายุที่ลงแข่งได้ (เลือก 1 รุ่น) *</label>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                    @foreach ($categories as $category)
                                        <label class="flex items-center p-3 border border-gray-100 dark:border-white/5 rounded-xl cursor-pointer hover:bg-gray-50 dark:hover:bg-white/5 transition-colors"
                                               :class="editClass.allowed_category === '{{ $category->name }}' ? 'border-yellow-500 bg-yellow-50/50 dark:bg-yellow-500/10' : ''">

                                            <input type="radio" name="allowed_category" x-model="editClass.allowed_category"
                                                value="{{ $category->name }}" required
                                                class="w-4 h-4 text-yellow-600 border-gray-300 rounded-full focus:ring-yellow-500 mr-3">

                                            <div class="flex flex-col">
                                                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $category->name }}</span>
                                                <span class="text-[10px] text-gray-500 font-normal">{{ $category->min_age }} - {{ $category->max_age }} ปี</span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                        </div>

                        <div class="mt-8 flex justify-end space-x-3 pt-4 border-t border-gray-100 dark:border-white/5">
                            <button type="button" @click="$dispatch('close')"
                                class="px-6 py-2.5 bg-gray-100 dark:bg-gray-800 text-gray-500 rounded-xl text-xs font-semibold uppercase tracking-widest transition-all">ยกเลิก</button>
                            <button type="submit"
                                class="px-6 py-2.5 bg-yellow-500 hover:bg-yellow-600 text-white rounded-xl text-xs font-semibold uppercase tracking-widest shadow-lg shadow-yellow-500/20 transition-all">บันทึกการแก้ไข</button>
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
                text: `ต้องการลบ "${name}" ใช่หรือไม่? (ไฟล์กติกาและรูปภาพจะถูกลบด้วย)`,
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
