<x-guest-layout>
    <div class="w-full max-w-[420px]">
        
        <div class="flex flex-col items-center mb-6">
            <a href="/" class="flex flex-col items-center group">
                <div class="mb-3 transition-transform group-hover:scale-105">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-[300px] object-contain filter dark:brightness-110">
                </div>
                <span class="text-[11px] tracking-[0.4em] text-gray-400 font-medium uppercase ml-1">Competition Center</span>
            </a>
        </div>

        <div class="bg-white dark:bg-[#111111] p-8 sm:p-10 rounded-2xl shadow-[0_10px_30px_rgba(0,0,0,0.04)] border border-gray-100 dark:border-white/5">
            
            <h2 class="text-xl font-medium text-center text-gray-900 dark:text-white mb-4 tracking-tight">ยืนยันอีเมลของคุณ</h2>

            <div class="mb-8 text-sm text-gray-500 dark:text-gray-400 text-center font-light leading-relaxed">
                {{ __('ขอบคุณที่สมัครสมาชิกกับเรา! ก่อนเริ่มใช้งาน โปรดยืนยันอีเมลของคุณโดยคลิกลิงก์ที่เราเพิ่งส่งให้ หากคุณไม่ได้รับอีเมล เรายินดีที่จะส่งให้อีกครั้งครับ') }}
            </div>

            @if (session('status') == 'verification-link-sent')
                <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 6000)" x-show="show"
                     class="mb-6 p-4 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-800/30 text-green-600 dark:text-green-400 text-xs font-medium text-center">
                    {{ __('ลิงก์ยืนยันตัวตนใหม่ถูกส่งไปยังอีเมลที่คุณใช้สมัครเรียบร้อยแล้ว') }}
                </div>
            @endif

            <div class="space-y-4">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button class="w-full bg-black dark:bg-white text-white dark:text-black py-3.5 rounded-xl font-medium text-base tracking-wide transition-all hover:opacity-90 active:scale-[0.99] shadow-lg shadow-black/5">
                        ส่งอีเมลยืนยันอีกครั้ง
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}" class="text-center">
                    @csrf
                    <button type="submit" class="text-sm text-gray-400 hover:text-black dark:hover:text-white transition-colors font-medium">
                        ออกจากระบบ
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>