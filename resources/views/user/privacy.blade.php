<x-user-layout>
    <x-slot name="title">นโยบายความเป็นส่วนตัว | Easykids</x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10 font-kanit pb-20">
        {{-- Header Section --}}
        <div class="mb-6 sm:mb-8 flex items-center gap-3 sm:gap-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-500/10 border border-blue-500/20 rounded-xl flex items-center justify-center text-blue-400 shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <div>
                <h1 class="text-xl sm:text-3xl font-normal text-white tracking-tight">นโยบายความเป็นส่วนตัว</h1>
                <p class="text-gray-500 text-xs sm:text-sm mt-0.5 sm:mt-1 font-normal">Privacy Policy - อัปเดตล่าสุดเมื่อ {{ date('d M Y') }}</p>
            </div>
        </div>

        {{-- Content Section --}}
        <div class="bg-[#121212] rounded-2xl sm:rounded-[2rem] border border-white/5 shadow-sm p-5 sm:p-8 md:p-10 space-y-6 sm:space-y-8 text-gray-400 font-normal leading-relaxed text-xs sm:text-sm md:text-base">
            
            <section class="space-y-2 sm:space-y-3">
                <h2 class="text-sm sm:text-lg font-normal text-gray-200 flex items-center">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 sm:mr-2.5 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    1. ข้อมูลที่เรารวบรวม
                </h2>
                <p class="pl-6 sm:pl-[30px]">Easykids Robotics จะทำการเก็บรวบรวมข้อมูลส่วนบุคคลที่จำเป็นสำหรับการลงทะเบียนแข่งขันเท่านั้น ได้แก่ ข้อมูลของครู/ผู้ควบคุมทีม (ชื่อ, อีเมล, เบอร์ติดต่อ) และข้อมูลของสมาชิกในทีม (ชื่อ-นามสกุล, วันเดือนปีเกิด เพื่อตรวจสอบคุณสมบัติในแต่ละรุ่นการแข่งขัน)</p>
            </section>

            <section class="space-y-2 sm:space-y-3">
                <h2 class="text-sm sm:text-lg font-normal text-gray-200 flex items-center">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 sm:mr-2.5 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>
                    2. การจัดเก็บและการรักษาความปลอดภัย
                </h2>
                <p class="pl-6 sm:pl-[30px]">เราให้ความสำคัญกับความปลอดภัยของข้อมูลผู้ใช้สูงสุด ข้อมูลและเอกสารทั้งหมด (รวมถึงสลิปการโอนเงิน) จะถูกจัดเก็บผ่านระบบ Google Drive Cloud Storage ที่มีการเข้ารหัสและจำกัดสิทธิ์การเข้าถึงเฉพาะผู้ดูแลระบบที่มีอำนาจหน้าที่เท่านั้น</p>
            </section>

            <section class="space-y-2 sm:space-y-3">
                <h2 class="text-sm sm:text-lg font-normal text-gray-200 flex items-center">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 sm:mr-2.5 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                    3. การเปิดเผยข้อมูล
                </h2>
                <p class="pl-6 sm:pl-[30px]">เราจะไม่นำข้อมูลส่วนบุคคลของท่านไปขาย แลกเปลี่ยน หรือส่งต่อให้บุคคลภายนอกโดยเด็ดขาด ยกเว้นกรณีที่เกี่ยวข้องกับการจัดการแข่งขัน เช่น การประกาศรายชื่อทีมที่เข้าร่วม หรือการจัดทำเกียรติบัตร</p>
            </section>

        </div>
    </div>
</x-user-layout>