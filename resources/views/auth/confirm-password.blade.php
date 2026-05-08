<x-guest-layout>
    <div class="w-full max-w-[420px]">
        <div class="bg-[#121212] p-8 sm:p-10 rounded-2xl shadow-2xl border border-white/5">
            
            <h2 class="text-xl font-medium text-center text-white mb-4 tracking-tight">ยืนยันรหัสผ่าน</h2>

            <div class="mb-8 text-sm text-gray-400 text-center font-light leading-relaxed">
                {{ __('เพื่อความปลอดภัยของข้อมูล กรุณายืนยันรหัสผ่านของท่านก่อนดำเนินการในขั้นตอนถัดไป') }}
            </div>

            <form method="POST" action="{{ route('password.confirm') }}" class="space-y-6">
                @csrf

                <div>
                    <input id="password" type="password" name="password" placeholder="รหัสผ่านของท่าน" required autocomplete="current-password"
                        class="w-full px-5 py-3.5 rounded-xl border border-white/10 bg-[#0a0a0a] text-base font-light focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all text-white placeholder-gray-600">
                    <x-input-error :messages="$errors->get('password')" class="mt-2 ml-1 text-xs" />
                </div>

                <button type="submit" class="w-full bg-white text-black py-3.5 rounded-xl font-medium text-base tracking-wide transition-all hover:bg-gray-200 active:scale-[0.99] shadow-lg">
                    {{ __('ยืนยันรหัสผ่าน') }}
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>