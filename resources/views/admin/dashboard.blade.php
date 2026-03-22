<x-admin-layout>
    <x-slot name="title">Admin Dashboard | Robot Competition</x-slot>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">ภาพรวมการแข่งขัน</h1>
        <p class="text-gray-500">ข้อมูลอัปเดตล่าสุดของระบบจัดการหุ่นยนต์</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="p-6 bg-white dark:bg-[#0f0f0f] border border-gray-100 dark:border-white/5 rounded-2xl shadow-sm">
            <div class="text-gray-400 text-sm font-medium uppercase tracking-wider">ทีมที่ลงทะเบียน</div>
            <div class="text-3xl font-bold mt-2 dark:text-white">42</div>
            <div class="mt-2 text-xs text-green-500 font-medium">↑ 12% จากเมื่อวาน</div>
        </div>

        <div class="p-6 bg-white dark:bg-[#0f0f0f] border border-gray-100 dark:border-white/5 rounded-2xl shadow-sm">
            <div class="text-gray-400 text-sm font-medium uppercase tracking-wider">หุ่นยนต์ที่ผ่านตรวจสภาพ</div>
            <div class="text-3xl font-bold mt-2 dark:text-white text-blue-500">38 / 42</div>
        </div>

        <div class="p-6 bg-white dark:bg-[#0f0f0f] border border-gray-100 dark:border-white/5 rounded-2xl shadow-sm">
            <div class="text-gray-400 text-sm font-medium uppercase tracking-wider">แมตช์ที่กำลังแข่ง</div>
            <div class="text-3xl font-bold mt-2 dark:text-white">4</div>
        </div>

        <div class="p-6 bg-white dark:bg-[#0f0f0f] border border-gray-100 dark:border-white/5 rounded-2xl shadow-sm">
            <div class="text-gray-400 text-sm font-medium uppercase tracking-wider">ประเภทการแข่งขัน</div>
            <div class="text-3xl font-bold mt-2 dark:text-white">3</div>
        </div>
    </div>
</x-admin-layout>