<x-guest-layout>
    <div class="w-full max-w-[420px]">

        <div class="flex flex-col items-center mb-6">
            <a href="/" class="flex flex-col items-center group">
                <div class="mb-3 transition-transform duration-300 group-hover:scale-105">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo"
                        class="w-[200px] object-contain filter brightness-110">
                </div>
                <span class="text-[11px] tracking-[0.4em] text-gray-500 font-medium uppercase ml-1 group-hover:text-white transition-colors duration-300">
                    Competition Center
                </span>
            </a>
        </div>

        <div class="bg-[#121212] p-8 sm:p-10 rounded-2xl shadow-2xl border border-white/5">

            <h2 class="text-xl font-medium text-center text-white mb-8 tracking-tight">เข้าสู่ระบบ</h2>

            @if (session('status'))
                <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show"
                    class="mb-6 font-normal text-sm text-emerald-400 p-4 bg-emerald-500/10 rounded-xl border border-emerald-500/20 text-center">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <input type="email" name="email" :value="old('email')" placeholder="อีเมล" required autofocus
                        class="w-full px-5 py-3.5 rounded-xl border border-white/10 bg-[#0a0a0a] text-base font-light focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all text-white placeholder-gray-600">
                    <x-input-error :messages="$errors->get('email')" class="mt-1 ml-1 text-xs" />
                </div>

                <div>
                    <input type="password" name="password" placeholder="รหัสผ่าน" required
                        class="w-full px-5 py-3.5 rounded-xl border border-white/10 bg-[#0a0a0a] text-base font-light focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all text-white placeholder-gray-600">
                    <x-input-error :messages="$errors->get('password')" class="mt-1 ml-1 text-xs" />
                </div>

                <div class="flex items-center justify-between px-1 py-2">
                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox" name="remember"
                            class="w-4 h-4 rounded border-gray-700 bg-[#0a0a0a] text-blue-500 focus:ring-0 focus:ring-offset-0 cursor-pointer">
                        <span class="ml-2 text-sm text-gray-500 group-hover:text-gray-300 transition-colors">จำฉันไว้</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                            class="text-sm text-gray-500 hover:text-white transition-colors font-medium">ลืมรหัสผ่าน?</a>
                    @endif
                </div>

                <button class="w-full bg-white text-black py-3.5 rounded-xl font-medium text-base tracking-wide transition-all hover:bg-gray-200 active:scale-[0.99] shadow-lg">
                    เข้าสู่ระบบ
                </button>
            </form>

            <div class="relative my-8">
                <div class="absolute inset-0 flex items-center">
                    <span class="w-full border-t border-white/10"></span>
                </div>
                <div class="relative flex justify-center text-[11px] font-medium text-gray-500 uppercase tracking-widest">
                    <span class="bg-[#121212] px-4">OR</span>
                </div>
            </div>

            <div class="space-y-5">
                <a href="{{ route('auth.google') }}"
                    class="w-full flex items-center justify-center gap-3 py-3.5 bg-white/5 border border-white/10 rounded-xl hover:bg-white/10 transition-all text-sm font-semibold text-gray-300">
                    <img src="https://www.svgrepo.com/show/355037/google.svg" class="w-5 h-5">
                    ดำเนินการต่อด้วย Google
                </a>

                <p class="text-center text-sm text-gray-500 font-medium pt-2">
                    ยังไม่มีบัญชี?
                    <a href="{{ route('register') }}"
                        class="text-white font-medium hover:underline underline-offset-4 decoration-2 ml-1 transition-all">สมัครสมาชิก</a>
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>