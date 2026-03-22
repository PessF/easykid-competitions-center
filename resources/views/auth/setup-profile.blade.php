<x-setup-layout>
    <div class="py-4 sm:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div
                class="bg-white dark:bg-[#0f0f0f] overflow-hidden shadow-sm rounded-xl border border-gray-100 dark:border-white/5 animate-in fade-in zoom-in duration-500 relative">

                {{-- ปุ่ม Logout --}}
                <div class="absolute top-6 right-6 sm:top-10 sm:right-10 z-10">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="flex items-center space-x-3 text-gray-400 hover:text-red-500 transition-all duration-300 group p-2 rounded-full">
                            <span
                                class="text-sm uppercase tracking-[0.25em] font-normal opacity-0 group-hover:opacity-100 transition-all transform translate-x-2 group-hover:translate-x-0">
                                {{ __('ออกจากระบบ') }}
                            </span>
                            <svg class="w-8 h-8 transform group-hover:-translate-x-1 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>

                <div class="p-6 sm:p-12">
                    {{-- Header --}}
                    <div class="mb-10 sm:mb-12">
                        <h2 class="text-3xl font-semibold text-gray-900 dark:text-white tracking-tight">
                            {{ __('ตั้งค่าข้อมูลส่วนตัว') }}
                        </h2>
                        <div class="h-1.5 w-16 bg-black dark:bg-white mt-3"></div>
                        <p class="mt-5 text-base text-gray-500 dark:text-gray-400 font-light leading-relaxed">
                            {{ __('กรุณาตรวจสอบข้อมูลให้ถูกต้อง เพื่อความปลอดภัยของบัญชีคุณ') }}
                        </p>
                    </div>

                    <form method="POST" action="{{ route('profile.setup.store') }}" enctype="multipart/form-data"
                        class="space-y-10">
                        @csrf

                        {{-- Profile Picture Section --}}
                        <div
                            class="flex flex-col sm:flex-row items-center space-y-6 sm:space-y-0 sm:space-x-10 p-8 bg-gray-50 dark:bg-white/[0.02] rounded-xl border border-gray-100 dark:border-white/5">
                            <div class="relative group cursor-pointer"
                                onclick="document.getElementById('avatar').click()">
                                <img id="avatar-preview"
                                    src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&color=7F9CF5&background=EBF4FF' }}"
                                    class="w-28 h-28 sm:w-32 sm:h-32 rounded-xl object-cover border border-gray-200 dark:border-white/10 shadow-sm transition-transform group-hover:scale-105">
                                <div
                                    class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-black/20 rounded-xl">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path d="M12 4v16m8-8H4" stroke-width="2" stroke-linecap="round" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 text-center sm:text-left">
                                <label
                                    class="block text-base font-semibold text-gray-800 dark:text-gray-100 mb-3">{{ __('รูปโปรไฟล์') }}</label>
                                <input type="file" name="avatar" id="avatar" accept="image/*"
                                    onchange="previewImage(event)" class="hidden">
                                <button type="button" onclick="document.getElementById('avatar').click()"
                                    class="text-sm font-bold py-2.5 px-6 bg-gray-200 dark:bg-white/10 dark:text-white rounded-lg hover:bg-gray-300 transition-all uppercase tracking-wide">
                                    {{ __('เลือกรูปภาพ') }}
                                </button>
                                <p class="mt-3 text-xs text-gray-400 uppercase tracking-widest">
                                    {{ __('ขนาดแนะนำ 500x500 px (ไม่เกิน 2MB)') }}</p>
                                <x-input-error :messages="$errors->get('avatar')" class="mt-2" />
                            </div>
                        </div>

                        {{-- Name Sections (Thai & English) --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                            {{-- Thai Section --}}
                            <div class="space-y-6">
                                <h3
                                    class="text-sm font-normal text-gray-400 dark:text-gray-500 uppercase tracking-[0.25em] flex items-center">
                                    {{ __('ข้อมูลภาษาไทย') }}
                                </h3>
                                <div class="grid grid-cols-12 gap-4">
                                    <div class="col-span-12 sm:col-span-4">
                                        <x-input-label for="prefix_th" :value="__('คำนำหน้า')" />
                                        <select name="prefix_th"
                                            class="mt-1 block w-full border-gray-200 dark:border-white/10 dark:bg-black dark:text-white rounded-lg text-base py-2.5 focus:ring-1 focus:ring-black">
                                            <option value="นาย" {{ old('prefix_th') == 'นาย' ? 'selected' : '' }}>
                                                นาย</option>
                                            <option value="นาง" {{ old('prefix_th') == 'นาง' ? 'selected' : '' }}>
                                                นาง</option>
                                            <option value="นางสาว"
                                                {{ old('prefix_th') == 'นางสาว' ? 'selected' : '' }}>นางสาว</option>
                                        </select>
                                    </div>
                                    <div class="col-span-12 sm:col-span-8">
                                        <x-input-label for="first_name_th" :value="__('ชื่อจริง')" />
                                        <x-text-input name="first_name_th" type="text" class="mt-1 block w-full"
                                            :value="old('first_name_th')" />
                                        <x-input-error :messages="$errors->get('first_name_th')" class="mt-1" />
                                    </div>
                                    <div class="col-span-12">
                                        <x-input-label for="last_name_th" :value="__('นามสกุล')" />
                                        <x-text-input name="last_name_th" type="text" class="mt-1 block w-full"
                                            :value="old('last_name_th')" />
                                        <x-input-error :messages="$errors->get('last_name_th')" class="mt-1" />
                                    </div>
                                </div>
                            </div>

                            {{-- English Section --}}
                            <div class="space-y-6">
                                <h3
                                    class="text-sm font-normal text-gray-400 dark:text-gray-500 uppercase tracking-[0.25em] flex items-center">
                                    {{ __('English Information') }}
                                </h3>
                                <div class="grid grid-cols-12 gap-4">
                                    <div class="col-span-12 sm:col-span-4">
                                        <x-input-label for="prefix_en" :value="__('Prefix')" />
                                        <select name="prefix_en"
                                            class="mt-1 block w-full border-gray-200 dark:border-white/10 dark:bg-black dark:text-white rounded-lg text-base py-2.5 focus:ring-1 focus:ring-black">
                                            <option value="Mr." {{ old('prefix_en') == 'Mr.' ? 'selected' : '' }}>
                                                Mr.</option>
                                            <option value="Mrs." {{ old('prefix_en') == 'Mrs.' ? 'selected' : '' }}>
                                                Mrs.</option>
                                            <option value="Ms." {{ old('prefix_en') == 'Ms.' ? 'selected' : '' }}>
                                                Ms.</option>
                                        </select>
                                    </div>
                                    <div class="col-span-12 sm:col-span-8">
                                        <x-input-label for="first_name_en" :value="__('First Name')" />
                                        <x-text-input name="first_name_en" type="text" class="mt-1 block w-full"
                                            :value="old('first_name_en')" />
                                        <x-input-error :messages="$errors->get('first_name_en')" class="mt-1" />
                                    </div>
                                    <div class="col-span-12">
                                        <x-input-label for="last_name_en" :value="__('Last Name')" />
                                        <x-text-input name="last_name_en" type="text" class="mt-1 block w-full"
                                            :value="old('last_name_en')" />
                                        <x-input-error :messages="$errors->get('last_name_en')" class="mt-1" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Additional Info & Password --}}
                        <div class="space-y-8 pt-8 border-t border-gray-100 dark:border-white/5">
                            {{-- Row 1: Birthday & Phone --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                                <div>
                                    <x-input-label for="birthday" :value="__('วัน/เดือน/ปี เกิด (ค.ศ.)')" />
                                    <input name="birthday" type="date" value="{{ old('birthday') }}"
                                        class="mt-1 block w-full rounded-lg border-gray-200 dark:border-white/10 dark:bg-black dark:text-white text-base py-2.5 focus:ring-1 focus:ring-black [color-scheme:light] dark:[color-scheme:dark]" />
                                    <x-input-error :messages="$errors->get('birthday')" class="mt-1" />
                                </div>
                                <div>
                                    <x-input-label for="phone_number" :value="__('เบอร์โทรศัพท์')" />
                                    <x-text-input name="phone_number" type="text" class="mt-1 block w-full"
                                        placeholder="08XXXXXXXX" :value="old('phone_number')" />
                                    <x-input-error :messages="$errors->get('phone_number')" class="mt-1" />
                                </div>
                                <div>
                                    <x-input-label for="shirt_size" :value="__('ไซส์เสื้อ (Shirt Size)')" />
                                    <select name="shirt_size" id="shirt_size"
                                        class="mt-1 block w-full border-gray-200 dark:border-white/10 dark:bg-black dark:text-white rounded-lg text-base py-2.5 focus:ring-1 focus:ring-black">
                                        <option value="" disabled {{ old('shirt_size') ? '' : 'selected' }}>
                                            {{ __('เลือกไซส์เสื้อ') }}</option>
                                        <option value="S" {{ old('shirt_size') == 'S' ? 'selected' : '' }}>S
                                            (รอบอก 34-36")</option>
                                        <option value="M" {{ old('shirt_size') == 'M' ? 'selected' : '' }}>M
                                            (รอบอก 36-38")</option>
                                        <option value="L" {{ old('shirt_size') == 'L' ? 'selected' : '' }}>L
                                            (รอบอก 38-40")</option>
                                        <option value="XL" {{ old('shirt_size') == 'XL' ? 'selected' : '' }}>XL
                                            (รอบอก 40-42")</option>
                                        <option value="2XL" {{ old('shirt_size') == '2XL' ? 'selected' : '' }}>2XL
                                            (รอบอก 42-44")</option>
                                        <option value="3XL" {{ old('shirt_size') == '3XL' ? 'selected' : '' }}>3XL
                                            (รอบอก 44-46")</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('shirt_size')" class="mt-1" />
                                </div>
                            </div>

                            {{-- Row 2: Password --}}
                            @if (is_null(auth()->user()->password))
                                <div
                                    class="grid grid-cols-1 sm:grid-cols-2 gap-8 animate-in slide-in-from-top-4 duration-700">
                                    <div>
                                        <x-input-label for="password" :value="__('กำหนดรหัสผ่านใหม่')" />
                                        <x-text-input id="password" name="password" type="password"
                                            class="mt-1 block w-full" placeholder="อย่างน้อย 8 ตัวอักษร"
                                            :required="is_null(auth()->user()->password)" />
                                        <p class="mt-2 text-[10px] text-gray-400 uppercase tracking-widest">
                                            {{ __('สำหรับใช้เข้าสู่ระบบด้วยอีเมลในครั้งถัดไป') }}
                                        </p>
                                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                                    </div>
                                    <div>
                                        <x-input-label for="password_confirmation" :value="__('ยืนยันรหัสผ่านอีกครั้ง')" />
                                        <x-text-input id="password_confirmation" name="password_confirmation"
                                            type="password" class="mt-1 block w-full"
                                            placeholder="กรอกรหัสผ่านอีกครั้ง" :required="is_null(auth()->user()->password)" />
                                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                                    </div>
                                </div>
                            @else
                                <div
                                    class="p-4 bg-blue-50 dark:bg-blue-900/10 rounded-lg border border-blue-100 dark:border-blue-900/20">
                                    <p class="text-sm text-blue-600 dark:text-blue-400 flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ __('บัญชีของคุณมีการตั้งรหัสผ่านเรียบร้อยแล้ว') }}
                                    </p>
                                </div>
                            @endif
                        </div>

                        <div class="pt-10">
                            <button type="submit"
                                class="w-full bg-black dark:bg-white text-white dark:text-black py-5 rounded-lg text-lg uppercase font-normal tracking-[0.15em] hover:opacity-90 transition-all shadow-xl active:scale-[0.98]">
                                {{ __('ยืนยันข้อมูลและเข้าสู่ระบบ') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

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
