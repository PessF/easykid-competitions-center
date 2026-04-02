<x-user-layout>
    <x-slot name="title">E-Ticket - {{ $registration->regis_no }}</x-slot>

    <div class="min-h-[80vh] flex items-center justify-center p-4 sm:p-8 font-kanit">

        {{-- 🎟️ TICKET CONTAINER 🎟️ --}}
        <div id="printable-ticket"
            class="w-full max-w-md bg-white dark:bg-[#141414] rounded-[2rem] shadow-xl border border-gray-200 dark:border-gray-800 overflow-hidden relative">

            {{-- Header --}}
            <div class="bg-blue-600 p-6 text-center text-white">
                <h1 class="text-2xl font-bold tracking-widest uppercase">E-Ticket</h1>
                <p class="text-blue-200 text-xs mt-1 font-medium">Easykids Competitions</p>
            </div>

            {{-- Body --}}
            <div class="p-8 flex flex-col items-center">
                {{-- QR Code --}}
                <div class="bg-white p-3 rounded-2xl shadow-sm border border-gray-100 mb-6">
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(180)->margin(1)->generate(URL::signedRoute('verify.ticket', ['reg_no' => $registration->regis_no])) !!}
                </div>

                {{-- Team Name & Regis No --}}
                <h2 class="text-xl font-bold text-gray-900 dark:text-white text-center mb-2 leading-tight">
                    {{ $registration->team->name }}
                </h2>
                <p
                    class="text-sm font-mono text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-500/10 px-4 py-1.5 rounded-lg mb-6 border border-blue-100 dark:border-blue-500/20 font-semibold tracking-wider">
                    {{ $registration->regis_no }}
                </p>

                {{-- Details --}}
                <div class="w-full space-y-3 text-sm border-t border-dashed border-gray-200 dark:border-gray-800 pt-6">
                    <div class="flex justify-between items-start gap-4">
                        <span class="text-gray-500 dark:text-gray-400 shrink-0">งานแข่งขัน</span>
                        <span
                            class="font-medium text-gray-900 dark:text-gray-200 text-right">{{ $registration->competition->name }}</span>
                    </div>
                    <div class="flex justify-between items-start gap-4">
                        <span class="text-gray-500 dark:text-gray-400 shrink-0">รุ่น</span>
                        <span
                            class="font-medium text-gray-900 dark:text-gray-200 text-right">{{ $registration->competitionClass->name }}</span>
                    </div>
                    <div class="flex justify-between items-start gap-4">
                        <span class="text-gray-500 dark:text-gray-400 shrink-0">สถาบัน</span>
                        <span
                            class="font-medium text-gray-900 dark:text-gray-200 text-right">{{ $registration->team->school_name ?? '-' }}</span>
                    </div>
                </div>

                {{-- Members --}}
                <div class="w-full border-t border-dashed border-gray-200 dark:border-gray-800 pt-5 mt-5">
                    <p
                        class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2 text-center sm:text-left">
                        สมาชิกในทีม:</p>
                    <div class="flex flex-wrap gap-1.5 justify-center sm:justify-start">
                        @foreach ($registration->team->members as $member)
                            <span
                                class="text-xs bg-gray-100 dark:bg-white/5 text-gray-700 dark:text-gray-300 px-2.5 py-1 rounded-md font-medium border border-gray-200 dark:border-gray-700">
                                {{ $member->first_name_th }} {{ $member->last_name_th }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Action / Print Button (ซ่อนตอนสั่งพิมพ์จริง) --}}
            <div
                class="bg-gray-50 dark:bg-white/5 p-5 text-center border-t border-gray-100 dark:border-gray-800 print:hidden">
                <button onclick="window.print()"
                    class="inline-flex items-center justify-center gap-2 bg-gray-900 hover:bg-black dark:bg-white dark:hover:bg-gray-200 dark:text-gray-900 text-white px-6 py-3 rounded-xl text-sm font-semibold transition-colors shadow-lg shadow-gray-900/20 dark:shadow-white/10 w-full sm:w-auto">
                    <i class="fas fa-print"></i> พิมพ์บัตรนี้ / บันทึก PDF
                </button>
                <p class="text-[10px] text-gray-400 mt-3">โปรดนำบัตรนี้ไปแสดงที่จุดลงทะเบียนในวันแข่งขัน</p>
            </div>


            {{-- โซนปุ่มเทสต์ (ซ่อนตอนปรินต์จริง) --}}
            <div class="mt-4 text-center print:hidden">
                <a href="{{ URL::signedRoute('verify.ticket', ['reg_no' => $registration->regis_no]) }}" target="_blank"
                    class="inline-block px-4 py-2 bg-purple-100 text-purple-700 text-xs font-bold rounded-lg hover:bg-purple-200 transition-colors border border-purple-200">
                    <i class="fas fa-search"></i> จำลองการสแกน QR (คลิกเลย)
                </a>
            </div>
        </div>

    </div>

    {{-- 🚀 CSS เวทมนตร์สำหรับการสั่งพิมพ์ --}}
    <style>
        @media print {

            /* 1. ซ่อนทุกอย่างบนหน้าจอ (รวมถึง Sidebar ของ Layout) */
            body * {
                visibility: hidden;
            }

            /* 2. บังคับพื้นหลังหน้ากระดาษให้เป็นสีขาว */
            body,
            html {
                background-color: white !important;
                margin: 0;
                padding: 0;
            }

            /* 3. เปิดการมองเห็นเฉพาะตั๋วที่เราต้องการ */
            #printable-ticket,
            #printable-ticket * {
                visibility: visible;
            }

            /* 4. จัดตำแหน่งตั๋วให้มาอยู่ตรงกลางกระดาษด้านบนสุด */
            #printable-ticket {
                position: absolute;
                left: 50%;
                top: 0;
                transform: translateX(-50%);
                width: 100%;
                max-width: 450px;
                box-shadow: none !important;
                border: 2px solid #e5e7eb !important;
                margin: 0 !important;
                background: white !important;
                color: black !important;
            }

            /* 5. บังคับให้สีพื้นหลังของกล่อง (เช่นแถบสีน้ำเงิน) ปรินต์ติดออกมาด้วย */
            .bg-blue-600 {
                background-color: #2563eb !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .text-white {
                color: white !important;
            }
        }
    </style>
</x-user-layout>
