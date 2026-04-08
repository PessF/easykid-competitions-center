<x-admin-layout>
    <x-slot name="title">จัดการหุ่นยนต์ | Robot Models</x-slot>

    <div x-data="{
        editRobot: { id: '', name: '', standard_weight: '', image_url: '' }
    }">
        {{-- Page Header --}}
        <div class="mb-6 sm:mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center">
                <div
                    class="p-2.5 bg-white dark:bg-[#0f0f0f] border border-gray-100 dark:border-white/5 rounded-xl shadow-sm mr-4 shrink-0">
                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-normal text-gray-900 dark:text-white leading-tight">คลังแม่แบบหุ่นยนต์</h1>
                    <p class="text-sm text-gray-500 mt-1 font-normal">จัดการสเปคมาตรฐานและรูปภาพอ้างอิงของหุ่นยนต์</p>
                </div>
            </div>
            <button @click="$dispatch('open-modal', 'add-robot')"
                class="w-full sm:w-auto px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-normal rounded-xl transition-all shadow-sm flex items-center justify-center">
                <i class="fas fa-plus mr-2 text-xs"></i> เพิ่มแม่แบบใหม่
            </button>
        </div>

        {{-- Robot Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($robotModels as $robot)
                <div
                    class="bg-white dark:bg-[#121212] border border-gray-100 dark:border-white/5 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden flex flex-col group">

                    {{-- Image Area --}}
                    <div
                        class="h-44 bg-gray-50 dark:bg-white/5 relative flex items-center justify-center overflow-hidden border-b border-gray-50 dark:border-white/5">
                        @if ($robot->image_url)
                            <img src="{{ route('admin.robot-models.image', $robot->id) }}" alt="{{ $robot->name }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                        @else
                            <div class="text-gray-300 dark:text-gray-700 flex flex-col items-center">
                                <svg class="w-10 h-10 mb-2 opacity-20" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="text-[10px] uppercase tracking-widest font-normal">No Image</span>
                            </div>
                        @endif
                    </div>

                    {{-- Content Area --}}
                    <div class="p-5 flex-1 flex flex-col">
                        <h3 class="text-base font-medium text-gray-900 dark:text-white leading-snug mb-1.5">
                            {{ $robot->name }}</h3>
                        <div class="flex items-center text-xs text-gray-500 dark:text-gray-400 font-normal">
                            <i class="fas fa-weight-hanging mr-2 opacity-50 text-[10px]"></i>
                            น้ำหนักมาตรฐาน: {{ $robot->standard_weight ?? '0.00' }} kg
                        </div>

                        {{-- Actions --}}
                        <div
                            class="mt-6 flex items-center justify-between pt-4 border-t border-gray-50 dark:border-white/5">
                            <button
                                @click="editRobot = { id: '{{ $robot->id }}', name: '{{ addslashes($robot->name) }}', standard_weight: '{{ $robot->standard_weight }}' }; $dispatch('open-modal', 'edit-robot')"
                                class="text-[11px] font-normal uppercase tracking-wider text-blue-600 dark:text-blue-400 hover:text-blue-700 flex items-center transition-colors">
                                <i class="far fa-edit mr-1.5"></i> แก้ไข
                            </button>
                            <button
                                onclick="confirmDelete('{{ route('admin.robot-models.destroy', $robot->id) }}', '{{ addslashes($robot->name) }}')"
                                class="text-[11px] font-normal uppercase tracking-wider text-gray-400 hover:text-red-500 flex items-center transition-colors">
                                <i class="far fa-trash-alt mr-1.5"></i> ลบ
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div
                    class="col-span-full py-20 flex flex-col items-center justify-center bg-white dark:bg-[#121212] border border-gray-100 dark:border-white/5 rounded-2xl border-dashed">
                    <div
                        class="w-16 h-16 rounded-full bg-gray-50 dark:bg-white/5 flex items-center justify-center mb-4">
                        <i class="fas fa-robot text-gray-200 dark:text-gray-700 text-2xl"></i>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-normal">ไม่พบข้อมูลแม่แบบหุ่นยนต์ในขณะนี้
                    </p>
                    <button @click="$dispatch('open-modal', 'add-robot')"
                        class="mt-4 text-blue-500 hover:underline text-xs font-normal">เพิ่มแม่แบบแรกของคุณ</button>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-8 flex justify-center">
            {{ $robotModels->links() }}
        </div>

        {{-- Modal: เพิ่มหุ่นยนต์ --}}
        <x-modal name="add-robot" focusable>
            <div class="p-6 bg-white dark:bg-[#1a1a1a]">
                <form method="POST" action="{{ route('admin.robot-models.store') }}" enctype="multipart/form-data"
                    x-data="{ fileName: '' }">
                    @csrf
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-normal text-gray-900 dark:text-white">เพิ่มแม่แบบหุ่นยนต์ใหม่</h2>
                        <button type="button" x-on:click="$dispatch('close')"
                            class="text-gray-400 hover:text-gray-500"><i class="fas fa-times"></i></button>
                    </div>

                    <div class="space-y-5">
                        <div>
                            <label
                                class="block text-xs font-normal text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">ชื่อรุ่นหุ่นยนต์
                                <span class="text-red-500">*</span></label>
                            <input type="text" name="name" required placeholder="ระบุชื่อรุ่น เช่น Sumo 3kg Auto"
                                class="w-full border-gray-200 dark:border-gray-700 dark:bg-[#0f0f0f] dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm font-normal transition-all">
                        </div>
                        <div>
                            <label
                                class="block text-xs font-normal text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">น้ำหนักมาตรฐาน
                                (kg)</label>
                            <input type="number" name="standard_weight" step="0.01" placeholder="0.00"
                                class="w-full border-gray-200 dark:border-gray-700 dark:bg-[#0f0f0f] dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm font-normal">
                        </div>

                        {{-- Custom File Input --}}
                        <div>
                            <label
                                class="block text-xs font-normal text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">รูปภาพประกอบ</label>
                            <div class="relative">
                                <input type="file" name="image" id="image_add" accept="image/*" class="hidden"
                                    @change="fileName = $event.target.files[0].name">
                                <label for="image_add"
                                    class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-200 dark:border-white/10 rounded-2xl cursor-pointer bg-gray-50/50 dark:bg-black/20 hover:bg-blue-50/50 dark:hover:bg-blue-500/5 hover:border-blue-300 dark:hover:border-blue-500/30 transition-all group">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <i
                                            class="fas fa-cloud-upload-alt text-gray-400 group-hover:text-blue-500 mb-2 text-xl transition-colors"></i>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 font-normal"
                                            x-show="!fileName">คลิกเพื่อเลือกไฟล์รูปภาพ</p>
                                        <p class="text-xs text-blue-600 dark:text-blue-400 font-medium truncate max-w-[250px]"
                                            x-show="fileName" x-text="fileName"></p>
                                        <p class="text-[10px] text-gray-400 mt-1" x-show="!fileName">PNG, JPG
                                            (แนะนำสัดส่วน 4:3)</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3">
                        <button type="button" x-on:click="$dispatch('close')"
                            class="px-5 py-2.5 text-xs font-normal text-gray-500 hover:text-gray-700 transition-all uppercase tracking-widest">ยกเลิก</button>
                        <button type="submit"
                            class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-normal uppercase tracking-widest shadow-sm transition-all">บันทึกข้อมูล</button>
                    </div>
                </form>
            </div>
        </x-modal>

        {{-- Modal: แก้ไขหุ่นยนต์ --}}
        <x-modal name="edit-robot" focusable>
            <div class="p-6 bg-white dark:bg-[#1a1a1a]">
                <form method="POST" :action="`{{ url('admin/robot-models') }}/${editRobot.id}`"
                    enctype="multipart/form-data" x-data="{ fileName: '' }">
                    @csrf @method('PUT')
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-normal text-gray-900 dark:text-white">แก้ไขข้อมูลแม่แบบ</h2>
                        <button type="button" x-on:click="$dispatch('close')"
                            class="text-gray-400 hover:text-gray-500"><i class="fas fa-times"></i></button>
                    </div>

                    <div class="space-y-5">
                        <div>
                            <label
                                class="block text-xs font-normal text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">ชื่อรุ่นหุ่นยนต์
                                <span class="text-red-500">*</span></label>
                            <input type="text" name="name" x-model="editRobot.name" required
                                class="w-full border-gray-200 dark:border-gray-700 dark:bg-[#0f0f0f] dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm font-normal">
                        </div>
                        <div>
                            <label
                                class="block text-xs font-normal text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">น้ำหนักมาตรฐาน
                                (kg)</label>
                            <input type="number" name="standard_weight" x-model="editRobot.standard_weight"
                                step="0.01"
                                class="w-full border-gray-200 dark:border-gray-700 dark:bg-[#0f0f0f] dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm font-normal">
                        </div>

                        {{-- Custom File Input สำหรับ Edit --}}
                        <div
                            class="p-4 bg-gray-50 dark:bg-black/20 rounded-2xl border border-gray-100 dark:border-white/5">
                            <label
                                class="block text-xs font-normal text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-3">เปลี่ยนรูปภาพใหม่</label>
                            <div class="relative">
                                <input type="file" name="image" id="image_edit" accept="image/*"
                                    class="hidden" @change="fileName = $event.target.files[0].name">
                                <label for="image_edit"
                                    class="flex items-center justify-between w-full p-3 bg-white dark:bg-[#121212] border border-gray-200 dark:border-white/10 rounded-xl cursor-pointer hover:border-blue-400 transition-all group">
                                    <div class="flex items-center overflow-hidden">
                                        <i
                                            class="fas fa-image text-gray-400 group-hover:text-blue-500 mr-3 transition-colors"></i>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-[200px]"
                                            x-text="fileName || 'เลือกไฟล์ใหม่...'"></span>
                                    </div>
                                    <span
                                        class="text-[10px] font-normal text-blue-600 uppercase tracking-widest shrink-0 ml-2">Browse</span>
                                </label>
                            </div>
                            <p class="mt-2 text-[10px] text-gray-400 font-normal italic">*
                                ปล่อยว่างไว้หากไม่ต้องการเปลี่ยนรูปเดิม</p>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3">
                        <button type="button" x-on:click="$dispatch('close')"
                            class="px-5 py-2.5 text-xs font-normal text-gray-500 hover:text-gray-700 transition-all uppercase tracking-widest">ยกเลิก</button>
                        <button type="submit"
                            class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-normal uppercase tracking-widest shadow-sm transition-all">บันทึกการแก้ไข</button>
                    </div>
                </form>
            </div>
        </x-modal>

        {{-- Modal: แก้ไขหุ่นยนต์ --}}
        <x-modal name="edit-robot" focusable>
            <div class="p-6 bg-white dark:bg-[#1a1a1a]">
                <form method="POST" :action="`{{ url('admin/robot-models') }}/${editRobot.id}`"
                    enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-normal text-gray-900 dark:text-white">แก้ไขข้อมูลแม่แบบ</h2>
                        <button type="button" x-on:click="$dispatch('close')"
                            class="text-gray-400 hover:text-gray-500"><i class="fas fa-times"></i></button>
                    </div>

                    <div class="space-y-5">
                        <div>
                            <label
                                class="block text-xs font-normal text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">ชื่อรุ่นหุ่นยนต์
                                <span class="text-red-500">*</span></label>
                            <input type="text" name="name" x-model="editRobot.name" required
                                class="w-full border-gray-200 dark:border-gray-700 dark:bg-[#0f0f0f] dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm font-normal">
                        </div>
                        <div>
                            <label
                                class="block text-xs font-normal text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">น้ำหนักมาตรฐาน
                                (kg)</label>
                            <input type="number" name="standard_weight" x-model="editRobot.standard_weight"
                                step="0.01"
                                class="w-full border-gray-200 dark:border-gray-700 dark:bg-[#0f0f0f] dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm font-normal">
                        </div>
                        <div
                            class="p-4 bg-blue-50/50 dark:bg-blue-500/5 rounded-xl border border-blue-100/50 dark:border-blue-500/10">
                            <label
                                class="block text-xs font-medium text-blue-700 dark:text-blue-400 mb-2 uppercase tracking-wider">เปลี่ยนรูปภาพใหม่</label>
                            <input type="file" name="image" accept="image/*"
                                class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-white dark:file:bg-black/20 file:text-blue-600 font-normal cursor-pointer">
                            <p class="mt-2 text-[10px] text-blue-500/70 font-normal italic">*
                                เลือกไฟล์เมื่อต้องการเปลี่ยนรูปภาพเดิมเท่านั้น</p>
                        </div>
                    </div>
                    <div class="mt-8 flex justify-end gap-3">
                        <button type="button" x-on:click="$dispatch('close')"
                            class="px-5 py-2.5 text-xs font-normal text-gray-500 hover:text-gray-700 transition-all uppercase tracking-widest">ยกเลิก</button>
                        <button type="submit"
                            class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-normal uppercase tracking-widest shadow-sm transition-all">บันทึกการแก้ไข</button>
                    </div>
                </form>
            </div>
        </x-modal>
    </div>

    <form id="delete-form" method="POST" class="hidden">@csrf @method('DELETE')</form>
</x-admin-layout>
