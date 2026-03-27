<x-user-layout>
    <main class="flex-1 flex flex-col w-full px-4 sm:px-6 lg:px-8 py-8 mx-auto max-w-7xl font-kanit"
        x-data="teamManager()">

        {{-- ===== HEADER ===== --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <div class="w-1.5 h-8 bg-blue-600 rounded-full"></div>
                    <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white tracking-tight">
                        จัดการทีมของฉัน</h1>
                </div>
                <p class="text-base font-normal text-gray-500 dark:text-gray-400 ml-4 pl-3 mt-1">
                    สร้างและจัดการรายชื่อสมาชิกในทีมให้พร้อมสำหรับการแข่งขัน
                </p>
            </div>
            <button @click="openCreateModal()"
                class="group inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-medium text-base rounded-xl transition-all duration-200 shadow-md hover:shadow-lg w-full sm:w-auto shrink-0">
                <i class="fas fa-plus-circle text-lg transition-transform duration-200 group-hover:rotate-90"></i>
                สร้างทีมใหม่
            </button>
        </div>

        {{-- ===== FLASH MESSAGES ===== --}}
        @if (session('success'))
            <div x-init="Swal.fire({ icon: 'success', title: 'สำเร็จ!', text: '{{ session('success') }}', confirmButtonColor: '#2563EB', customClass: { popup: 'rounded-2xl font-kanit' } })"></div>
        @endif
        @if (session('error'))
            <div x-init="Swal.fire({ icon: 'error', title: 'เกิดข้อผิดพลาด!', text: '{{ session('error') }}', confirmButtonColor: '#EF4444', customClass: { popup: 'rounded-2xl font-kanit' } })"></div>
        @endif

        {{-- ===== THE DATA LIST ===== --}}
        <div class="bg-white dark:bg-[#141414] border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm overflow-hidden">

            {{-- Table Header (แสดงเฉพาะบนจอ Desktop) --}}
            <div class="hidden md:grid grid-cols-12 gap-4 px-6 py-4 bg-gray-50/80 dark:bg-[#1a1a1a] border-b border-gray-200 dark:border-gray-800 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                <div class="col-span-4 lg:col-span-3">ข้อมูลทีม</div>
                <div class="col-span-3 lg:col-span-4">โรงเรียน/สถาบัน</div>
                <div class="col-span-3 pl-2">สมาชิกในทีม</div>
                <div class="col-span-2 text-right pr-2">จัดการ</div>
            </div>

            {{-- Table Body --}}
            <div class="divide-y divide-gray-100 dark:divide-gray-800/80">
                @forelse($teams as $team)
                    <div class="flex flex-col md:grid md:grid-cols-12 gap-4 px-5 sm:px-6 py-4 sm:py-5 md:items-center hover:bg-blue-50/40 dark:hover:bg-white/[0.02] transition-colors group">

                        {{-- 1. ข้อมูลทีม (มือถือ: โชว์ไอคอน, ชื่อทีม, ชื่อโรงเรียน และป้ายจำนวนคน) --}}
                        <div class="md:col-span-4 lg:col-span-3 flex items-center justify-between md:justify-start gap-4">
                            <div class="flex items-center gap-3.5 min-w-0">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border border-blue-100 dark:border-blue-800/30 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                    <i class="fas fa-shield-alt text-blue-600 dark:text-blue-400 text-xl"></i>
                                </div>
                                <div class="min-w-0">
                                    <h3 class="text-base font-bold text-gray-900 dark:text-white leading-tight group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors truncate" title="{{ $team->name }}">
                                        {{ $team->name }}
                                    </h3>
                                    {{-- ชื่อโรงเรียน (แสดงซ้อนใต้ชื่อทีมเฉพาะมือถือ) --}}
                                    <div class="md:hidden flex items-center gap-1.5 mt-1 text-xs font-medium text-gray-500 dark:text-gray-400">
                                        <i class="fas fa-school"></i> <span class="truncate">{{ $team->school_name }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Badge สมาชิก (แสดงดันขวาสุดเฉพาะมือถือ) --}}
                            <div class="md:hidden shrink-0">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-100 dark:bg-[#222] border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 text-xs font-semibold rounded-lg">
                                    <i class="fas fa-users text-blue-500"></i> {{ $team->members_count }}
                                </span>
                            </div>
                        </div>

                        {{-- 2. ชื่อโรงเรียน (เฉพาะ Desktop - เป็นคอลัมน์แยก) --}}
                        <div class="hidden md:flex md:col-span-3 lg:col-span-4 items-center text-sm font-medium text-gray-600 dark:text-gray-400 min-w-0">
                            <i class="fas fa-school mr-2 opacity-60 shrink-0"></i>
                            <span class="truncate" title="{{ $team->school_name }}">{{ $team->school_name }}</span>
                        </div>

                        {{-- 3. จำนวนสมาชิก & Avatars (เฉพาะ Desktop) --}}
                        <div class="hidden md:flex md:col-span-3 items-center gap-3">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-100 dark:bg-[#222] border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 text-xs font-semibold rounded-lg shrink-0">
                                <i class="fas fa-users text-blue-500"></i> {{ $team->members_count }} คน
                            </span>
                            
                            {{-- Avatars ซ้อนกัน --}}
                            <div class="flex -space-x-2 shrink-0">
                                @for ($i = 0; $i < min($team->members_count, 3); $i++)
                                    <div class="w-7 h-7 rounded-full bg-gray-200 dark:bg-gray-700 border-2 border-white dark:border-[#141414] flex items-center justify-center relative z-[{{ 3 - $i }}]">
                                        <i class="fas fa-user text-[10px] text-gray-400 dark:text-gray-500"></i>
                                    </div>
                                @endfor
                                @if ($team->members_count > 3)
                                    <div class="w-7 h-7 rounded-full bg-blue-50 dark:bg-blue-900/40 border-2 border-white dark:border-[#141414] flex items-center justify-center relative z-0">
                                        <span class="text-[9px] font-bold text-blue-600 dark:text-blue-400">+{{ $team->members_count - 3 }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- 4. ปุ่ม Actions (Mobile = ขยายเต็มจอ, Desktop = ชิดขวา) --}}
                        <div class="md:col-span-2 flex items-center justify-end gap-2 mt-1 md:mt-0 pt-4 md:pt-0 border-t md:border-t-0 border-gray-100 dark:border-gray-800">
                            <button @click='openEditModal(@json($team->load("members")))'
                                class="flex-1 md:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 md:py-2 text-sm font-semibold text-blue-600 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-600 hover:text-white dark:hover:bg-blue-600 dark:text-blue-400 dark:hover:text-white rounded-xl transition-colors focus:outline-none">
                                <i class="fas fa-pen text-xs"></i> <span class="md:hidden xl:inline">แก้ไข</span>
                            </button>

                            <form action="{{ route('user.teams.destroy', $team->id) }}" method="POST"
                                onsubmit="return confirm('ยืนยันการลบทีมนี้? ข้อมูลสมาชิกในทีมจะถูกลบทั้งหมด')"
                                class="flex-1 md:flex-none">
                                @csrf @method('DELETE')
                                <button type="submit" title="ลบทีม"
                                    class="w-full md:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 md:py-2 text-sm font-semibold text-red-500 bg-red-50 dark:bg-red-900/10 hover:bg-red-500 hover:text-white dark:hover:bg-red-500 dark:hover:text-white rounded-xl transition-colors focus:outline-none">
                                    <i class="fas fa-trash-alt text-xs"></i> <span class="md:hidden">ลบ</span>
                                </button>
                            </form>
                        </div>

                    </div>
                @empty
                    {{-- Empty State กรณีไม่มีทีม --}}
                    <div class="flex flex-col items-center justify-center py-20 px-6 text-center">
                        <div class="w-20 h-20 bg-gray-50 dark:bg-[#111] border border-gray-100 dark:border-gray-800 rounded-full flex items-center justify-center mb-5">
                            <i class="fas fa-users-slash text-3xl text-gray-300 dark:text-gray-600"></i>
                        </div>
                        <p class="text-lg font-bold text-gray-900 dark:text-white mb-1.5">คุณยังไม่มีทีมในระบบ</p>
                        <p class="text-sm font-normal text-gray-500 dark:text-gray-400 mb-6">สร้างทีมแรกของคุณเพื่อเตรียมพร้อมสำหรับการแข่งขัน</p>
                        <button @click="openCreateModal()" class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm focus:outline-none">
                            <i class="fas fa-plus"></i> เริ่มสร้างทีมใหม่
                        </button>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- ===== THE OMNISCIENT MODAL ===== --}}
        <x-modal name="team-form-modal" maxWidth="4xl" focusable>
            <div
                class="max-h-[90vh] overflow-y-auto custom-scrollbar bg-gray-50 dark:bg-[#0a0a0a] rounded-2xl flex flex-col relative w-full">

                {{-- Modal Header (Sticky) --}}
                <div
                    class="px-6 py-5 border-b border-gray-200 dark:border-gray-800 bg-white dark:bg-[#1a1a1a] sticky top-0 z-50 shrink-0 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                                <i class="fas text-blue-600 dark:text-blue-400 text-lg"
                                    :class="isEdit ? 'fa-pen' : 'fa-plus'"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white leading-tight"
                                    x-text="isEdit ? 'แก้ไขข้อมูลทีม' : 'สร้างทีมแข่งขันใหม่'"></h2>
                                <p class="text-sm font-normal text-gray-500 dark:text-gray-400 mt-1"
                                    x-text="isEdit ? 'อัปเดตข้อมูลทีมและรายชื่อสมาชิก' : 'กรอกข้อมูลทีมและเพิ่มสมาชิกลูกทีม'">
                                </p>
                            </div>
                        </div>
                        <button @click="$dispatch('close-modal', 'team-form-modal')" type="button"
                            class="w-10 h-10 flex items-center justify-center text-gray-400 hover:text-red-500 bg-gray-50 hover:bg-red-50 dark:bg-gray-800 dark:hover:bg-red-900/20 rounded-xl transition-colors focus:outline-none">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                <form :action="formAction" method="POST" class="flex flex-col flex-1">
                    @csrf
                    <template x-if="isEdit">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div class="p-4 sm:p-6 space-y-6 flex-1">

                        {{-- Section 1: Team Info --}}
                        <div
                            class="bg-white dark:bg-[#1a1a1a] rounded-2xl border border-gray-200 dark:border-gray-800 overflow-hidden shadow-sm">
                            <div
                                class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center gap-2.5 bg-gray-50/50 dark:bg-white/[0.02]">
                                <i class="fas fa-info-circle text-blue-500 text-lg"></i>
                                <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200">ข้อมูลทีมพื้นฐาน</h3>
                            </div>
                            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-base font-medium text-gray-700 dark:text-gray-300">ชื่อทีม
                                        <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" x-model="teamData.name" required
                                        placeholder="เช่น หุ่นยนต์พิฆาต"
                                        class="w-full px-4 py-3.5 text-base font-normal rounded-xl border border-gray-300 dark:border-gray-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 bg-white dark:bg-black/20 text-gray-900 dark:text-white transition-all outline-none">
                                </div>
                                <div class="space-y-2">
                                    <label
                                        class="block text-base font-medium text-gray-700 dark:text-gray-300">โรงเรียน/สถาบัน
                                        <span class="text-red-500">*</span></label>
                                    <input type="text" name="school_name" x-model="teamData.school_name" required
                                        placeholder="เช่น โรงเรียนวิทยาศาสตร์"
                                        class="w-full px-4 py-3.5 text-base font-normal rounded-xl border border-gray-300 dark:border-gray-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 bg-white dark:bg-black/20 text-gray-900 dark:text-white transition-all outline-none">
                                </div>
                            </div>
                        </div>

                        {{-- Section 2: Members --}}
                        <div class="space-y-5">
                            <div class="flex items-center justify-between px-2 pt-2">
                                <div class="flex items-center gap-2.5">
                                    <i class="fas fa-users text-blue-500 text-xl"></i>
                                    <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200">รายชื่อสมาชิก</h3>
                                </div>
                                <span
                                    class="text-sm font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 px-3.5 py-1.5 rounded-lg border border-blue-100 dark:border-blue-800/50"
                                    x-text="`จำนวน ${members.length} คน`"></span>
                            </div>

                            <template x-for="(member, index) in members" :key="member.id">
                                <div
                                    class="bg-white dark:bg-[#1a1a1a] rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm relative group animate-fade-in">

                                    {{-- Member Card Header --}}
                                    <div
                                        class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-white/[0.02]">
                                        <div class="flex items-center gap-3.5">
                                            <div
                                                class="w-9 h-9 rounded-lg bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center">
                                                <span class="text-base font-bold text-blue-600 dark:text-blue-400"
                                                    x-text="index + 1"></span>
                                            </div>
                                            <span
                                                class="text-lg font-medium text-gray-800 dark:text-gray-200">ลูกทีมคนที่
                                                <span x-text="index + 1"></span></span>
                                        </div>
                                        <button type="button" @click="removeMember(index)"
                                            x-show="members.length > 1"
                                            class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-500 hover:text-red-500 bg-white dark:bg-[#1a1a1a] hover:bg-red-50 dark:hover:bg-red-900/20 border border-gray-200 dark:border-gray-700 hover:border-red-200 dark:hover:border-red-800/50 rounded-xl transition-all focus:outline-none shadow-sm">
                                            <i class="fas fa-trash-alt"></i> ลบ
                                        </button>
                                    </div>

                                    <div class="p-6 space-y-7">
                                        <input type="hidden" :name="`members[${index}][id]`"
                                            x-model="member.member_id">

                                        {{-- Row 1: TH Name --}}
                                        <div>
                                            <p
                                                class="text-sm font-medium text-blue-600 dark:text-blue-400 mb-3.5 bg-blue-50 dark:bg-blue-900/20 inline-block px-3 py-1 rounded-lg">
                                                <i class="fas fa-language mr-1.5"></i> ภาษาไทย
                                            </p>
                                            <div class="grid grid-cols-12 gap-5">
                                                <div class="col-span-12 md:col-span-3">
                                                    <label
                                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">คำนำหน้า
                                                        <span class="text-red-500">*</span></label>
                                                    <div x-data="{ open: false }" class="relative"
                                                        @click.outside="open = false">
                                                        <button @click="open = !open" type="button"
                                                            class="w-full px-4 py-3.5 bg-white dark:bg-black/20 border border-gray-300 dark:border-gray-700 rounded-xl text-base font-normal text-left flex items-center justify-between focus:ring-2 focus:ring-blue-500/20 outline-none">
                                                            <span x-text="member.prefix_th || 'เลือก...'"
                                                                class="text-gray-900 dark:text-white"></span>
                                                            <i class="fas fa-chevron-down text-sm text-gray-400"
                                                                :class="open && 'rotate-180'"></i>
                                                        </button>
                                                        <div x-show="open" x-transition.opacity style="display:none"
                                                            class="absolute z-[60] w-full mt-1 bg-white dark:bg-[#222] shadow-xl rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                                                            <template
                                                                x-for="opt in ['เด็กชาย', 'เด็กหญิง', 'นาย', 'นางสาว']">
                                                                <div @click="member.prefix_th = opt; open = false"
                                                                    class="px-5 py-3.5 text-base font-normal cursor-pointer hover:bg-blue-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300"
                                                                    x-text="opt"></div>
                                                            </template>
                                                        </div>
                                                        <input type="hidden" :name="`members[${index}][prefix_th]`"
                                                            x-model="member.prefix_th">
                                                    </div>
                                                </div>
                                                <div class="col-span-12 sm:col-span-6 md:col-span-4">
                                                    <label
                                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ชื่อจริง
                                                        <span class="text-red-500">*</span></label>
                                                    <input type="text" :name="`members[${index}][first_name_th]`"
                                                        x-model="member.first_name_th" required
                                                        class="w-full px-4 py-3.5 text-base font-normal rounded-xl border border-gray-300 dark:border-gray-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 bg-white dark:bg-black/20 text-gray-900 dark:text-white outline-none transition-all">
                                                </div>
                                                <div class="col-span-12 sm:col-span-6 md:col-span-5">
                                                    <label
                                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">นามสกุล
                                                        <span class="text-red-500">*</span></label>
                                                    <input type="text" :name="`members[${index}][last_name_th]`"
                                                        x-model="member.last_name_th" required
                                                        class="w-full px-4 py-3.5 text-base font-normal rounded-xl border border-gray-300 dark:border-gray-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 bg-white dark:bg-black/20 text-gray-900 dark:text-white outline-none transition-all">
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Row 2: EN Name --}}
                                        <div>
                                            <p
                                                class="text-sm font-medium text-indigo-600 dark:text-indigo-400 mb-3.5 bg-indigo-50 dark:bg-indigo-900/20 inline-block px-3 py-1 rounded-lg">
                                                <i class="fas fa-globe-americas mr-1.5"></i> English
                                            </p>
                                            <div class="grid grid-cols-12 gap-5">
                                                <div class="col-span-12 md:col-span-3">
                                                    <label
                                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Prefix
                                                        <span class="text-red-500">*</span></label>
                                                    <div x-data="{ open: false }" class="relative"
                                                        @click.outside="open = false">
                                                        <button @click="open = !open" type="button"
                                                            class="w-full px-4 py-3.5 bg-white dark:bg-black/20 border border-gray-300 dark:border-gray-700 rounded-xl text-base font-normal text-left flex items-center justify-between focus:ring-2 focus:ring-blue-500/20 outline-none">
                                                            <span x-text="member.prefix_en || 'Select...'"
                                                                class="text-gray-900 dark:text-white"></span>
                                                            <i class="fas fa-chevron-down text-sm text-gray-400"
                                                                :class="open && 'rotate-180'"></i>
                                                        </button>
                                                        <div x-show="open" x-transition.opacity style="display:none"
                                                            class="absolute z-[60] w-full mt-1 bg-white dark:bg-[#222] shadow-xl rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                                                            <template x-for="opt in ['Master', 'Miss', 'Mr.', 'Ms.']">
                                                                <div @click="member.prefix_en = opt; open = false"
                                                                    class="px-5 py-3.5 text-base font-normal cursor-pointer hover:bg-blue-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300"
                                                                    x-text="opt"></div>
                                                            </template>
                                                        </div>
                                                        <input type="hidden" :name="`members[${index}][prefix_en]`"
                                                            x-model="member.prefix_en">
                                                    </div>
                                                </div>
                                                <div class="col-span-12 sm:col-span-6 md:col-span-4">
                                                    <label
                                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">First
                                                        Name <span class="text-red-500">*</span></label>
                                                    <input type="text" :name="`members[${index}][first_name_en]`"
                                                        x-model="member.first_name_en" required
                                                        class="w-full px-4 py-3.5 text-base font-normal rounded-xl border border-gray-300 dark:border-gray-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 bg-white dark:bg-black/20 text-gray-900 dark:text-white outline-none transition-all">
                                                </div>
                                                <div class="col-span-12 sm:col-span-6 md:col-span-5">
                                                    <label
                                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Last
                                                        Name <span class="text-red-500">*</span></label>
                                                    <input type="text" :name="`members[${index}][last_name_en]`"
                                                        x-model="member.last_name_en" required
                                                        class="w-full px-4 py-3.5 text-base font-normal rounded-xl border border-gray-300 dark:border-gray-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 bg-white dark:bg-black/20 text-gray-900 dark:text-white outline-none transition-all">
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Row 3: DOB & Shirt --}}
                                        <div class="pt-3 border-t border-gray-100 dark:border-gray-800">
                                            <div class="grid grid-cols-12 gap-5">
                                                <div class="col-span-12 sm:col-span-6">
                                                    {{-- 🚀 Gimmick: คำนวณอายุอัตโนมัติ --}}
                                                    <div class="flex justify-between items-end mb-2">
                                                        <label
                                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">วันเกิด
                                                            (ปี ค.ศ.) <span class="text-red-500">*</span></label>
                                                        <span x-show="member.birth_date" x-transition.opacity
                                                            class="text-xs font-semibold text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/20 px-2.5 py-1 rounded-md border border-green-200 dark:border-green-800/30 shadow-sm"
                                                            style="display: none;">
                                                            อายุ: <span
                                                                x-text="calculateAge(member.birth_date)"></span> ปี
                                                        </span>
                                                    </div>
                                                    <input type="date" :name="`members[${index}][birth_date]`"
                                                        x-model="member.birth_date" required
                                                        class="w-full px-4 py-3.5 text-base font-normal rounded-xl border border-gray-300 dark:border-gray-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 bg-white dark:bg-black/20 text-gray-900 dark:text-white outline-none transition-all [color-scheme:light] dark:[color-scheme:dark] cursor-pointer">
                                                </div>
                                                <div class="col-span-12 sm:col-span-6">
                                                    <label
                                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ไซส์เสื้อ
                                                        (Shirt Size) <span class="text-red-500">*</span></label>
                                                    <div x-data="{ open: false }" class="relative"
                                                        @click.outside="open = false">
                                                        <button @click="open = !open" type="button"
                                                            class="w-full px-4 py-3.5 bg-white dark:bg-black/20 border border-gray-300 dark:border-gray-700 rounded-xl text-base font-normal text-left flex items-center justify-between focus:ring-2 focus:ring-blue-500/20 outline-none">
                                                            <span x-text="member.shirt_size || 'เลือกไซส์'"
                                                                class="text-gray-900 dark:text-white"></span>
                                                            <i class="fas fa-chevron-down text-sm text-gray-400"
                                                                :class="open && 'rotate-180'"></i>
                                                        </button>
                                                        <div x-show="open" x-transition.opacity style="display:none"
                                                            class="absolute z-[60] w-full mt-1 bg-white dark:bg-[#222] shadow-xl rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                                                            <div class="grid grid-cols-3 gap-2 p-3">
                                                                <template
                                                                    x-for="opt in ['JS', 'JM', 'JL', 'S', 'M', 'L', 'XL', '2XL', '3XL']">
                                                                    <div @click="member.shirt_size = opt; open = false"
                                                                        class="text-center py-2.5 text-base font-medium rounded-xl cursor-pointer transition-colors"
                                                                        :class="member.shirt_size === opt ?
                                                                            'bg-blue-600 text-white shadow-sm' :
                                                                            'bg-gray-50 dark:bg-gray-800 hover:bg-blue-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300'"
                                                                        x-text="opt"></div>
                                                                </template>
                                                            </div>
                                                        </div>
                                                        <input type="hidden" :name="`members[${index}][shirt_size]`"
                                                            x-model="member.shirt_size">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </template>

                            {{-- ปุ่มเพิ่มสมาชิก --}}
                            <div class="pt-4 pb-2">
                                <button type="button" @click="addMember()"
                                    class="w-full flex flex-col items-center justify-center gap-3 py-8 border-2 border-dashed border-blue-200 dark:border-blue-900/50 hover:border-blue-400 dark:hover:border-blue-700 bg-blue-50/50 dark:bg-blue-900/10 hover:bg-blue-50 dark:hover:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-3xl transition-all duration-200 group">
                                    <div
                                        class="w-12 h-12 rounded-full bg-white dark:bg-[#1a1a1a] shadow-sm border border-blue-100 dark:border-blue-800 flex items-center justify-center group-hover:scale-110 transition-transform">
                                        <i class="fas fa-user-plus text-xl"></i>
                                    </div>
                                    <span class="text-base font-medium">คลิกเพื่อเพิ่มสมาชิกลูกทีมคนต่อไป</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Modal Footer (Sticky) --}}
                    <div
                        class="flex flex-col-reverse sm:flex-row items-center justify-between gap-4 px-6 py-5 border-t border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-[#111] sticky bottom-0 z-50 shrink-0">
                        <p
                            class="text-sm font-normal text-gray-500 dark:text-gray-400 w-full sm:w-auto text-center sm:text-left">
                            <i class="fas fa-asterisk text-red-400 mr-1.5"></i> กรุณาตรวจสอบข้อมูลให้ครบถ้วนก่อนบันทึก
                            เพราะเราถือว่าคุณตรวจเช็คถี่ถ้วนแล้ว
                        </p>
                        <div class="flex flex-col-reverse sm:flex-row gap-3 w-full sm:w-auto">
                            <button type="button" @click="$dispatch('close-modal', 'team-form-modal')"
                                class="w-full sm:w-auto px-8 py-3.5 text-base font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-gray-700 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors focus:outline-none shadow-sm">
                                ยกเลิก
                            </button>
                            <button type="submit"
                                class="w-full sm:w-auto px-10 py-3.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-base font-medium rounded-xl shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-[#111]">
                                <i class="fas fa-save text-lg"></i>
                                <span x-text="isEdit ? 'บันทึกการแก้ไข' : 'บันทึกข้อมูลทีม'"></span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </x-modal>

    </main>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('teamManager', () => ({
                isEdit: false,
                formAction: '{{ route('user.teams.store') }}',
                teamData: {
                    name: '',
                    school_name: ''
                },
                members: [],

                getEmptyMember() {
                    return {
                        id: Date.now(),
                        member_id: null,
                        prefix_th: 'เด็กชาย',
                        first_name_th: '',
                        last_name_th: '',
                        prefix_en: 'Master',
                        first_name_en: '',
                        last_name_en: '',
                        birth_date: '',
                        shirt_size: 'M'
                    };
                },

                calculateAge(dateString) {
                    if (!dateString) return 0;
                    const today = new Date();
                    const birthDate = new Date(dateString);
                    let age = today.getFullYear() - birthDate.getFullYear();
                    const m = today.getMonth() - birthDate.getMonth();

                    // ถ้ายันไม่ถึงเดือนเกิด หรือ ถึงเดือนเกิดแล้วแต่วันที่ยังไม่ถึง ให้ลบอายุออก 1 ปี
                    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                        age--;
                    }
                    return age >= 0 ? age : 0;
                },

                openCreateModal() {
                    this.isEdit = false;
                    this.formAction = '{{ route('user.teams.store') }}';
                    this.teamData = {
                        name: '',
                        school_name: ''
                    };
                    this.members = [this.getEmptyMember()];
                    this.$dispatch('open-modal', 'team-form-modal');
                },

                openEditModal(team) {
                    this.isEdit = true;
                    this.formAction = '{{ route('user.teams.index') }}/' + team.id;
                    this.teamData = {
                        name: team.name,
                        school_name: team.school_name
                    };

                    if (team.members && team.members.length > 0) {
                        this.members = team.members.map(m => ({
                            id: m.id,
                            member_id: m.id,
                            prefix_th: m.prefix_th,
                            first_name_th: m.first_name_th,
                            last_name_th: m.last_name_th,
                            prefix_en: m.prefix_en,
                            first_name_en: m.first_name_en,
                            last_name_en: m.last_name_en,
                            birth_date: m.birth_date ? m.birth_date.split('T')[0] : '',
                            shirt_size: m.shirt_size
                        }));
                    } else {
                        this.members = [this.getEmptyMember()];
                    }
                    this.$dispatch('open-modal', 'team-form-modal');
                },

                addMember() {
                    this.members.push(this.getEmptyMember());
                    setTimeout(() => {
                        const el = document.querySelector('.custom-scrollbar');
                        if (el) el.scrollTop = el.scrollHeight;
                    }, 50);
                },

                removeMember(index) {
                    if (this.members.length > 1) this.members.splice(index, 1);
                }
            }))
        })
    </script>
</x-user-layout>
