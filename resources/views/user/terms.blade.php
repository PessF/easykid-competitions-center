<x-user-layout>
    <x-slot name="title">ข้อตกลงการใช้บริการ | Easykids</x-slot>

    <div class="max-w-4xl mx-auto pb-12">
        {{-- Header Section --}}
        <div class="mb-8 flex items-center space-x-4">
            <div
                class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <div>
                <h1 class="text-2xl md:text-3xl font-semibold text-gray-900 dark:text-white tracking-tight">
                    ข้อตกลงและเงื่อนไขการใช้บริการ</h1>
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-1 font-normal">Terms of Service -
                    มีผลบังคับใช้ตั้งแต่ {{ date('Y') }} เป็นต้นไป</p>
            </div>
        </div>

        {{-- Content Section --}}
        <div
            class="bg-white dark:bg-[#121212] rounded-2xl border border-gray-100 dark:border-white/5 shadow-sm p-8 md:p-10 space-y-8 text-gray-600 dark:text-gray-300 font-normal leading-relaxed text-sm md:text-base">

            <section class="space-y-3">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    1. กฎและกติกาการแข่งขัน
                </h2>
                <p>ผู้สมัครทุกทีมต้องทำความเข้าใจและยอมรับกฎกติกาการแข่งขัน (Rules & Regulations)
                    ของแต่ละรายการอย่างละเอียด หากพบว่ามีการทุจริต หรือคุณสมบัติสมาชิกในทีมไม่ตรงตามที่กำหนด (เช่น
                    อายุเกินเกณฑ์) คณะกรรมการมีสิทธิ์ตัดสิทธิ์การแข่งขันทันทีโดยไม่มีการคืนเงินค่าสมัคร</p>
            </section>

            <section class="space-y-3">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    2. นโยบายการชำระเงินและการคืนเงิน
                </h2>
                <p>การสมัครจะสมบูรณ์เมื่อมีการชำระเงินและแนบสลิปผ่านระบบเรียบร้อยแล้วเท่านั้น ทางผู้จัดงานขอสงวนสิทธิ์
                    <strong>ไม่มีการคืนเงินค่าสมัครในทุกกรณี</strong> (No Refund Policy)
                    ยกเว้นกรณีที่งานแข่งขันถูกยกเลิกโดยผู้จัดงานเอง</p>
            </section>

            <section class="space-y-3">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    3. การเปลี่ยนแปลงข้อมูล
                </h2>
                <p>ผู้ควบคุมทีมสามารถแก้ไขรายชื่อสมาชิกในทีมได้ผ่านระบบจัดการทีม
                    จนกว่าทีมนั้นถูกนำไปลงทะเบียนการแข่งขันอยู่จะไม่สามารถแก้ไขได้ เพราะถือว่าคุณตรวจสอบข้อมูลดีแล้ว</p>
            </section>

        </div>
    </div>
</x-user-layout>
