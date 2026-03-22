<x-admin-layout>
    <x-slot name="title">จัดการหุ่นยนต์ | Robot Models</x-slot>

    <div x-data="{ 
        editRobot: { id: '', name: '', standard_weight: '', image_url: '' }
    }">
        <div class="mb-6 sm:mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center">
                <div class="p-2.5 sm:p-3 bg-white dark:bg-[#0f0f0f] border border-gray-100 dark:border-white/5 rounded-xl shadow-sm mr-3 sm:mr-4 shrink-0">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-gray-900 dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white leading-tight">คลังแม่แบบหุ่นยนต์</h1>
                    <p class="text-xs sm:text-sm text-gray-500 mt-0.5 sm:mt-1">จัดการสเปคมาตรฐานและรูปภาพของหุ่นยนต์</p>
                </div>
            </div>
            <button @click="$dispatch('open-modal', 'add-robot')" class="w-full sm:w-auto px-4 py-2.5 sm:py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold uppercase tracking-widest rounded-xl transition-all shadow-sm shadow-blue-500/30 flex items-center justify-center">
                <span class="mr-1 text-base leading-none">+</span> เพิ่มแม่แบบ
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
            @forelse($robotModels as $robot)
                <div class="bg-white dark:bg-[#0f0f0f] border border-gray-100 dark:border-white/5 rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden flex flex-col group">
                    
                    <div class="h-40 sm:h-48 bg-gray-50 dark:bg-white/5 relative flex items-center justify-center overflow-hidden border-b border-gray-100 dark:border-white/5">
                        @if($robot->image_url)
                            <img src="{{ route('admin.robot-models.image', $robot->id) }}" 
                                 alt="{{ $robot->name }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="text-gray-300 dark:text-gray-600 flex flex-col items-center">
                                <svg class="w-8 h-8 sm:w-10 sm:h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span class="text-[10px] sm:text-xs font-semibold uppercase tracking-widest">ไม่มีรูปภาพ</span>
                            </div>
                        @endif
                    </div>

                    <div class="p-4 sm:p-5 flex-1 flex flex-col">
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white leading-tight mb-2">{{ $robot->name }}</h3>
                        <div class="flex items-center text-xs sm:text-sm text-gray-500 dark:text-gray-400 mb-4 sm:mb-6 font-mono">
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 mr-1.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                            {{ $robot->standard_weight ?? '0.00' }} kg
                        </div>

                        <div class="mt-auto flex items-center justify-between pt-3 sm:pt-4 border-t border-gray-100 dark:border-white/5">
                            <button @click="editRobot = { id: '{{ $robot->id }}', name: '{{ addslashes($robot->name) }}', standard_weight: '{{ $robot->standard_weight }}' }; $dispatch('open-modal', 'edit-robot')" 
                                class="text-[10px] sm:text-xs font-bold uppercase tracking-widest text-yellow-500 hover:text-yellow-600 transition-colors flex items-center p-1 -ml-1">
                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                แก้ไข
                            </button>
                            <button onclick="confirmDelete('{{ route('admin.robot-models.destroy', $robot->id) }}', '{{ addslashes($robot->name) }}')" 
                                class="text-[10px] sm:text-xs font-bold uppercase tracking-widest text-red-500 hover:text-red-600 transition-colors flex items-center p-1 -mr-1">
                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                ลบ
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 sm:py-16 flex flex-col items-center justify-center bg-white dark:bg-[#0f0f0f] border border-gray-100 dark:border-white/5 rounded-2xl border-dashed mx-2 sm:mx-0">
                    <svg class="w-12 h-12 sm:w-16 sm:h-16 text-gray-300 dark:text-gray-600 mb-3 sm:mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    <p class="text-sm sm:text-base text-gray-500 dark:text-gray-400 font-medium">ยังไม่มีข้อมูลแม่แบบหุ่นยนต์</p>
                    <button @click="$dispatch('open-modal', 'add-robot')" class="mt-3 sm:mt-4 text-blue-500 hover:text-blue-600 text-xs sm:text-sm font-semibold">คลิกเพื่อเพิ่มแม่แบบแรก</button>
                </div>
            @endforelse
        </div>

        {{-- Modal: เพิ่มหุ่นยนต์ (ปรับ padding สำหรับมือถือ) --}}
        <x-modal name="add-robot" focusable>
            <div class="p-5 sm:p-6 bg-white dark:bg-[#1a1a1a]">
                <form method="POST" action="{{ route('admin.robot-models.store') }}" enctype="multipart/form-data">
                    @csrf
                    <h2 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white mb-4 sm:mb-6">เพิ่มแม่แบบหุ่นยนต์ใหม่</h2>
                    <div class="space-y-4 sm:space-y-6">
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 sm:mb-2">ชื่อรุ่นหุ่นยนต์ <span class="text-red-500">*</span></label>
                            <input type="text" name="name" required placeholder="เช่น Mega Sumo 3kg" class="w-full border-gray-300 dark:border-gray-700 dark:bg-[#0f0f0f] dark:text-white rounded-xl focus:ring-blue-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 sm:mb-2">น้ำหนักมาตรฐาน (kg)</label>
                            <input type="number" name="standard_weight" step="0.01" placeholder="เช่น 3.00" class="w-full border-gray-300 dark:border-gray-700 dark:bg-[#0f0f0f] dark:text-white rounded-xl focus:ring-blue-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 sm:mb-2">รูปภาพ (ไม่เกิน 2MB)</label>
                            <input type="file" name="image" accept="image/png, image/jpeg, image/jpg" class="w-full text-xs sm:text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 sm:file:py-2 sm:file:px-4 file:rounded-xl file:border-0 file:bg-blue-50 dark:file:bg-blue-900/20 dark:file:text-blue-400 font-semibold cursor-pointer">
                        </div>
                    </div>
                    <div class="mt-6 sm:mt-8 flex justify-end space-x-2 sm:space-x-3">
                        <button type="button" x-on:click="$dispatch('close')" class="px-3 sm:px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-xl text-[10px] sm:text-xs font-semibold uppercase tracking-widest transition-all">ยกเลิก</button>
                        <button type="submit" class="px-3 sm:px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-[10px] sm:text-xs font-semibold uppercase tracking-widest transition-all">บันทึกข้อมูล</button>
                    </div>
                </form>
            </div>
        </x-modal>

        {{-- Modal: แก้ไขหุ่นยนต์ (ปรับ padding สำหรับมือถือ) --}}
        <x-modal name="edit-robot" focusable>
            <div class="p-5 sm:p-6 bg-white dark:bg-[#1a1a1a]">
                <form method="POST" :action="`{{ url('admin/robot-models') }}/${editRobot.id}`" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <h2 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white mb-4 sm:mb-6">แก้ไขข้อมูลหุ่นยนต์</h2>
                    <div class="space-y-4 sm:space-y-6">
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 sm:mb-2">ชื่อรุ่นหุ่นยนต์ <span class="text-red-500">*</span></label>
                            <input type="text" name="name" x-model="editRobot.name" required class="w-full border-gray-300 dark:border-gray-700 dark:bg-[#0f0f0f] dark:text-white rounded-xl focus:ring-yellow-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 sm:mb-2">น้ำหนักมาตรฐาน (kg)</label>
                            <input type="number" name="standard_weight" x-model="editRobot.standard_weight" step="0.01" class="w-full border-gray-300 dark:border-gray-700 dark:bg-[#0f0f0f] dark:text-white rounded-xl focus:ring-yellow-500 text-sm">
                        </div>
                        <div class="p-3 sm:p-4 bg-yellow-50 dark:bg-yellow-500/5 rounded-xl border border-yellow-100 dark:border-yellow-500/10">
                            <label class="block text-xs sm:text-sm font-bold text-yellow-700 dark:text-yellow-500 mb-2">เปลี่ยนรูปภาพ (เลือกเมื่อต้องการเปลี่ยนเท่านั้น)</label>
                            <input type="file" name="image" accept="image/png, image/jpeg, image/jpg" class="w-full text-xs sm:text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 sm:file:py-2 sm:file:px-4 file:rounded-xl file:border-0 file:bg-white dark:file:bg-black/20 file:text-yellow-600 dark:file:text-yellow-500 font-semibold cursor-pointer shadow-sm">
                        </div>
                    </div>
                    <div class="mt-6 sm:mt-8 flex justify-end space-x-2 sm:space-x-3">
                        <button type="button" x-on:click="$dispatch('close')" class="px-3 sm:px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-xl text-[10px] sm:text-xs font-semibold uppercase tracking-widest transition-all">ยกเลิก</button>
                        <button type="submit" class="px-3 sm:px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-xl text-[10px] sm:text-xs font-semibold uppercase tracking-widest transition-all shadow-sm shadow-yellow-500/30">บันทึกการแก้ไข</button>
                    </div>
                </form>
            </div>
        </x-modal>
    </div>

    <form id="delete-form" method="POST" class="hidden">@csrf @method('DELETE')</form>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // สคริปต์ SweetAlert เดิมคงไว้ได้เลยครับ ทำงานได้ดีเยี่ยมอยู่แล้ว
        function confirmDelete(url, name) { ... }
        document.addEventListener('DOMContentLoaded', function() { ... });
    </script>
</x-admin-layout>