<x-admin-layout>
    <x-slot name="title">ตั้งค่าหมวดหมู่ | Category Settings</x-slot>

    {{-- x-data สำหรับจัดการ State ของฟอร์ม Edit --}}
    <div x-data="{ 
        editGameType: { id: '', name: '' },
        editCategory: { id: '', name: '', min_age: '', max_age: '' }
    }">

        {{-- Page Header --}}
        <div class="mb-8 flex items-center">
            <div class="p-2.5 bg-white dark:bg-[#0f0f0f] border border-gray-100 dark:border-white/5 rounded-xl shadow-sm mr-4 shrink-0">
                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-normal text-gray-900 dark:text-white leading-tight">ตั้งค่าหมวดหมู่</h1>
                <p class="text-sm text-gray-500 mt-1 font-normal">จัดการประเภทการแข่งขันและเกณฑ์อายุสำหรับนักแข่ง</p>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
            
            {{-- ส่วนที่ 1: ประเภทการแข่งขัน --}}
            <div class="xl:col-span-5">
                <div class="bg-white dark:bg-[#121212] border border-gray-100 dark:border-white/5 rounded-2xl shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-gray-50 dark:border-white/5 flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-gamepad text-blue-500 mr-2.5 opacity-70"></i>
                            <h2 class="text-base font-medium text-gray-900 dark:text-white">ประเภทการแข่งขัน</h2>
                        </div>
                        <button @click="$dispatch('open-modal', 'add-game-type')" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-normal uppercase tracking-widest rounded-xl transition-all">
                            + เพิ่มประเภท
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="text-gray-400 text-[11px] uppercase tracking-wider bg-gray-50/50 dark:bg-black/20">
                                    <th class="px-6 py-3 font-normal">ชื่อประเภท</th>
                                    <th class="px-6 py-3 font-normal text-right">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-white/5 text-sm">
                                @forelse($gameTypes as $type)
                                <tr class="group hover:bg-gray-50/50 dark:hover:bg-white/[0.01] transition-colors">
                                    <td class="px-6 py-4 font-normal text-gray-900 dark:text-white">{{ $type->name }}</td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap">
                                        <button @click="editGameType = { id: '{{ $type->id }}', name: '{{ addslashes($type->name) }}' }; $dispatch('open-modal', 'edit-game-type')" 
                                            class="text-xs font-normal text-blue-600 dark:text-blue-400 hover:underline mr-4">แก้ไข</button>
                                        <button onclick="confirmDelete('{{ route('admin.game-types.destroy', $type->id) }}', '{{ addslashes($type->name) }}')" 
                                            class="text-xs font-normal text-gray-400 hover:text-red-500 transition-colors">ลบ</button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="px-6 py-10 text-center text-gray-400 font-normal italic text-xs">ยังไม่มีข้อมูลประเภทการแข่งขัน</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($gameTypes->hasPages())
                    <div class="p-4 border-t border-gray-50 dark:border-white/5">
                        {{ $gameTypes->appends(request()->except('game_page'))->links() }}
                    </div>
                    @endif
                </div>
            </div>

            {{-- ส่วนที่ 2: รุ่นอายุ (Categories) --}}
            <div class="xl:col-span-7">
                <div class="bg-white dark:bg-[#121212] border border-gray-100 dark:border-white/5 rounded-2xl shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-gray-50 dark:border-white/5 flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-user-graduate text-green-500 mr-2.5 opacity-70"></i>
                            <h2 class="text-base font-medium text-gray-900 dark:text-white">รุ่นอายุ (Categories)</h2>
                        </div>
                        <button @click="$dispatch('open-modal', 'add-category')" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-[11px] font-normal uppercase tracking-widest rounded-xl transition-all">
                            + เพิ่มรุ่นอายุ
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="text-gray-400 text-[11px] uppercase tracking-wider bg-gray-50/50 dark:bg-black/20">
                                    <th class="px-6 py-3 font-normal">ชื่อรุ่น</th>
                                    <th class="px-6 py-3 font-normal text-center">เกณฑ์อายุ (ปี)</th>
                                    <th class="px-6 py-3 font-normal text-right">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-white/5 text-sm">
                                @forelse($categories as $cat)
                                <tr class="group hover:bg-gray-50/50 dark:hover:bg-white/[0.01] transition-colors">
                                    <td class="px-6 py-4 font-normal text-gray-900 dark:text-white">{{ $cat->name }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex px-2.5 py-0.5 bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 rounded-lg text-xs font-normal border border-blue-100/50 dark:border-blue-500/20">
                                            {{ $cat->min_age }} - {{ $cat->max_age }} ปี
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap">
                                        <button @click="editCategory = { id: '{{ $cat->id }}', name: '{{ addslashes($cat->name) }}', min_age: '{{ $cat->min_age }}', max_age: '{{ $cat->max_age }}' }; $dispatch('open-modal', 'edit-category')" 
                                            class="text-xs font-normal text-blue-600 dark:text-blue-400 hover:underline mr-4">แก้ไข</button>
                                        <button onclick="confirmDelete('{{ route('admin.categories.destroy', $cat->id) }}', '{{ addslashes($cat->name) }}')" 
                                            class="text-xs font-normal text-gray-400 hover:text-red-500 transition-colors">ลบ</button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-10 text-center text-gray-400 font-normal italic text-xs">ยังไม่มีข้อมูลรุ่นอายุ</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($categories->hasPages())
                    <div class="p-4 border-t border-gray-50 dark:border-white/5">
                        {{ $categories->appends(request()->except('cat_page'))->links() }}
                    </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- 1. Modal เพิ่มประเภท --}}
        <x-modal name="add-game-type" focusable>
            <div class="p-6 bg-white dark:bg-[#1a1a1a]">
                <form method="post" action="{{ route('admin.game-types.store') }}">
                    @csrf
                    <h2 class="text-lg font-normal text-gray-900 dark:text-white mb-6">เพิ่มประเภทการแข่งขันใหม่</h2>
                    
                    <div class="mb-8">
                        <label class="block text-xs font-normal text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">ชื่อประเภท (เช่น Sumo, Line Tracing)</label>
                        <input type="text" name="name" required class="w-full border-gray-200 dark:border-gray-700 dark:bg-[#0f0f0f] dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm font-normal">
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" x-on:click="$dispatch('close')" class="px-5 py-2 text-xs font-normal text-gray-500 uppercase tracking-widest">ยกเลิก</button>
                        <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-normal uppercase tracking-widest rounded-xl transition-all shadow-sm">บันทึกข้อมูล</button>
                    </div>
                </form>
            </div>
        </x-modal>

        {{-- 2. Modal เพิ่มรุ่นอายุ --}}
        <x-modal name="add-category" focusable>
            <div class="p-6 bg-white dark:bg-[#1a1a1a]">
                <form method="post" action="{{ route('admin.categories.store') }}">
                    @csrf
                    <h2 class="text-lg font-normal text-gray-900 dark:text-white mb-6">เพิ่มรุ่นอายุใหม่</h2>
                    
                    <div class="space-y-6">
                        <div>
                            <label class="block text-xs font-normal text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">ชื่อรุ่น (เช่น Junior, Senior)</label>
                            <input type="text" name="name" required class="w-full border-gray-200 dark:border-gray-700 dark:bg-[#0f0f0f] dark:text-white rounded-xl focus:ring-2 focus:ring-green-500/20 focus:border-green-500 text-sm font-normal">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-normal text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">อายุขั้นต่ำ (ปี)</label>
                                <input type="number" name="min_age" required class="w-full border-gray-200 dark:border-gray-700 dark:bg-[#0f0f0f] dark:text-white rounded-xl focus:ring-2 focus:ring-green-500/20 focus:border-green-500 text-sm font-normal">
                            </div>
                            <div>
                                <label class="block text-xs font-normal text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">อายุสูงสุด (ปี)</label>
                                <input type="number" name="max_age" required class="w-full border-gray-200 dark:border-gray-700 dark:bg-[#0f0f0f] dark:text-white rounded-xl focus:ring-2 focus:ring-green-500/20 focus:border-green-500 text-sm font-normal">
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3">
                        <button type="button" x-on:click="$dispatch('close')" class="px-5 py-2 text-xs font-normal text-gray-500 uppercase tracking-widest">ยกเลิก</button>
                        <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white text-xs font-normal uppercase tracking-widest rounded-xl transition-all shadow-sm">บันทึกข้อมูล</button>
                    </div>
                </form>
            </div>
        </x-modal>


        {{-- 3. Modal แก้ไขประเภทการแข่งขัน --}}
        <x-modal name="edit-game-type" focusable>
            <div class="p-6 bg-white dark:bg-[#1a1a1a]">
                <form method="post" :action="`{{ url('admin/game-types') }}/${editGameType.id}`">
                    @csrf @method('PUT')
                    <h2 class="text-lg font-normal text-gray-900 dark:text-white mb-6">แก้ไขประเภทการแข่งขัน</h2>
                    
                    <div class="mb-8">
                        <label class="block text-xs font-normal text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">ชื่อประเภท</label>
                        <input type="text" name="name" x-model="editGameType.name" required class="w-full border-gray-200 dark:border-gray-700 dark:bg-[#0f0f0f] dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm font-normal">
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" x-on:click="$dispatch('close')" class="px-5 py-2 text-xs font-normal text-gray-500 uppercase tracking-widest">ยกเลิก</button>
                        <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-normal uppercase tracking-widest rounded-xl shadow-sm">บันทึกการแก้ไข</button>
                    </div>
                </form>
            </div>
        </x-modal>

        {{-- 4. Modal แก้ไขรุ่นอายุ --}}
        <x-modal name="edit-category" focusable>
            <div class="p-6 bg-white dark:bg-[#1a1a1a]">
                <form method="post" :action="`{{ url('admin/categories') }}/${editCategory.id}`">
                    @csrf @method('PUT')
                    <h2 class="text-lg font-normal text-gray-900 dark:text-white mb-6">แก้ไขรุ่นอายุ</h2>
                    
                    <div class="space-y-6">
                        <div>
                            <label class="block text-xs font-normal text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">ชื่อรุ่น</label>
                            <input type="text" name="name" x-model="editCategory.name" required class="w-full border-gray-200 dark:border-gray-700 dark:bg-[#0f0f0f] dark:text-white rounded-xl focus:ring-2 focus:ring-green-500/20 focus:border-green-500 text-sm font-normal">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-normal text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">อายุขั้นต่ำ (ปี)</label>
                                <input type="number" name="min_age" x-model="editCategory.min_age" required class="w-full border-gray-200 dark:border-gray-700 dark:bg-[#0f0f0f] dark:text-white rounded-xl focus:ring-2 focus:ring-green-500/20 focus:border-green-500 text-sm font-normal">
                            </div>
                            <div>
                                <label class="block text-xs font-normal text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">อายุสูงสุด (ปี)</label>
                                <input type="number" name="max_age" x-model="editCategory.max_age" required class="w-full border-gray-200 dark:border-gray-700 dark:bg-[#0f0f0f] dark:text-white rounded-xl focus:ring-2 focus:ring-green-500/20 focus:border-green-500 text-sm font-normal">
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3">
                        <button type="button" x-on:click="$dispatch('close')" class="px-5 py-2 text-xs font-normal text-gray-500 uppercase tracking-widest">ยกเลิก</button>
                        <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white text-xs font-normal uppercase tracking-widest rounded-xl shadow-sm">บันทึกการแก้ไข</button>
                    </div>
                </form>
            </div>
        </x-modal>

    </div> {{-- ปิด x-data --}}

    {{-- ฟอร์มลับสำหรับการลบ --}}
    <form id="delete-form" method="POST" class="hidden">
        @csrf @method('DELETE')
    </form>
</x-admin-layout>