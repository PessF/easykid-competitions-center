<x-admin-layout>
    <x-slot name="title">ตั้งค่าหมวดหมู่ | Category Settings</x-slot>

    {{-- เปิด x-data เพื่อเก็บ State สำหรับดึงข้อมูลเก่ามาใส่ฟอร์ม Edit --}}
    <div x-data="{ 
        editGameType: { id: '', name: '' },
        editCategory: { id: '', name: '', min_age: '', max_age: '' }
    }">

        <div class="mb-8 flex items-center">
            <div class="p-3 bg-white dark:bg-[#0f0f0f] border border-gray-100 dark:border-white/5 rounded-xl shadow-sm mr-4">
                <svg class="w-6 h-6 text-gray-900 dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">ตั้งค่าหมวดหมู่</h1>
                <p class="text-sm text-gray-500 mt-1">จัดการประเภทการแข่งขันและเกณฑ์อายุสำหรับนักแข่ง</p>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
            
            {{-- ส่วนที่ 1: ประเภทการแข่งขัน --}}
            <div class="xl:col-span-5 space-y-4">
                <div class="p-6 bg-white dark:bg-[#0f0f0f] border border-gray-100 dark:border-white/5 rounded-2xl shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white">ประเภทการแข่งขัน</h2>
                        </div>
                        <button @click="$dispatch('open-modal', 'add-game-type')" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold uppercase tracking-widest rounded-xl transition-all">
                            + เพิ่มประเภท
                        </button>
                    </div>

                    <div class="overflow-hidden">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="text-gray-400 text-xs uppercase tracking-wider border-b border-gray-100 dark:border-white/5">
                                    <th class="pb-3 font-medium">ชื่อประเภท</th>
                                    <th class="pb-3 font-medium text-right">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-white/5 text-sm">
                                @forelse($gameTypes as $type)
                                <tr class="group">
                                    <td class="py-4 font-medium dark:text-white">{{ $type->name }}</td>
                                    <td class="py-4 text-right">
                                        {{-- ปุ่มแก้ไข Game Type --}}
                                        <button @click="editGameType = { id: '{{ $type->id }}', name: '{{ $type->name }}' }; $dispatch('open-modal', 'edit-game-type')" 
                                            class="text-gray-400 hover:text-yellow-500 transition-colors mr-3">แก้ไข</button>
                                        {{-- ปุ่มลบ Game Type --}}
                                        <button onclick="confirmDelete('{{ route('admin.game-types.destroy', $type->id) }}', '{{ $type->name }}')" 
                                            class="text-gray-400 hover:text-red-500 transition-colors">ลบ</button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="py-8 text-center text-gray-500 italic">ยังไม่มีข้อมูลประเภทการแข่งขัน</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination for Game Types --}}
                    <div class="mt-4 flex justify-center">
                        {{ $gameTypes->appends(request()->except('game_page'))->links() }}
                    </div>
                </div>
            </div>

            {{-- ส่วนที่ 2: รุ่นอายุ (Categories) --}}
            <div class="xl:col-span-7 space-y-4">
                <div class="p-6 bg-white dark:bg-[#0f0f0f] border border-gray-100 dark:border-white/5 rounded-2xl shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13.732 4c-.76-1.01-1.93-1.42-3.232-1.42s-2.472.41-3.232 1.42" />
                            </svg>
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white">รุ่นอายุ (Categories)</h2>
                        </div>
                        <button @click="$dispatch('open-modal', 'add-category')" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold uppercase tracking-widest rounded-xl transition-all">
                            + เพิ่มรุ่นอายุ
                        </button>
                    </div>

                    <div class="overflow-hidden">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="text-gray-400 text-xs uppercase tracking-wider border-b border-gray-100 dark:border-white/5">
                                    <th class="pb-3 font-medium">ชื่อรุ่น</th>
                                    <th class="pb-3 font-medium text-center">เกณฑ์อายุ (ปี)</th>
                                    <th class="pb-3 font-medium text-right">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-white/5 text-sm">
                                @forelse($categories as $cat)
                                <tr class="group">
                                    <td class="py-4 font-medium dark:text-white">{{ $cat->name }}</td>
                                    <td class="py-4 text-center">
                                        <span class="px-3 py-1 bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 rounded-full text-xs font-bold">
                                            {{ $cat->min_age }} - {{ $cat->max_age }}
                                        </span>
                                    </td>
                                    <td class="py-4 text-right">
                                        {{-- ปุ่มแก้ไข Category --}}
                                        <button @click="editCategory = { id: '{{ $cat->id }}', name: '{{ $cat->name }}', min_age: '{{ $cat->min_age }}', max_age: '{{ $cat->max_age }}' }; $dispatch('open-modal', 'edit-category')" 
                                            class="text-gray-400 hover:text-yellow-500 transition-colors mr-3">แก้ไข</button>
                                        {{-- ปุ่มลบ Category --}}
                                        <button onclick="confirmDelete('{{ route('admin.categories.destroy', $cat->id) }}', '{{ $cat->name }}')" 
                                            class="text-gray-400 hover:text-red-500 transition-colors">ลบ</button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="py-8 text-center text-gray-500 italic">ยังไม่มีข้อมูลรุ่นอายุ</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination for Categories --}}
                    <div class="mt-4 flex justify-center">
                        {{ $categories->appends(request()->except('cat_page'))->links() }}
                    </div>
                </div>
            </div>

        </div>

        {{-- 1. Modal เพิ่มประเภท (ของเดิม) --}}
        <x-modal name="add-game-type" focusable>
            <div class="p-6 bg-white dark:bg-[#1a1a1a]">
                <form method="post" action="{{ route('admin.game-types.store') }}">
                    @csrf
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-6">
                        เพิ่มประเภทการแข่งขันใหม่
                    </h2>
                    
                    <div class="mb-6">
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ชื่อประเภท (เช่น Sumo, Line Tracing)</label>
                        <input type="text" id="name" name="name" 
                            class="w-full border-gray-300 dark:border-gray-700 dark:bg-[#0f0f0f] dark:text-white rounded-xl shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                            placeholder="กรอกชื่อประเภท...">
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 text-xs font-bold uppercase tracking-widest rounded-xl transition-all">
                            ยกเลิก
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold uppercase tracking-widest rounded-xl transition-all">
                            บันทึกข้อมูล
                        </button>
                    </div>
                </form>
            </div>
        </x-modal>

        {{-- 2. Modal เพิ่มรุ่นอายุ (ของเดิม) --}}
        <x-modal name="add-category" focusable>
            <div class="p-6 bg-white dark:bg-[#1a1a1a]">
                <form method="post" action="{{ route('admin.categories.store') }}">
                    @csrf
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-6">
                        เพิ่มรุ่นอายุใหม่
                    </h2>
                    
                    <div class="space-y-6">
                        <div>
                            <label for="cat_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ชื่อรุ่น (เช่น Junior, Senior)</label>
                            <input type="text" id="cat_name" name="name" 
                                class="w-full border-gray-300 dark:border-gray-700 dark:bg-[#0f0f0f] dark:text-white rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500" 
                                placeholder="กรอกชื่อรุ่น...">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="min_age" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">อายุขั้นต่ำ (ปี)</label>
                                <input type="number" id="min_age" name="min_age" 
                                    class="w-full border-gray-300 dark:border-gray-700 dark:bg-[#0f0f0f] dark:text-white rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500">
                            </div>
                            <div>
                                <label for="max_age" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">อายุสูงสุด (ปี)</label>
                                <input type="number" id="max_age" name="max_age" 
                                    class="w-full border-gray-300 dark:border-gray-700 dark:bg-[#0f0f0f] dark:text-white rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500">
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end space-x-3">
                        <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 text-xs font-bold uppercase tracking-widest rounded-xl transition-all">
                            ยกเลิก
                        </button>
                        <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-xs font-bold uppercase tracking-widest rounded-xl transition-all">
                            บันทึกข้อมูล
                        </button>
                    </div>
                </form>
            </div>
        </x-modal>


        {{-- 3. Modal แก้ไขประเภทการแข่งขัน --}}
        <x-modal name="edit-game-type" focusable>
            <div class="p-6 bg-white dark:bg-[#1a1a1a]">
                <form method="post" :action="`{{ url('admin/game-types') }}/${editGameType.id}`">
                    @csrf
                    @method('PUT')
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-6">
                        แก้ไขประเภทการแข่งขัน
                    </h2>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ชื่อประเภท</label>
                        <input type="text" name="name" x-model="editGameType.name" 
                            class="w-full border-gray-300 dark:border-gray-700 dark:bg-[#0f0f0f] dark:text-white rounded-xl shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 text-xs font-bold uppercase tracking-widest rounded-xl transition-all">
                            ยกเลิก
                        </button>
                        <button type="submit" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-bold uppercase tracking-widest rounded-xl transition-all">
                            บันทึกการแก้ไข
                        </button>
                    </div>
                </form>
            </div>
        </x-modal>

        {{-- 4. Modal แก้ไขรุ่นอายุ --}}
        <x-modal name="edit-category" focusable>
            <div class="p-6 bg-white dark:bg-[#1a1a1a]">
                <form method="post" :action="`{{ url('admin/categories') }}/${editCategory.id}`">
                    @csrf
                    @method('PUT')
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-6">
                        แก้ไขรุ่นอายุ
                    </h2>
                    
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ชื่อรุ่น</label>
                            <input type="text" name="name" x-model="editCategory.name" 
                                class="w-full border-gray-300 dark:border-gray-700 dark:bg-[#0f0f0f] dark:text-white rounded-xl shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">อายุขั้นต่ำ (ปี)</label>
                                <input type="number" name="min_age" x-model="editCategory.min_age" 
                                    class="w-full border-gray-300 dark:border-gray-700 dark:bg-[#0f0f0f] dark:text-white rounded-xl shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">อายุสูงสุด (ปี)</label>
                                <input type="number" name="max_age" x-model="editCategory.max_age" 
                                    class="w-full border-gray-300 dark:border-gray-700 dark:bg-[#0f0f0f] dark:text-white rounded-xl shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end space-x-3">
                        <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 text-xs font-bold uppercase tracking-widest rounded-xl transition-all">
                            ยกเลิก
                        </button>
                        <button type="submit" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-bold uppercase tracking-widest rounded-xl transition-all">
                            บันทึกการแก้ไข
                        </button>
                    </div>
                </form>
            </div>
        </x-modal>

    </div> {{-- ปิด x-data --}}

    {{-- ฟอร์มลับสำหรับการลบ (Hidden Form) --}}
    <form id="delete-form" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // ฟังก์ชันจัดการปุ่มลบ
        function confirmDelete(url, name) {
            Swal.fire({
                title: 'ยืนยันการลบ?',
                text: `คุณกำลังจะลบ "${name}" ข้อมูลจะไม่สามารถกู้คืนได้`,
                icon: 'warning',
                showCancelButton: true,
                background: document.documentElement.classList.contains('dark') ? '#1a1a1a' : '#fff',
                color: document.documentElement.classList.contains('dark') ? '#fff' : '#000',
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'ลบทิ้ง!',
                cancelButtonText: 'ยกเลิก',
                customClass: {
                    popup: 'rounded-2xl border border-gray-100 dark:border-white/10 shadow-xl',
                    confirmButton: 'rounded-xl px-4 py-2 font-bold tracking-widest',
                    cancelButton: 'rounded-xl px-4 py-2 font-bold tracking-widest'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('delete-form');
                    form.action = url;
                    form.submit();
                }
            });
        }

        // Script เดิมของคุณภูมิ
        document.addEventListener('DOMContentLoaded', function() {
            
            // 1. แจ้งเตือนเมื่อบันทึกสำเร็จ (สีเขียว)
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ!',
                    text: "{{ session('success') }}",
                    background: document.documentElement.classList.contains('dark') ? '#1a1a1a' : '#fff',
                    color: document.documentElement.classList.contains('dark') ? '#fff' : '#000',
                    showConfirmButton: false,
                    timer: 2000,
                    customClass: {
                        popup: 'rounded-2xl border border-gray-100 dark:border-white/10 shadow-xl'
                    }
                });
            @endif

            // 2. แจ้งเตือนเมื่อกรอกข้อมูลผิดพลาด (สีแดง)
            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด!',
                    html: `
                        <div class="text-left text-sm text-red-500 mt-2">
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    `,
                    background: document.documentElement.classList.contains('dark') ? '#1a1a1a' : '#fff',
                    color: document.documentElement.classList.contains('dark') ? '#fff' : '#000',
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'ตกลง',
                    customClass: {
                        popup: 'rounded-2xl border border-gray-100 dark:border-white/10 shadow-xl',
                        confirmButton: 'rounded-xl px-4 py-2 font-bold tracking-widest'
                    }
                });
            @endif

        });
    </script>

</x-admin-layout>