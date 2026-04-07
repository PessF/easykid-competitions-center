<x-user-layout>
    <x-slot name="title">E-Ticket - {{ $registration->regis_no }}</x-slot>

    {{-- เราใช้ Alpine.js (x-data) เพื่อควบคุมการแสดงผลของ Modal --}}
    <div x-data="{ showModal: true }" class="min-h-[80vh] flex items-center justify-center p-4 sm:p-8 font-kanit">

        {{-- 🎟️ TICKET CONTAINER 🎟️ --}}
        <div id="printable-ticket"
            class="w-full max-w-md bg-white dark:bg-[#141414] rounded-[2rem] shadow-xl border border-gray-200 dark:border-gray-800 overflow-hidden relative">

            {{-- Header --}}
            <div class="bg-blue-600 p-6 text-center text-white relative">
                {{-- ปุ่มเปิด Modal อีกครั้ง (เผื่อปิดไปแล้วอยากอ่านใหม่) ให้แสดงเฉพาะหน้าจอ ไม่แสดงตอนปรินต์ --}}
                <button @click="showModal = true" class="absolute top-4 right-4 text-white/80 hover:text-white transition print:hidden" title="คำแนะนำการใช้งาน">
                    <i class="fas fa-info-circle text-xl"></i>
                </button>

                <h1 class="text-2xl font-normal tracking-widest uppercase">E-Ticket</h1>
                <p class="text-blue-200 text-xs mt-1 font-normal">Easykids Competitions</p>
            </div>

            {{-- Body --}}
            <div class="p-8 flex flex-col items-center">
                {{-- QR Code --}}
                <div class="bg-white p-3 rounded-2xl shadow-sm border border-gray-100 mb-6">
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(180)->margin(1)->generate(URL::signedRoute('verify.ticket', ['reg_no' => $registration->regis_no])) !!}
                </div>

                {{-- Team Name & Regis No --}}
                <h2 class="text-xl font-normal text-gray-900 dark:text-white text-center mb-2 leading-tight">
                    {{ $registration->team->name }}
                </h2>
                <p
                    class="text-sm font-mono text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-500/10 px-4 py-1.5 rounded-lg mb-6 border border-blue-100 dark:border-blue-500/20 font-normal tracking-wider">
                    {{ $registration->regis_no }}
                </p>

                {{-- Details --}}
                <div class="w-full space-y-3 text-sm border-t border-dashed border-gray-200 dark:border-gray-800 pt-6">
                    <div class="flex justify-between items-start gap-4">
                        <span class="text-gray-500 dark:text-gray-400 shrink-0">งานแข่งขัน</span>
                        <span
                            class="font-normal text-gray-900 dark:text-gray-200 text-right">{{ $registration->competition->name }}</span>
                    </div>
                    <div class="flex justify-between items-start gap-4">
                        <span class="text-gray-500 dark:text-gray-400 shrink-0">รุ่น</span>
                        <span
                            class="font-normal text-gray-900 dark:text-gray-200 text-right">{{ $registration->competitionClass->name }}</span>
                    </div>
                    <div class="flex justify-between items-start gap-4">
                        <span class="text-gray-500 dark:text-gray-400 shrink-0">สถาบัน</span>
                        <span
                            class="font-normal text-gray-900 dark:text-gray-200 text-right">{{ $registration->team->school_name ?? '-' }}</span>
                    </div>
                </div>

                {{-- Members --}}
                <div class="w-full border-t border-dashed border-gray-200 dark:border-gray-800 pt-5 mt-5">
                    <p
                        class="text-[11px] font-normal text-gray-400 uppercase tracking-wider mb-2 text-center sm:text-left">
                        สมาชิกในทีม:</p>
                    <div class="flex flex-wrap gap-1.5 justify-center sm:justify-start">
                        @foreach ($registration->team->members as $member)
                            <span
                                class="text-xs bg-gray-100 dark:bg-white/5 text-gray-700 dark:text-gray-300 px-2.5 py-1 rounded-md font-normal border border-gray-200 dark:border-gray-700">
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
                    class="inline-flex items-center justify-center gap-2 bg-gray-900 hover:bg-black dark:bg-white dark:hover:bg-gray-200 dark:text-gray-900 text-white px-6 py-3 rounded-xl text-sm font-normal transition-colors shadow-lg shadow-gray-900/20 dark:shadow-white/10 w-full sm:w-auto">
                    <i class="fas fa-print"></i> พิมพ์บัตรนี้ / บันทึก PDF
                </button>
                <p class="text-[10px] text-gray-400 mt-3">โปรดนำบัตรนี้ไปแสดงที่จุดลงทะเบียนในวันแข่งขัน</p>
            </div>

            {{-- โซนปุ่มเทสต์ (ซ่อนตอนปรินต์จริง) --}}
            <div class="mt-4 pb-4 text-center print:hidden">
                <a href="{{ URL::signedRoute('verify.ticket', ['reg_no' => $registration->regis_no]) }}" target="_blank"
                    class="inline-block px-4 py-2 bg-purple-100 text-purple-700 text-xs font-normal rounded-lg hover:bg-purple-200 transition-colors border border-purple-200">
                    <i class="fas fa-search"></i> จำลองการสแกน QR (คลิกเลย)
                </a>
            </div>
        </div>

        {{-- 🔔 INSTRUCTION MODAL (Popup) 🔔 --}}
        <div x-show="showModal" 
             style="display: none;" 
             class="fixed inset-0 z-50 flex items-center justify-center print:hidden"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            {{-- Backdrop (ฉากหลังสีดำโปร่งแสง) --}}
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="showModal = false"></div>
            
            {{-- Modal Content --}}
            <div class="relative bg-white dark:bg-gray-900 w-full max-w-lg mx-4 rounded-3xl shadow-2xl overflow-hidden transform transition-all"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                
                {{-- Modal Header --}}
                <div class="bg-blue-50 dark:bg-blue-900/30 p-6 flex items-center justify-between border-b border-blue-100 dark:border-blue-800">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-800 flex items-center justify-center text-blue-600 dark:text-blue-300">
                            <i class="fas fa-info-circle text-xl"></i>
                        </div>
                        <h3 class="text-lg font-normal text-gray-900 dark:text-white">คำแนะนำการลงทะเบียนหน้างาน</h3>
                    </div>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="p-6 sm:p-8 space-y-5 text-gray-700 dark:text-gray-300">
                    <p class="text-sm font-normal">เพื่อความรวดเร็วในการจุดลงทะเบียน ขอให้ทุกทีมเตรียมตัวดังนี้:</p>
                    
                    <ul class="space-y-4 font-normal">
                        <li class="flex items-start gap-3">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xs font-normal mt-0.5">1</span>
                            <span class="text-sm"><span>โชว์บัตรเดียว:</span> ให้ตัวแทนทีม 1 คน (ผู้เข้าแข่งขัน หรือ ครูที่ปรึกษา) เป็นผู้เตรียม E-Ticket นี้ให้เจ้าหน้าที่สแกน (เปิดจากมือถือหรือพิมพ์มาก็ได้)</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xs font-normal mt-0.5">2</span>
                            <span class="text-sm"><span>เตรียมเอกสาร:</span> สมาชิกในทีม <span>ทุกคน</span> ต้องเตรียม <u>บัตรประชาชน</u> หรือ <u>บัตรนักเรียน/นักศึกษา</u> มาแสดงตัวต่อเจ้าหน้าที่เพื่อตรวจสอบสิทธิ์</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xs font-normal mt-0.5">3</span>
                            <span class="text-sm"><span>ตรวจหุ่นยนต์:</span> เตรียมหุ่นยนต์ที่ใช้แข่งขันมาให้คณะกรรมการตรวจสอบน้ำหนัก ขนาด และสเปค ให้ตรงตามกติกาของรุ่นนั้นๆ</span>
                        </li>
                    </ul>

                    <div class="mt-6 bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-xl border border-yellow-200 dark:border-yellow-700">
                        <p class="text-xs text-yellow-800 dark:text-yellow-300 font-normal">
                            <i class="fas fa-exclamation-triangle mr-1"></i> หากมีปัญหาใด ๆ กรูณาติดต่อเจ้าหน้าที่ที่จุดลงทะเบียน ขอให้ท่านจงมีแต่ความสุข ขอบคุณครับ/ค่ะ
                        </p>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="bg-gray-50 dark:bg-gray-800/50 p-5 border-t border-gray-100 dark:border-gray-800 flex justify-end">
                    <button @click="showModal = false" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-normal rounded-xl transition-colors w-full sm:w-auto shadow-sm">
                        รับทราบ
                    </button>
                </div>
            </div>
        </div>

    </div>

    <style>
        @media print {
            body * { visibility: hidden; }
            body, html { background-color: white !important; margin: 0; padding: 0; }
            #printable-ticket, #printable-ticket * { visibility: visible; }
            #printable-ticket {
                position: absolute; left: 50%; top: 0; transform: translateX(-50%);
                width: 100%; max-width: 450px; box-shadow: none !important;
                border: 2px solid #e5e7eb !important; margin: 0 !important;
                background: white !important; color: black !important;
            }
            .bg-blue-600 {
                background-color: #2563eb !important; -webkit-print-color-adjust: exact; print-color-adjust: exact;
            }
            .text-white { color: white !important; }
        }
    </style>
</x-user-layout>