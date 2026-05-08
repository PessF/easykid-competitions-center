<x-user-layout>
    <x-slot name="title">ข้อตกลงการใช้บริการ | Easykids</x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10 font-kanit pb-20">
        {{-- Header Section --}}
        <div class="mb-6 sm:mb-8 flex items-center gap-3 sm:gap-4">
            <div
                class="w-10 h-10 sm:w-12 sm:h-12 bg-indigo-500/10 border border-indigo-500/20 rounded-xl flex items-center justify-center text-indigo-400 shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <div>
                <h1 class="text-xl sm:text-3xl font-normal text-white tracking-tight">
                    ข้อตกลงและเงื่อนไขการใช้บริการ</h1>
                <p class="text-gray-500 text-xs sm:text-sm mt-0.5 sm:mt-1 font-normal">Terms of Service -
                    มีผลบังคับใช้ตั้งแต่ปี {{ date('Y') }} เป็นต้นไป</p>
            </div>
        </div>

        {{-- Content Section --}}
        <div
            class="bg-[#121212] rounded-2xl sm:rounded-[2rem] border border-white/5 shadow-sm p-5 sm:p-8 md:p-10 space-y-6 sm:space-y-8 text-gray-400 font-normal leading-relaxed text-xs sm:text-sm md:text-base">

            <section class="space-y-2 sm:space-y-3">
                <h2 class="text-sm sm:text-lg font-normal text-gray-200 flex items-center">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 sm:mr-2.5 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    1. กฎระเบียบและกติกาการแข่งขัน
                </h2>
                <p class="pl-6 sm:pl-[30px]">ผู้สมัครทุกทีมต้องศึกษา ทำความเข้าใจ และยอมรับกฎระเบียบและกติกาการแข่งขัน (Rules & Regulations)
                    ของแต่ละรายการอย่างละเอียด หากตรวจพบการทุจริต หรือคุณสมบัติของสมาชิกในทีมไม่เป็นไปตามที่กำหนด (เช่น
                    อายุเกินเกณฑ์) คณะกรรมการจัดการแข่งขันขอสงวนสิทธิ์ในการตัดสิทธิ์การแข่งขันของทีมดังกล่าวทันที โดยจะไม่มีการคืนเงินค่าธรรมเนียมการสมัครในทุกกรณี</p>
            </section>

            <section class="space-y-2 sm:space-y-3">
                <h2 class="text-sm sm:text-lg font-normal text-gray-200 flex items-center">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 sm:mr-2.5 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    2. นโยบายการชำระเงินและการคืนเงินค่าธรรมเนียม
                </h2>
                <p class="pl-6 sm:pl-[30px]">การลงทะเบียนจะถือว่าสมบูรณ์เมื่อผู้สมัครได้ดำเนินการชำระเงินและแนบหลักฐานการชำระเงินผ่านระบบเป็นที่เรียบร้อยแล้วเท่านั้น ทั้งนี้ ผู้จัดการแข่งขันขอสงวนสิทธิ์
                    <span class="text-red-400 underline decoration-red-400/50 underline-offset-4">ไม่คืนเงินค่าธรรมเนียมการลงทะเบียนในทุกกรณี</span> (No Refund Policy)
                    เว้นแต่ในกรณีที่การแข่งขันถูกยกเลิกโดยผู้จัดการแข่งขันเอง หรือหากผู้สมัครมีความประสงค์จะยกเลิกหลังจากการลงทะเบียนเสร็จสมบูรณ์แล้ว กรุณาติดต่อผู้จัดการแข่งขันโดยตรง</p>
            </section>

            <section class="space-y-2 sm:space-y-3">
                <h2 class="text-sm sm:text-lg font-normal text-gray-200 flex items-center">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 sm:mr-2.5 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    3. การแก้ไขและการเปลี่ยนแปลงข้อมูล
                </h2>
                <p class="pl-6 sm:pl-[30px]">ผู้ควบคุมทีมสามารถแก้ไขรายชื่อสมาชิกในทีมได้ผ่านระบบการจัดการข้อมูลทีม
                    อย่างไรก็ตาม เมื่อทีมดังกล่าวได้ถูกนำไปลงทะเบียนเข้าร่วมการแข่งขันเรียบร้อยแล้ว จะไม่สามารถแก้ไขข้อมูลได้อีก เนื่องจากระบบจะถือว่าท่านได้ตรวจสอบและยืนยันความถูกต้องของข้อมูลทั้งหมดก่อนการลงทะเบียนแล้ว</p>
            </section>

        </div>
    </div>
</x-user-layout>