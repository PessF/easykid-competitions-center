<x-guest-layout>
    <div class="w-full max-w-[420px]">
        
        {{-- Logo Section --}}
        <div class="flex flex-col items-center mb-6">
            <a href="/" class="flex flex-col items-center group">
                <div class="mb-3 transition-transform duration-300 group-hover:scale-105">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-[200px] object-contain filter brightness-110">
                </div>
                <span class="text-[11px] tracking-[0.4em] text-gray-500 font-medium uppercase ml-1 group-hover:text-white transition-colors duration-300">Competition Center</span>
            </a>
        </div>

        {{-- Main Card --}}
        <div class="bg-[#121212] p-8 sm:p-10 rounded-2xl shadow-2xl border border-white/5 relative animate-in fade-in zoom-in duration-500">
            
            <h2 class="text-xl font-medium text-center text-white mb-4 tracking-tight">ยืนยันอีเมลของคุณ</h2>

            <div class="mb-6 text-sm text-gray-400 text-center font-light leading-relaxed">
                {{ __('เราได้ส่งลิงก์สำหรับยืนยันตัวตนไปที่') }}
                <span class="text-white font-medium block my-1">{{ auth()->user()->email ?? 'อีเมลของคุณ' }}</span>
                {{ __('กรุณากดลิงก์ในอีเมลเพื่อเริ่มใช้งาน') }}
            </div>

            {{-- 🚀 กล่องข้อความแจ้งเตือน --}}
            <div class="p-4 bg-amber-500/10 border border-amber-500/20 rounded-xl mb-6 text-left">
                <div class="flex items-start gap-3">
                    <i class="fas fa-info-circle text-amber-500 mt-0.5"></i>
                    <div class="text-xs text-amber-200/80 leading-relaxed">
                        <strong class="text-amber-400 block mb-1 text-sm font-medium">เพิ่งกดลิงก์ยืนยันมาใช่ไหม?</strong>
                        ระบบอาจใช้เวลาประมวลผลสักครู่ หากกดยืนยันแล้วแต่ยังอยู่หน้านี้ 
                        <span class="text-white">กรุณารอ 5-10 วินาที แล้วกดลิงก์จากอีเมลฉบับเดิมครับ</span>
                    </div>
                </div>
            </div>

            {{-- Alert สำหรับตอนกดส่งเมลซ้ำ --}}
            @if (session('status') == 'verification-link-sent')
                <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 6000)" x-show="show"
                     class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-medium text-center">
                    {{ __('ลิงก์ยืนยันตัวตนใหม่ถูกส่งไปยังอีเมลของคุณเรียบร้อยแล้ว') }}
                </div>
            @endif

            <div class="space-y-4">

                {{-- ปุ่มส่งอีเมลซ้ำ --}}
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button class="w-full bg-white text-black hover:bg-gray-200 py-3.5 rounded-xl font-medium text-sm tracking-wide transition-all active:scale-[0.99] shadow-lg">
                        ส่งอีเมลยืนยันอีกครั้ง
                    </button>
                </form>

                {{-- ปุ่มออกจากระบบ --}}
                <form method="POST" action="{{ route('logout') }}" class="text-center pt-2">
                    @csrf
                    <button type="submit" class="text-xs text-gray-500 hover:text-white transition-colors font-medium underline underline-offset-4 decoration-2">
                        ออกจากระบบ / ใช้บัญชีอื่น
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>