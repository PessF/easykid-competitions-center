<x-setup-layout>
    <div class="py-4 sm:py-12 bg-[#0a0a0a] min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div
                class="bg-[#121212] overflow-hidden shadow-2xl rounded-2xl border border-white/5 animate-in fade-in zoom-in duration-500 relative">

                {{-- ปุ่ม Logout --}}
                <div class="absolute top-4 right-4 sm:top-10 sm:right-10 z-10">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="flex items-center space-x-2 sm:space-x-3 text-gray-500 hover:text-red-400 transition-all duration-300 group p-1.5 sm:p-2 rounded-full hover:bg-white/5">
                            <span
                                class="text-xs sm:text-sm uppercase tracking-[0.25em] font-normal opacity-0 group-hover:opacity-100 transition-all transform translate-x-2 group-hover:translate-x-0 hidden sm:block">
                                {{ __('ออกจากระบบ') }}
                            </span>
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 transform group-hover:-translate-x-1 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>

                <div class="p-5 sm:p-12">
                    {{-- Header --}}
                    <div class="mb-8 sm:mb-12">
                        <h2 class="text-2xl sm:text-3xl font-normal text-white tracking-tight">
                            {{ __('ตั้งค่าข้อมูลส่วนตัว') }}
                        </h2>
                        <div class="h-1 sm:h-1.5 w-12 sm:w-16 bg-blue-500 mt-2 sm:mt-3 rounded-full"></div>
                        <p class="mt-3 sm:mt-5 text-sm sm:text-base text-gray-400 font-light leading-relaxed">
                            {{ __('กรุณาตรวจสอบข้อมูลให้ถูกต้อง เพื่อความปลอดภัยของบัญชีคุณ') }}
                        </p>
                    </div>

                    <form method="POST" action="{{ route('profile.setup.store') }}" enctype="multipart/form-data"
                        class="space-y-8 sm:space-y-10">
                        @csrf

                        {{-- Profile Picture Section --}}
                        <div
                            class="flex flex-col sm:flex-row items-center space-y-4 sm:space-y-0 sm:space-x-10 p-5 sm:p-8 bg-[#0a0a0a] rounded-2xl border border-white/5">
                            <div class="relative group cursor-pointer shrink-0"
                                onclick="document.getElementById('avatar').click()">
                                <img id="avatar-preview"
                                    src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&color=7F9CF5&background=EBF4FF' }}"
                                    class="w-24 h-24 sm:w-32 sm:h-32 rounded-full object-cover border-4 border-[#121212] shadow-xl transition-transform duration-300 group-hover:scale-105 bg-[#1a1a1a]">
                                <div
                                    class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-black/60 rounded-full duration-300">
                                    <svg class="w-6 h-6 sm:w-8 sm:h-8 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path d="M12 4v16m8-8H4" stroke-width="2" stroke-linecap="round" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 text-center sm:text-left">
                                <label
                                    class="block text-sm sm:text-base font-normal text-white mb-2 sm:mb-3">{{ __('รูปโปรไฟล์ (ไม่บังคับ)') }}</label>
                                <input type="file" name="avatar" id="avatar" accept="image/*"
                                    onchange="previewImage(event)" class="hidden">
                                <button type="button" onclick="document.getElementById('avatar').click()"
                                    class="text-xs sm:text-sm font-medium py-2 px-4 sm:py-2.5 sm:px-6 bg-white/5 text-gray-300 rounded-xl hover:bg-white/10 hover:text-white transition-all border border-white/10">
                                    <i class="fas fa-camera mr-1.5 sm:mr-2"></i>{{ __('เลือกรูปภาพ') }}
                                </button>
                                <p class="mt-2 sm:mt-3 text-[10px] sm:text-[11px] text-gray-500 uppercase tracking-widest">
                                    {{ __('ขนาดแนะนำ 500x500 px (ไม่เกิน 2MB)') }}</p>
                                <x-input-error :messages="$errors->get('avatar')" class="mt-2 text-red-400 text-xs" />
                            </div>
                        </div>

                        {{-- Name Sections (Thai & English) --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-12">
                            {{-- Thai Section --}}
                            <div class="space-y-4 sm:space-y-6">
                                <h3
                                    class="text-xs font-normal text-blue-400 uppercase tracking-[0.2em] flex items-center gap-2 border-b border-white/5 pb-2 sm:pb-3">
                                    <i class="fas fa-id-card"></i> {{ __('ข้อมูลภาษาไทย') }}
                                </h3>
                                <div class="grid grid-cols-12 gap-3 sm:gap-4">
                                    <div class="col-span-12 sm:col-span-4 relative">
                                        <label class="block font-medium text-xs text-gray-400 mb-1.5">{{ __('คำนำหน้า') }}</label>
                                        
                                        {{-- 🚀 Custom Dropdown: Prefix TH --}}
                                        <div x-data="{ 
                                            open: false, 
                                            selected: '{{ old('prefix_th', 'นาย') }}',
                                            options: [
                                                { value: 'นาย', label: 'นาย' },
                                                { value: 'นาง', label: 'นาง' },
                                                { value: 'นางสาว', label: 'นางสาว' },
                                                { value: 'เด็กหญิง', label: 'เด็กหญิง' },
                                                { value: 'เด็กชาย', label: 'เด็กชาย' }
                                            ],
                                            get selectedLabel() { return this.options.find(o => o.value === this.selected)?.label || 'เลือกคำนำหน้า'; }
                                        }" @click.outside="open = false" class="relative">
                                            <input type="hidden" name="prefix_th" x-model="selected">
                                            <button type="button" @click="open = !open" 
                                                class="w-full flex items-center justify-between bg-[#0a0a0a] border border-white/10 text-white rounded-xl text-sm py-2.5 sm:py-3 px-3 sm:px-4 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                                :class="open ? 'border-blue-500 ring-1 ring-blue-500' : ''">
                                                <span x-text="selectedLabel"></span>
                                                <svg :class="open ? 'rotate-180' : ''" class="w-3.5 h-3.5 text-gray-500 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                            </button>
                                            <div x-show="open" x-transition.opacity.duration.200ms style="display: none;" 
                                                class="absolute z-50 w-full mt-1.5 bg-[#1a1a1a] border border-white/10 rounded-xl shadow-xl py-1.5 overflow-hidden">
                                                <template x-for="option in options" :key="option.value">
                                                    <button type="button" @click="selected = option.value; open = false;" 
                                                        class="w-full text-left px-3 sm:px-4 py-2 sm:py-2.5 text-sm transition-colors flex items-center justify-between"
                                                        :class="selected === option.value ? 'bg-blue-500/10 text-blue-400 font-normal' : 'text-gray-300 hover:bg-white/5 hover:text-white'">
                                                        <span x-text="option.label"></span>
                                                        <i x-show="selected === option.value" class="fas fa-check text-blue-500 text-[10px]"></i>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-span-12 sm:col-span-8">
                                        <label for="first_name_th" class="block font-medium text-xs text-gray-400 mb-1.5">{{ __('ชื่อจริง') }}</label>
                                        <input id="first_name_th" name="first_name_th" type="text" 
                                            class="block w-full bg-[#0a0a0a] border-white/10 text-white rounded-xl text-sm py-2.5 sm:py-3 px-3 sm:px-4 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                            value="{{ old('first_name_th') }}" />
                                        <x-input-error :messages="$errors->get('first_name_th')" class="mt-1 text-red-400 text-xs" />
                                    </div>
                                    <div class="col-span-12">
                                        <label for="last_name_th" class="block font-medium text-xs text-gray-400 mb-1.5">{{ __('นามสกุล') }}</label>
                                        <input id="last_name_th" name="last_name_th" type="text" 
                                            class="block w-full bg-[#0a0a0a] border-white/10 text-white rounded-xl text-sm py-2.5 sm:py-3 px-3 sm:px-4 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                            value="{{ old('last_name_th') }}" />
                                        <x-input-error :messages="$errors->get('last_name_th')" class="mt-1 text-red-400 text-xs" />
                                    </div>
                                </div>
                            </div>

                            {{-- English Section --}}
                            <div class="space-y-4 sm:space-y-6">
                                <h3
                                    class="text-xs font-normal text-emerald-400 uppercase tracking-[0.2em] flex items-center gap-2 border-b border-white/5 pb-2 sm:pb-3">
                                    <i class="fas fa-globe"></i> {{ __('English Information') }}
                                </h3>
                                <div class="grid grid-cols-12 gap-3 sm:gap-4">
                                    <div class="col-span-12 sm:col-span-4 relative">
                                        <label class="block font-medium text-xs text-gray-400 mb-1.5">{{ __('Prefix') }}</label>
                                        
                                        {{-- 🚀 Custom Dropdown: Prefix EN --}}
                                        <div x-data="{ 
                                            open: false, 
                                            selected: '{{ old('prefix_en', 'Mr.') }}',
                                            options: [
                                                { value: 'Mr.', label: 'Mr.' },
                                                { value: 'Mrs.', label: 'Mrs.' },
                                                { value: 'Ms.', label: 'Ms.' },
                                                { value: 'Miss', label: 'Miss' },
                                                { value: 'Master', label: 'Master' }
                                            ],
                                            get selectedLabel() { return this.options.find(o => o.value === this.selected)?.label || 'Select Prefix'; }
                                        }" @click.outside="open = false" class="relative">
                                            <input type="hidden" name="prefix_en" x-model="selected">
                                            <button type="button" @click="open = !open" 
                                                class="w-full flex items-center justify-between bg-[#0a0a0a] border border-white/10 text-white rounded-xl text-sm py-2.5 sm:py-3 px-3 sm:px-4 focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                                                :class="open ? 'border-emerald-500 ring-1 ring-emerald-500' : ''">
                                                <span x-text="selectedLabel"></span>
                                                <svg :class="open ? 'rotate-180' : ''" class="w-3.5 h-3.5 text-gray-500 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                            </button>
                                            <div x-show="open" x-transition.opacity.duration.200ms style="display: none;" 
                                                class="absolute z-50 w-full mt-1.5 bg-[#1a1a1a] border border-white/10 rounded-xl shadow-xl py-1.5 overflow-hidden">
                                                <template x-for="option in options" :key="option.value">
                                                    <button type="button" @click="selected = option.value; open = false;" 
                                                        class="w-full text-left px-3 sm:px-4 py-2 sm:py-2.5 text-sm transition-colors flex items-center justify-between"
                                                        :class="selected === option.value ? 'bg-emerald-500/10 text-emerald-400 font-normal' : 'text-gray-300 hover:bg-white/5 hover:text-white'">
                                                        <span x-text="option.label"></span>
                                                        <i x-show="selected === option.value" class="fas fa-check text-emerald-500 text-[10px]"></i>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-span-12 sm:col-span-8">
                                        <label for="first_name_en" class="block font-medium text-xs text-gray-400 mb-1.5">{{ __('First Name') }}</label>
                                        <input id="first_name_en" name="first_name_en" type="text" 
                                            class="block w-full bg-[#0a0a0a] border-white/10 text-white rounded-xl text-sm py-2.5 sm:py-3 px-3 sm:px-4 focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                                            value="{{ old('first_name_en') }}" />
                                        <x-input-error :messages="$errors->get('first_name_en')" class="mt-1 text-red-400 text-xs" />
                                    </div>
                                    <div class="col-span-12">
                                        <label for="last_name_en" class="block font-medium text-xs text-gray-400 mb-1.5">{{ __('Last Name') }}</label>
                                        <input id="last_name_en" name="last_name_en" type="text" 
                                            class="block w-full bg-[#0a0a0a] border-white/10 text-white rounded-xl text-sm py-2.5 sm:py-3 px-3 sm:px-4 focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                                            value="{{ old('last_name_en') }}" />
                                        <x-input-error :messages="$errors->get('last_name_en')" class="mt-1 text-red-400 text-xs" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Additional Info & Password --}}
                        <div class="space-y-6 sm:space-y-8 pt-6 sm:pt-8 border-t border-white/5">
                            {{-- Row 1: Birthday & Phone --}}
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
                                <div class="sm:col-span-1">
                                    <label for="birthday" class="block font-medium text-xs text-gray-400 mb-1.5">{{ __('วัน/เดือน/ปี เกิด (ค.ศ.) - ไม่บังคับ') }}</label>
                                    <input name="birthday" id="birthday" type="date" value="{{ old('birthday') }}"
                                        class="block w-full rounded-xl border border-white/10 bg-[#0a0a0a] text-white text-sm px-3 sm:px-4 py-2.5 sm:py-3 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-colors [color-scheme:dark]" />
                                    <x-input-error :messages="$errors->get('birthday')" class="mt-1 text-red-400 text-xs" />
                                </div>
                                <div class="sm:col-span-1">
                                    <label for="phone_number" class="block font-medium text-xs text-gray-400 mb-1.5">{{ __('เบอร์โทรศัพท์') }}</label>
                                    <input id="phone_number" name="phone_number" type="text" 
                                        class="block w-full bg-[#0a0a0a] border-white/10 text-white rounded-xl text-sm px-3 sm:px-4 py-2.5 sm:py-3 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-colors placeholder-gray-600"
                                        placeholder="08XXXXXXXX" value="{{ old('phone_number') }}" />
                                    <x-input-error :messages="$errors->get('phone_number')" class="mt-1 text-red-400 text-xs" />
                                </div>
                                <div class="sm:col-span-1 relative">
                                    <label class="block font-medium text-xs text-gray-400 mb-1.5">{{ __('ไซส์เสื้อ (Shirt Size)') }}</label>
                                    
                                    {{-- 🚀 Custom Dropdown: Shirt Size --}}
                                    <div x-data="{ 
                                        open: false, 
                                        selected: '{{ old('shirt_size', '') }}',
                                        options: [
                                            { value: '', label: 'เลือกไซส์ (เว้นได้)' },
                                            { value: 'S', label: 'S (รอบอก 34-36 นิ้ว)' },
                                            { value: 'M', label: 'M (รอบอก 36-38 นิ้ว)' },
                                            { value: 'L', label: 'L (รอบอก 38-40 นิ้ว)' },
                                            { value: 'XL', label: 'XL (รอบอก 40-42 นิ้ว)' },
                                            { value: '2XL', label: '2XL (รอบอก 42-44 นิ้ว)' },
                                            { value: '3XL', label: '3XL (รอบอก 44-46 นิ้ว)' }
                                        ],
                                        get selectedLabel() { return this.options.find(o => o.value === this.selected)?.label || 'เลือกไซส์ (เว้นได้)'; }
                                    }" @click.outside="open = false" class="relative">
                                        <input type="hidden" name="shirt_size" x-model="selected">
                                        <button type="button" @click="open = !open" 
                                            class="w-full flex items-center justify-between bg-[#0a0a0a] border border-white/10 rounded-xl text-sm py-2.5 sm:py-3 px-3 sm:px-4 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                            :class="open ? 'border-blue-500 ring-1 ring-blue-500 text-white' : (selected === '' ? 'text-gray-500' : 'text-white')">
                                            <span x-text="selectedLabel" class="truncate pr-2"></span>
                                            <svg :class="open ? 'rotate-180' : ''" class="w-3.5 h-3.5 text-gray-500 transition-transform duration-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </button>
                                        <div x-show="open" x-transition.opacity.duration.200ms style="display: none;" 
                                            class="absolute z-50 w-full mt-1.5 bg-[#1a1a1a] border border-white/10 rounded-xl shadow-xl py-1.5 max-h-60 overflow-y-auto custom-scrollbar">
                                            <template x-for="option in options" :key="option.value">
                                                <button type="button" @click="selected = option.value; open = false;" 
                                                    class="w-full text-left px-3 sm:px-4 py-2 sm:py-2.5 text-sm transition-colors flex items-center justify-between"
                                                    :class="selected === option.value ? 'bg-blue-500/10 text-blue-400 font-normal' : 'text-gray-300 hover:bg-white/5 hover:text-white'">
                                                    <span x-text="option.label" class="truncate pr-2"></span>
                                                    <i x-show="selected !== '' && selected === option.value" class="fas fa-check text-blue-500 text-[10px] shrink-0"></i>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                    <x-input-error :messages="$errors->get('shirt_size')" class="mt-1 text-red-400 text-xs" />
                                </div>
                            </div>

                            {{-- Row 2: Password --}}
                            @if (is_null(auth()->user()->password))
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 animate-in slide-in-from-top-4 duration-700 p-5 sm:p-6 bg-[#0a0a0a] rounded-2xl border border-white/5">
                                    <div class="sm:col-span-2">
                                        <h3 class="text-xs font-normal text-yellow-500 uppercase tracking-[0.2em] flex items-center gap-2 mb-1.5">
                                             {{ __('ตั้งค่ารหัสผ่านสำหรับการเข้าสู่ระบบครั้งถัดไป') }}
                                        </h3>
                                        <p class="text-[10px] sm:text-[11px] text-gray-500">กรุณาตั้งรหัสผ่านเพื่อให้คุณสามารถเข้าสู่ระบบด้วยอีเมลในครั้งหน้าได้</p>
                                    </div>
                                    <div>
                                        <label for="password" class="block font-medium text-xs text-gray-400 mb-1.5">{{ __('กำหนดรหัสผ่านใหม่') }}</label>
                                        <input id="password" name="password" type="password"
                                            class="block w-full bg-[#121212] border-white/10 text-white rounded-xl text-sm px-3 sm:px-4 py-2.5 sm:py-3 focus:ring-1 focus:ring-yellow-500 focus:border-yellow-500 transition-colors placeholder-gray-600" 
                                            placeholder="อย่างน้อย 8 ตัวอักษร"
                                            {{ is_null(auth()->user()->password) ? 'required' : '' }} />
                                        <x-input-error :messages="$errors->get('password')" class="mt-1 text-red-400 text-xs" />
                                    </div>
                                    <div>
                                        <label for="password_confirmation" class="block font-medium text-xs text-gray-400 mb-1.5">{{ __('ยืนยันรหัสผ่านอีกครั้ง') }}</label>
                                        <input id="password_confirmation" name="password_confirmation" type="password" 
                                            class="block w-full bg-[#121212] border-white/10 text-white rounded-xl text-sm px-3 sm:px-4 py-2.5 sm:py-3 focus:ring-1 focus:ring-yellow-500 focus:border-yellow-500 transition-colors placeholder-gray-600"
                                            placeholder="กรอกรหัสผ่านอีกครั้ง" 
                                            {{ is_null(auth()->user()->password) ? 'required' : '' }} />
                                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-red-400 text-xs" />
                                    </div>
                                </div>
                            @else
                                <div class="p-4 sm:p-5 bg-blue-500/10 rounded-2xl border border-blue-500/20">
                                    <p class="text-xs sm:text-sm text-blue-400 flex items-center justify-center font-medium text-center">
                                        <i class="fas fa-check-circle mr-1.5 sm:mr-2 shrink-0"></i>
                                        {{ __('บัญชีของคุณมีการตั้งรหัสผ่านเพื่อเข้าสู่ระบบเรียบร้อยแล้ว') }}
                                    </p>
                                </div>
                            @endif
                        </div>

                        <div class="pt-4 sm:pt-6">
                            <button type="submit"
                                class="w-full bg-white text-black py-3 sm:py-4 rounded-xl text-xs sm:text-sm font-normal uppercase tracking-widest hover:bg-gray-200 transition-all shadow-lg active:scale-[0.99]">
                                <i class="fas fa-save mr-1.5 sm:mr-2"></i> {{ __('บันทึกข้อมูลและเข้าสู่ระบบ') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
    </style>

    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function() {
                    const output = document.getElementById('avatar-preview');
                    output.src = reader.result;
                }
                reader.readAsDataURL(file);
            }
        }
    </script>
</x-setup-layout>