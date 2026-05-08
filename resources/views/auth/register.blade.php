<x-guest-layout>
    <div class="w-full max-w-[420px]">
        
        <div class="flex flex-col items-center mb-6">
            <a href="/" class="flex flex-col items-center group">
                <div class="mb-3 transition-transform duration-300 group-hover:scale-105">
                    <img src="{{ asset('images/logo.png') }}" 
                         alt="Logo" 
                         class="w-[200px] object-contain filter brightness-110">
                </div>
                
                <span class="text-[11px] tracking-[0.4em] text-gray-500 font-medium uppercase ml-1 group-hover:text-white transition-colors duration-300">Competition Center</span>
            </a>
        </div>

        <div class="bg-[#121212] p-8 sm:p-10 rounded-2xl shadow-2xl border border-white/5">
            
            <h2 class="text-xl font-medium text-center text-white mb-8 tracking-tight">ลงทะเบียนบัญชีผู้ใช้งานใหม่</h2>

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <div>
                    <input id="name" type="text" name="name" :value="old('name')" placeholder="ชื่อ-นามสกุล" required autofocus autocomplete="name"
                        class="w-full px-5 py-3.5 rounded-xl border border-white/10 bg-[#0a0a0a] text-base font-light focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all text-white placeholder-gray-600">
                    <x-input-error :messages="$errors->get('name')" class="mt-1 ml-1 text-xs" />
                </div>

                <div>
                    <input id="email" type="email" name="email" :value="old('email')" placeholder="ที่อยู่อีเมล" required autocomplete="username"
                        class="w-full px-5 py-3.5 rounded-xl border border-white/10 bg-[#0a0a0a] text-base font-light focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all text-white placeholder-gray-600">
                    <x-input-error :messages="$errors->get('email')" class="mt-1 ml-1 text-xs" />
                </div>

                <div class="space-y-2">
                    <div class="relative" x-data="{ show: false }">
                        <input id="password" :type="show ? 'text' : 'password'" name="password" placeholder="กำหนดรหัสผ่าน" required autocomplete="new-password"
                            class="w-full px-5 py-3.5 rounded-xl border border-white/10 bg-[#0a0a0a] text-base font-light focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all text-white placeholder-gray-600">
                        
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-500 hover:text-white transition-colors">
                            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <svg x-show="show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>

                    <div class="px-1 py-1 space-y-1">
                        <p class="text-[13px] text-gray-500 flex items-center gap-1.5">
                            <span class="w-1 h-1 rounded-full bg-gray-500"></span>
                            ความยาวอย่างน้อย 8 ตัวอักษร
                        </p>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-1 ml-1 text-xs" />
                </div>

                <div class="relative" x-data="{ showConfirm: false }">
                    <input id="password_confirmation" :type="showConfirm ? 'text' : 'password'" name="password_confirmation" placeholder="ยืนยันรหัสผ่านอีกครั้ง" required autocomplete="new-password"
                        class="w-full px-5 py-3.5 rounded-xl border border-white/10 bg-[#0a0a0a] text-base font-light focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all text-white placeholder-gray-600">
                    
                    <button type="button" @click="showConfirm = !showConfirm" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-500 hover:text-white transition-colors">
                        <svg x-show="!showConfirm" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <svg x-show="showConfirm" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    </button>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 ml-1 text-xs" />
                </div>

                <button class="w-full bg-white text-black py-3.5 rounded-xl font-medium text-base tracking-wide transition-all hover:bg-gray-200 active:scale-[0.99] shadow-lg mt-2">
                    ยืนยันการลงทะเบียน
                </button>
            </form>

            <div class="relative my-8">
                <div class="absolute inset-0 flex items-center">
                    <span class="w-full border-t border-white/10"></span>
                </div>
                <div class="relative flex justify-center text-[12px] font-medium text-gray-500 uppercase tracking-widest">
                    <span class="bg-[#121212] px-4">OR</span>
                </div>
            </div>

            <div class="text-center">
                <p class="text-sm text-gray-500 font-medium">
                    มีบัญชีผู้ใช้งานแล้วใช่หรือไม่? 
                    <a href="{{ route('login') }}" class="text-white font-medium hover:underline underline-offset-4 decoration-2 ml-1 transition-all">เข้าสู่ระบบ</a>
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>