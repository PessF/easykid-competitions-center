<x-guest-layout>
    <div class="w-full max-w-[420px]">


        <div class="bg-white dark:bg-[#111111] p-8 sm:p-10 rounded-2xl shadow-[0_10px_30px_rgba(0,0,0,0.04)] border border-gray-100 dark:border-white/5">
            
            <h2 class="text-xl font-medium text-center text-gray-900 dark:text-white mb-4 tracking-tight">ลืมรหัสผ่าน?</h2>

            <div class="mb-8 text-sm text-gray-500 dark:text-gray-400 text-center font-light leading-relaxed">
                {{ __('ไม่ต้องกังวล! เพียงกรอกอีเมลของคุณ แล้วเราจะส่งลิงก์สำหรับตั้งรหัสผ่านใหม่ให้ทางอีเมลทันที') }}
            </div>

            @if (session('status'))
                <div x-data="{ show: true }" 
                     x-init="setTimeout(() => show = false, 6000)" 
                     x-show="show"
                     class="mb-6 p-4 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-800/30 text-green-600 dark:text-green-400 text-xs font-medium">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                @csrf

                <div>
                    <input id="email" type="email" name="email" :value="old('email')" placeholder="ใส่อีเมลของคุณ" required autofocus
                        class="w-full px-5 py-3.5 rounded-xl border-none bg-gray-50 dark:bg-[#1a1a1a] text-base font-light focus:ring-1 focus:ring-black dark:focus:ring-white transition-all dark:text-white placeholder:text-gray-400">
                    <x-input-error :messages="$errors->get('email')" class="mt-2 ml-1 text-xs" />
                </div>

                <div class="space-y-4">
                    <button class="w-full bg-black dark:bg-white text-white dark:text-black py-3.5 rounded-xl font-medium text-base tracking-wide transition-all hover:opacity-90 active:scale-[0.99] shadow-lg shadow-black/5">
                        ส่งลิงก์ตั้งรหัสผ่านใหม่
                    </button>

                    <a href="{{ route('login') }}" class="flex items-center justify-center gap-2 w-full py-2 text-sm text-gray-400 hover:text-black dark:hover:text-white transition-colors font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                        กลับไปหน้าเข้าสู่ระบบ
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>