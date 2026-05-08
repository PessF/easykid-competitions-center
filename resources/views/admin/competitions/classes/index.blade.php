<x-admin-layout>
    <x-slot name="title">จัดการรายการย่อย | {{ $competition->name }}</x-slot>

    <div x-data="{
        newClass: {
            robot_weight: ''
        },
    
        editClass: {
            id: '',
            name: '',
            entry_fee: '',
            min_members: '',
            max_members: '',
            max_teams: '',
            game_type_name: '',
            robot_weight: '',
            allowed_category: ''
        }
    }">

        {{-- ===== HEADER ===== --}}
        <div class="mb-5 md:mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3 md:gap-4">
                <a href="{{ route('admin.competitions.index') }}"
                    class="w-10 h-10 md:w-12 md:h-12 bg-[#121212] border border-white/5 rounded-xl shadow-sm flex items-center justify-center text-gray-400 hover:text-blue-400 hover:border-blue-500/30 transition-all shrink-0">
                    <i class="fas fa-arrow-left text-base md:text-lg"></i>
                </a>
                <div>
                    <div class="text-[9px] md:text-[10px] font-semibold text-blue-400 uppercase tracking-widest mb-0.5 md:mb-1 flex items-center gap-1.5">
                        <i class="fas fa-layer-group"></i> รายการย่อย (Classes)
                    </div>
                    <h1 class="text-lg md:text-2xl font-semibold text-white line-clamp-1 tracking-tight">
                        {{ $competition->name }}
                    </h1>
                </div>
            </div>

            <button @click="$dispatch('open-modal', 'add-class')"
                class="w-full md:w-auto group px-4 py-2.5 md:px-6 md:py-3 bg-blue-600 hover:bg-blue-500 active:bg-blue-700 text-white text-xs md:text-sm font-semibold rounded-xl transition-all shadow-md flex items-center justify-center gap-2">
                <i class="fas fa-plus-circle text-sm md:text-base transition-transform duration-200 group-hover:rotate-90"></i>
                เพิ่มรุ่นการแข่งขัน
            </button>
        </div>

        {{-- ===== GRID CARDS ===== --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 md:gap-6">
            @forelse($classes as $class)
                <div class="bg-[#121212] border border-white/5 rounded-xl md:rounded-2xl shadow-sm hover:shadow-[0_8px_30px_rgb(0,0,0,0.5)] hover:border-white/10 transition-all duration-300 p-4 md:p-6 flex flex-col group">

                    {{-- Card Header --}}
                    <div class="flex justify-between items-start mb-3 md:mb-5">
                        <div class="flex-1 pr-2 md:pr-4">
                            <span class="inline-flex items-center px-2 py-1 md:px-3 md:py-1 bg-blue-500/10 text-blue-400 text-[10px] md:text-xs font-semibold rounded-md md:rounded-lg border border-blue-500/20 mb-2">
                                <i class="fas fa-gamepad mr-1.5 opacity-70"></i> {{ $class->game_type_name }}
                            </span>
                            <h3 class="text-base md:text-xl font-semibold text-white leading-snug line-clamp-2" title="{{ $class->name }}">
                                {{ $class->name }}
                            </h3>
                        </div>
                    </div>

                    {{-- Age Categories Badge --}}
                    <div class="flex flex-wrap gap-1.5 md:gap-2 mb-4 md:mb-5">
                        @foreach ($class->allowed_categories as $cat)
                            <span class="px-2 py-1 md:px-2.5 md:py-1.5 bg-[#1a1a1a] border border-white/5 text-gray-300 rounded-md md:rounded-lg text-[10px] md:text-xs font-medium flex items-center shadow-sm">
                                <i class="fas fa-user-graduate text-[9px] md:text-[10px] text-gray-500 mr-1.5"></i>
                                {{ $cat['name'] }} <span class="text-gray-500 ml-1">({{ $cat['min_age'] }}-{{ $cat['max_age'] }} ปี)</span>
                            </span>
                        @endforeach
                    </div>

			{{-- Info Box --}}
			<div class="space-y-2 md:space-y-3 text-xs md:text-sm text-gray-400 mb-4 md:mb-6 bg-[#0a0a0a] p-3 md:p-4 rounded-lg md:rounded-xl border border-white/5 flex-1">

				<div class="flex justify-between items-center mt-1">
					<div class="flex items-center">
						<i class="fas fa-users w-4 md:w-5 text-center mr-1.5 md:mr-2 text-blue-500"></i>
						<span>สมาชิก (คน/ทีม):</span>
					</div>
					<span class="font-semibold text-white">
						{{ $class->min_members == $class->max_members ? $class->max_members : $class->min_members . '-' . $class->max_members }} คน
					</span>
				</div>

				{{-- โค้ดที่เพิ่มเข้ามาใหม่: จำนวนทีมที่รับ --}}
				<div class="flex justify-between items-center mt-1">
					<div class="flex items-center">
						<i class="fas fa-trophy w-4 md:w-5 text-center mr-1.5 md:mr-2 text-yellow-500"></i>
						<span>จำนวนทีมที่รับ:</span>
					</div>
					<span class="font-semibold text-yellow-400">
						{{ $class->max_teams ? number_format($class->max_teams) . ' ทีม' : 'ไม่จำกัด' }}
					</span>
				</div>
			</div>

                    {{-- Footer Actions --}}
                    <div class="mt-auto flex flex-col gap-2 md:gap-3">
                        @if ($class->rules_url)
                            <a href="{{ route('competitions.classes.rule', [$competition->id, $class->id]) }}"
                                target="_blank"
                                class="flex justify-center items-center py-2 md:py-2.5 text-xs md:text-sm font-semibold text-purple-400 bg-purple-500/10 hover:bg-purple-500/20 rounded-lg md:rounded-xl transition-colors border border-transparent hover:border-purple-500/30">
                                <i class="fas fa-file-pdf mr-1.5 md:mr-2 text-sm md:text-lg"></i>
                                กติกาการแข่งขัน
                            </a>
                        @endif

                        <div class="grid grid-cols-2 gap-2 md:gap-3 pt-3 md:pt-4 border-t border-white/5">
                            <button @click="editClass = {
                                    id: '{{ $class->id }}', 
                                    name: {{ Js::from($class->name) }}, 
                                    entry_fee: '{{ (float)$class->entry_fee }}',
                                    min_members: '{{ $class->min_members ?? 1 }}',
                                    max_members: '{{ $class->max_members }}', 
                                    max_teams: '{{ $class->max_teams ?? '' }}', 
                                    game_type_name: {{ Js::from($class->game_type_name) }},
                                    robot_weight: '{{ $class->robot_weight ?? '' }}', 
                                    allowed_category: '{{ !empty($class->allowed_categories) ? $class->allowed_categories[0]['name'] : '' }}'
                                }; $dispatch('open-modal', 'edit-class')"
                                class="py-2 md:py-2.5 text-blue-400 hover:text-blue-300 bg-blue-500/10 hover:bg-blue-500/20 rounded-lg md:rounded-xl text-[11px] md:text-sm font-semibold flex justify-center items-center transition-colors">
                                <i class="fas fa-pen mr-1.5 md:mr-2 text-[10px] md:text-[11px]"></i> แก้ไข
                            </button>

                            <button onclick="confirmDelete('{{ route('admin.competitions.classes.destroy', [$competition->id, $class->id]) }}', {{ Js::from($class->name) }})"
                                class="py-2 md:py-2.5 text-red-400 hover:text-red-300 bg-red-500/10 hover:bg-red-500/20 rounded-lg md:rounded-xl text-[11px] md:text-sm font-semibold flex justify-center items-center transition-colors">
                                <i class="fas fa-trash-alt mr-1.5 md:mr-2 text-[10px] md:text-[11px]"></i> ลบ
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 md:py-24 flex flex-col items-center justify-center border border-dashed border-white/10 rounded-xl md:rounded-2xl bg-[#121212] px-4 text-center">
                    <i class="fas fa-layer-group text-3xl md:text-4xl text-gray-600 mb-3 md:mb-4"></i>
                    <p class="text-base md:text-lg font-semibold text-gray-300 mb-1">ยังไม่มีรุ่นการแข่งขันในงานนี้</p>
                    <p class="text-xs md:text-sm font-normal text-gray-500 mb-4 md:mb-6">สร้างรายการแรกของคุณเพื่อเริ่มต้น</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination Links --}}
        <div class="mt-6 md:mt-8 flex justify-center">
            {{ $classes->links() }}
        </div>

        {{-- ===== MODAL: ADD ===== --}}
        <x-modal name="add-class" focusable maxWidth="3xl">
            <div class="bg-[#121212] flex flex-col h-[90vh] md:h-[85vh] overflow-hidden rounded-xl md:rounded-2xl border border-white/10">
                <div class="px-4 py-4 md:px-6 md:py-5 border-b border-white/5 flex items-center justify-between bg-[#0a0a0a]">
                    <h2 class="text-lg md:text-xl font-semibold text-white">เพิ่มรุ่นการแข่งขันใหม่</h2>
                    <button @click="$dispatch('close-modal', 'add-class')" class="text-gray-500 hover:text-white transition-colors p-2"><i class="fas fa-times"></i></button>
                </div>

                <form method="POST" action="{{ route('admin.competitions.classes.store', $competition->id) }}" enctype="multipart/form-data" class="flex flex-col flex-1 overflow-hidden">
                    @csrf
                    <div class="p-4 md:p-6 lg:p-8 overflow-y-auto custom-scrollbar flex-1 space-y-4 md:space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                            <div class="md:col-span-2 space-y-1.5 md:space-y-2">
                                <label class="block text-xs md:text-sm font-semibold text-gray-300">ชื่อรุ่นการแข่งขัน <span class="text-red-500">*</span></label>
                                <input type="text" name="name" required class="w-full px-3 py-2.5 md:px-4 md:py-3 text-sm border-white/10 bg-[#0f0f0f] text-white rounded-lg md:rounded-xl focus:ring-2 focus:ring-blue-500/20 outline-none placeholder-gray-600">
                            </div>

                            <div class="space-y-1.5 md:space-y-2">
                                <label class="block text-xs md:text-sm font-semibold text-gray-300">ประเภทเกมแข่งขัน <span class="text-red-500">*</span></label>
                                <select name="game_type_name" required class="w-full px-3 py-2.5 md:px-4 md:py-3 text-sm bg-[#0f0f0f] border-white/10 rounded-lg md:rounded-xl text-white focus:ring-2 focus:ring-blue-500/20 outline-none">
                                    <option value="">-- เลือกประเภท --</option>
                                    @foreach ($gameTypes as $game)
                                        <option value="{{ $game->name }}">{{ $game->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="space-y-1.5 md:space-y-2">
                                <label class="block text-xs md:text-sm font-semibold text-gray-300">พิกัดน้ำหนัก (Kg.)</label>
                                <input type="number" step="0.01" name="robot_weight" x-model="newClass.robot_weight" placeholder="เช่น 1.5 (ว่าง=ไม่จำกัด)" class="w-full px-3 py-2.5 md:px-4 md:py-3 text-sm bg-[#0f0f0f] border-white/10 rounded-lg md:rounded-xl text-white focus:ring-2 focus:ring-blue-500/20 outline-none">
                            </div>

                            <div class="space-y-1.5 md:space-y-2">
                                <label class="block text-xs md:text-sm font-semibold text-gray-300">ค่าสมัคร (บาท) <span class="text-red-500">*</span></label>
                                <input type="number" name="entry_fee" value="0" min="0" required class="w-full px-3 py-2.5 md:px-4 md:py-3 text-sm border-white/10 bg-[#0f0f0f] text-white rounded-lg md:rounded-xl focus:ring-2 focus:ring-blue-500/20 outline-none">
                            </div>

                            <div class="space-y-1.5 md:space-y-2">
                                <label class="block text-xs md:text-sm font-semibold text-gray-300">ไฟล์กติกา PDF</label>
                                <input type="file" name="rule_pdf" accept="application/pdf" class="w-full text-xs md:text-sm text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-[#1a1a1a] file:border-white/10 file:text-purple-400 hover:file:bg-white/5 cursor-pointer transition-colors border border-white/5 rounded-lg md:rounded-xl bg-[#0f0f0f]">
                            </div>
                            
                            <div class="space-y-1.5 md:space-y-2">
                                <label class="block text-xs md:text-sm font-semibold text-gray-300">จำนวนสมาชิก (คน/ทีม) <span class="text-red-500">*</span></label>
                                <div class="flex items-center gap-2">
                                    <input type="number" name="min_members" value="1" min="1" placeholder="ขั้นต่ำ" required class="w-full px-3 py-2.5 md:px-4 md:py-3 text-sm border-white/10 bg-[#0f0f0f] text-white rounded-lg md:rounded-xl focus:ring-2 focus:ring-blue-500/20 outline-none placeholder-gray-600">
                                    <span class="text-gray-500 font-bold">-</span>
                                    <input type="number" name="max_members" value="1" min="1" placeholder="สูงสุด" required class="w-full px-3 py-2.5 md:px-4 md:py-3 text-sm border-white/10 bg-[#0f0f0f] text-white rounded-lg md:rounded-xl focus:ring-2 focus:ring-blue-500/20 outline-none placeholder-gray-600">
                                </div>
                            </div>

                            <div class="space-y-1.5 md:space-y-2">
                                <label class="block text-xs md:text-sm font-semibold text-gray-300">จำนวนทีมที่รับ</label>
                                <input type="number" name="max_teams" min="1" placeholder="ไม่จำกัด" class="w-full px-3 py-2.5 md:px-4 md:py-3 text-sm border-white/10 bg-[#0f0f0f] text-white rounded-lg md:rounded-xl focus:ring-2 focus:ring-blue-500/20 outline-none placeholder-gray-600">
                            </div>

                            <div class="md:col-span-2 space-y-2 md:space-y-3 pt-2">
                                <label class="block text-xs md:text-sm font-semibold text-gray-300">หมวดหมู่อายุที่ลงแข่งได้ <span class="text-red-500">*</span></label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2 md:gap-3" x-data="{ selectedAge: '' }">
                                    @foreach ($categories as $category)
                                        <label class="flex items-center p-2.5 md:p-3 border rounded-lg md:rounded-xl cursor-pointer hover:bg-white/5 transition-all"
                                               :class="selectedAge === '{{ $category->name }}' ? 'border-blue-500 bg-blue-500/10' : 'border-white/5 bg-[#0a0a0a]'">
                                            <input type="radio" name="allowed_category" value="{{ $category->name }}" required x-model="selectedAge" class="w-3.5 h-3.5 md:w-4 md:h-4 text-blue-600 bg-[#121212] border-white/10 mr-2 md:mr-3 focus:ring-blue-500">
                                            <div class="flex flex-col">
                                                <span class="text-xs md:text-sm font-bold text-white">{{ $category->name }}</span>
                                                <span class="text-[9px] md:text-[10px] text-gray-500">{{ $category->min_age }}-{{ $category->max_age }} ปี</span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 py-3 md:px-6 md:py-4 bg-[#0a0a0a] border-t border-white/5 flex flex-col-reverse sm:flex-row justify-end gap-2 md:gap-3 shrink-0">
                        <button type="button" @click="$dispatch('close-modal', 'add-class')" class="w-full sm:w-auto px-4 py-2 md:px-6 md:py-2.5 bg-[#1a1a1a] hover:bg-white/5 border border-white/10 rounded-lg md:rounded-xl text-sm font-semibold text-gray-300 transition-colors">ยกเลิก</button>
                        <button type="submit" class="w-full sm:w-auto px-4 py-2 md:px-8 md:py-2.5 bg-blue-600 hover:bg-blue-500 text-white rounded-lg md:rounded-xl text-sm font-semibold shadow-md transition-colors">บันทึกข้อมูล</button>
                    </div>
                </form>
            </div>
        </x-modal>

        {{-- ===== MODAL: EDIT ===== --}}
        <x-modal name="edit-class" focusable maxWidth="3xl">
            <div class="bg-[#121212] flex flex-col h-[90vh] md:h-[85vh] overflow-hidden rounded-xl md:rounded-2xl border border-white/10">
                <div class="px-4 py-4 md:px-6 md:py-5 border-b border-white/5 flex items-center justify-between bg-[#0a0a0a] shrink-0">
                    <h2 class="text-lg md:text-xl font-semibold text-white">แก้ไขรุ่นการแข่งขัน</h2>
                    <button @click="$dispatch('close-modal', 'edit-class')" class="text-gray-500 hover:text-white transition-colors p-2"><i class="fas fa-times"></i></button>
                </div>

                <form method="POST" :action="`{{ url('/admin/competitions/' . $competition->id . '/classes') }}/${editClass.id}`" enctype="multipart/form-data" class="flex flex-col flex-1 overflow-hidden">
                    @csrf @method('PUT')
                    <div class="p-4 md:p-6 lg:p-8 overflow-y-auto custom-scrollbar flex-1 space-y-4 md:space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                            <div class="md:col-span-2 space-y-1.5 md:space-y-2">
                                <label class="block text-xs md:text-sm font-semibold text-gray-300">ชื่อรุ่นการแข่งขัน <span class="text-red-500">*</span></label>
                                <input type="text" name="name" x-model="editClass.name" required class="w-full px-3 py-2.5 md:px-4 md:py-3 text-sm border-white/10 bg-[#0f0f0f] text-white rounded-lg md:rounded-xl focus:ring-2 focus:ring-blue-500/20 outline-none">
                            </div>

                            <div class="space-y-1.5 md:space-y-2">
                                <label class="block text-xs md:text-sm font-semibold text-gray-300">ประเภทเกมแข่งขัน <span class="text-red-500">*</span></label>
                                <select x-model="editClass.game_type_name" name="game_type_name" required class="w-full px-3 py-2.5 md:px-4 md:py-3 text-sm bg-[#0f0f0f] border-white/10 rounded-lg md:rounded-xl text-white focus:ring-2 focus:ring-blue-500/20 outline-none">
                                    @foreach ($gameTypes as $game)
                                        <option value="{{ $game->name }}">{{ $game->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="space-y-1.5 md:space-y-2">
                                <label class="block text-xs md:text-sm font-semibold text-gray-300">พิกัดน้ำหนัก (Kg.)</label>
                                <input type="number" step="0.01" name="robot_weight" x-model="editClass.robot_weight" placeholder="เช่น 1.5" class="w-full px-3 py-2.5 md:px-4 md:py-3 text-sm bg-[#0f0f0f] border-white/10 rounded-lg md:rounded-xl text-blue-400 font-semibold focus:ring-2 focus:ring-blue-500/20 outline-none">
                            </div>

                            <div class="space-y-1.5 md:space-y-2">
                                <label class="block text-xs md:text-sm font-semibold text-gray-300">ค่าสมัคร (บาท) <span class="text-red-500">*</span></label>
                                <input type="number" name="entry_fee" x-model="editClass.entry_fee" min="0" required class="w-full px-3 py-2.5 md:px-4 md:py-3 text-sm border-white/10 bg-[#0f0f0f] text-white rounded-lg md:rounded-xl focus:ring-2 focus:ring-blue-500/20 outline-none">
                            </div>

                            <div class="space-y-1.5 md:space-y-2">
                                <label class="block text-xs md:text-sm font-semibold text-gray-300">อัปเดตกติกา PDF</label>
                                <input type="file" name="rule_pdf" accept="application/pdf" class="w-full text-xs md:text-sm text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-[#1a1a1a] file:border-white/10 file:text-purple-400 hover:file:bg-white/5 cursor-pointer transition-colors border border-white/5 rounded-lg md:rounded-xl bg-[#0f0f0f]">
                            </div>
                            
                            <div class="space-y-1.5 md:space-y-2">
                                <label class="block text-xs md:text-sm font-semibold text-gray-300">จำนวนสมาชิก (คน/ทีม) <span class="text-red-500">*</span></label>
                                <div class="flex items-center gap-2">
                                    <input type="number" name="min_members" x-model="editClass.min_members" min="1" placeholder="ขั้นต่ำ" required class="w-full px-3 py-2.5 md:px-4 md:py-3 text-sm border-white/10 bg-[#0f0f0f] text-white rounded-lg md:rounded-xl focus:ring-2 focus:ring-blue-500/20 outline-none">
                                    <span class="text-gray-500 font-bold">-</span>
                                    <input type="number" name="max_members" x-model="editClass.max_members" min="1" placeholder="สูงสุด" required class="w-full px-3 py-2.5 md:px-4 md:py-3 text-sm border-white/10 bg-[#0f0f0f] text-white rounded-lg md:rounded-xl focus:ring-2 focus:ring-blue-500/20 outline-none">
                                </div>
                            </div>

                            <div class="space-y-1.5 md:space-y-2">
                                <label class="block text-xs md:text-sm font-semibold text-gray-300">จำนวนทีมที่รับ</label>
                                <input type="number" name="max_teams" x-model="editClass.max_teams" min="1" placeholder="ไม่จำกัด" class="w-full px-3 py-2.5 md:px-4 md:py-3 text-sm border-white/10 bg-[#0f0f0f] text-white rounded-lg md:rounded-xl focus:ring-2 focus:ring-blue-500/20 outline-none placeholder-gray-600">
                            </div>

                            <div class="md:col-span-2 space-y-2 md:space-y-3 pt-2">
                                <label class="block text-xs md:text-sm font-semibold text-gray-300">หมวดหมู่อายุ <span class="text-red-500">*</span></label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2 md:gap-3">
                                    @foreach ($categories as $category)
                                        <label class="flex items-center p-2.5 md:p-3 border rounded-lg md:rounded-xl cursor-pointer hover:bg-white/5 transition-all"
                                               :class="editClass.allowed_category === '{{ $category->name }}' ? 'border-blue-500 bg-blue-500/10' : 'border-white/5 bg-[#0a0a0a]'">
                                            <input type="radio" name="allowed_category" value="{{ $category->name }}" required x-model="editClass.allowed_category" class="w-3.5 h-3.5 md:w-4 md:h-4 text-blue-600 bg-[#121212] border-white/10 mr-2 md:mr-3 focus:ring-blue-500">
                                            <div class="flex flex-col">
                                                <span class="text-xs md:text-sm font-bold text-white">{{ $category->name }}</span>
                                                <span class="text-[9px] md:text-[10px] text-gray-500">{{ $category->min_age }}-{{ $category->max_age }} ปี</span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 py-3 md:px-6 md:py-4 bg-[#0a0a0a] border-t border-white/5 flex flex-col-reverse sm:flex-row justify-end gap-2 md:gap-3 shrink-0">
                        <button type="button" @click="$dispatch('close-modal', 'edit-class')" class="w-full sm:w-auto px-4 py-2 md:px-6 md:py-2.5 bg-[#1a1a1a] hover:bg-white/5 border border-white/10 rounded-lg md:rounded-xl text-sm font-semibold text-gray-300 transition-colors">ยกเลิก</button>
                        <button type="submit" class="w-full sm:w-auto px-4 py-2 md:px-8 md:py-2.5 bg-blue-600 hover:bg-blue-500 text-white rounded-lg md:rounded-xl text-sm font-semibold shadow-md transition-colors">บันทึกการแก้ไข</button>
                    </div>
                </form>
            </div>
        </x-modal>

    </div>

    <form id="delete-form" method="POST" class="hidden">@csrf @method('DELETE')</form>

</x-admin-layout>