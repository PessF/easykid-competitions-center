<x-guest-layout>
    <div class="w-full max-w-[420px]">

        <div class="flex flex-col items-center mb-6">
            <a href="/" class="flex flex-col items-center group">
                <div class="mb-3 transition-transform group-hover:scale-105">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo"
                        class="w-[300px] object-contain filter dark:brightness-110">
                </div>

                <span class="text-[11px] tracking-[0.4em] text-gray-400 font-medium uppercase ml-1">Competition
                    Center</span>
            </a>
        </div>

        <div
            class="bg-white dark:bg-[#111111] p-8 sm:p-10 rounded-2xl shadow-[0_10px_30px_rgba(0,0,0,0.04)] border border-gray-100 dark:border-white/5">

            <h2 class="text-xl font-medium text-center text-gray-900 dark:text-white mb-8 tracking-tight">เข้าสู่ระบบ
            </h2>

            @if (session('status'))
                <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show"
                    class="mb-4 font-normal text-sm text-green-600 dark:text-green-400 p-3 bg-green-50 dark:bg-green-900/20 rounded-xl border border-green-100 dark:border-green-900/30">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <input type="email" name="email" :value="old('email')" placeholder="อีเมล" required autofocus
                        class="w-full px-5 py-3.5 rounded-xl border-none bg-gray-50 dark:bg-[#1a1a1a] text-base font-light focus:ring-1 focus:ring-black dark:focus:ring-white transition-all dark:text-white placeholder:text-gray-400">
                    <x-input-error :messages="$errors->get('email')" class="mt-1 ml-1 text-xs" />
                </div>

                <div>
                    <input type="password" name="password" placeholder="รหัสผ่าน" required
                        class="w-full px-5 py-3.5 rounded-xl border-none bg-gray-50 dark:bg-[#1a1a1a] text-base font-light focus:ring-1 focus:ring-black dark:focus:ring-white transition-all dark:text-white placeholder:text-gray-400">
                    <x-input-error :messages="$errors->get('password')" class="mt-1 ml-1 text-xs" />
                </div>

                <div class="flex items-center justify-between px-1">
                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox" name="remember"
                            class="w-4 h-4 rounded border-gray-300 text-black focus:ring-0 cursor-pointer">
                        <span
                            class="ml-2 text-sm text-gray-500 group-hover:text-black dark:group-hover:text-white transition-colors">จำฉันไว้</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                            class="text-sm text-gray-400 hover:text-black dark:hover:text-white transition-colors font-medium">ลืมรหัสผ่าน?</a>
                    @endif
                </div>

                <button
                    class="w-full bg-black dark:bg-white text-white dark:text-black py-3.5 rounded-xl font-medium text-base tracking-wide transition-all hover:opacity-90 active:scale-[0.99] shadow-lg shadow-black/5">
                    เข้าสู่ระบบ
                </button>
            </form>

            <div class="relative my-8">
                <div class="absolute inset-0 flex items-center"><span
                        class="w-full border-t border-gray-100 dark:border-white/5"></span></div>
                <div
                    class="relative flex justify-center text-[12px] font-medium text-gray-300 uppercase tracking-widest">
                    <span class="bg-white dark:bg-[#111111] px-4">OR</span>
                </div>
            </div>

            <div class="space-y-4">
                <a href="{{ route('auth.google') }}"
                    class="w-full flex items-center justify-center gap-3 py-3.5 border border-gray-100 dark:border-white/10 rounded-xl hover:bg-gray-50 dark:hover:bg-white/5 transition-all text-sm font-semibold text-gray-600 dark:text-gray-300">
                    <img src="https://www.svgrepo.com/show/355037/google.svg" class="w-5 h-5">
                    ดำเนินการต่อด้วย Google
                </a>

                <p class="text-center text-sm text-gray-400 font-medium">
                    ยังไม่มีบัญชี?
                    <a href="{{ route('register') }}"
                        class="text-black dark:text-white font-medium hover:underline underline-offset-4 decoration-2">สมัครสมาชิก</a>
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>
