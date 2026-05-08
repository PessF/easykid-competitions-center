<x-guest-layout>
    <div class="w-full max-w-[420px]">

        <div class="bg-[#121212] p-8 sm:p-10 rounded-2xl shadow-2xl border border-white/5">
            
            <h2 class="text-xl font-medium text-center text-white mb-4 tracking-tight">ขอตั้งค่ารหัสผ่านใหม่</h2>

            <div class="mb-8 text-sm text-gray-400 text-center font-light leading-relaxed">
                {{ __('กรุณาระบุที่อยู่อีเมลของท่าน ระบบจะทำการจัดส่งลิงก์สำหรับการตั้งค่ารหัสผ่านใหม่ไปยังอีเมลดังกล่าว') }}
            </div>

            @if (session('status'))
                <div x-data="{ show: true }" 
                     x-init="setTimeout(() => show = false, 6000)" 
                     x-show="show"
                     class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-medium text-center">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                @csrf

                <div>
                    <input id="email" type="email" name="email" :value="old('email')" placeholder="ที่อยู่อีเมลของท่าน" required autofocus
                        class="w-full px-5 py-3.5 rounded-xl border border-white/10 bg-[#0a0a0a] text-base font-light focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all text-white placeholder-gray-600">
                    <x-input-error :messages="$errors->get('email')" class="mt-2 ml-1 text-xs" />
                </div>

                <div class="space-y-4">
                    <button class="w-full bg-white text-black py-3.5 rounded-xl font-medium text-base tracking-wide transition-all hover:bg-gray-200 active:scale-[0.99] shadow-lg">
                        ส่งลิงก์ตั้งค่ารหัสผ่านใหม่
                    </button>

                    <a href="{{ route('login') }}" class="flex items-center justify-center gap-2 w-full py-2 text-sm text-gray-500 hover:text-white transition-colors font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                        กลับสู่หน้าเข้าสู่ระบบ
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>