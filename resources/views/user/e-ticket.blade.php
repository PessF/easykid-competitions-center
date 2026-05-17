<x-user-layout>
    <x-slot name="title">E-Ticket - {{ $registration->regis_no }}</x-slot>

    {{-- เราใช้ Alpine.js (x-data) เพื่อควบคุมการแสดงผลของ Modal --}}
    <div x-data="{ showModal: true }" class="min-h-[80vh] flex items-center justify-center p-4 sm:p-8 font-kanit">

        {{-- 🎟️ TICKET CONTAINER 🎟️ --}}
        <div id="printable-ticket"
            class="w-full max-w-md bg-[#121212] rounded-2xl sm:rounded-[2rem] shadow-2xl border border-white/5 overflow-hidden relative">

            {{-- Header --}}
            <div class="bg-[#1a1a1a] border-b border-white/5 p-5 sm:p-6 text-center text-white relative">
                {{-- ปุ่มเปิด Modal อีกครั้ง (เผื่อปิดไปแล้วอยากอ่านใหม่) ให้แสดงเฉพาะหน้าจอ ไม่แสดงตอนปรินต์ --}}
                <button @click="showModal = true" class="absolute top-4 right-4 sm:top-5 sm:right-5 text-gray-500 hover:text-white transition-colors print:hidden focus:outline-none" title="คำแนะนำการใช้งาน">
                    <i class="fas fa-info-circle text-lg sm:text-xl"></i>
                </button>

                <h1 class="text-xl sm:text-2xl font-normal tracking-[0.2em] uppercase text-white">E-Ticket</h1>
                <p class="text-blue-400 text-[10px] sm:text-xs mt-1 font-normal tracking-wide">Easykids Competitions</p>
            </div>

            {{-- Body --}}
            <div class="p-6 sm:p-8 flex flex-col items-center">
                {{-- QR Code (ต้องมีพื้นหลังสีขาวเพื่อให้สแกนติดง่าย) --}}
                <div class="bg-white p-2.5 sm:p-3 rounded-[1rem] sm:rounded-2xl shadow-sm mb-5 sm:mb-6">
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(160)->margin(1)->generate(URL::signedRoute('verify.ticket', ['reg_no' => $registration->regis_no])) !!}
                </div>

                {{-- Team Name & Regis No --}}
                <h2 class="text-lg sm:text-xl font-normal text-white text-center mb-1.5 sm:mb-2 leading-tight">
                    {{ $registration->team->name }}
                </h2>
                <p
                    class="text-xs sm:text-sm font-mono text-blue-400 bg-blue-500/10 px-3 py-1 sm:px-4 sm:py-1.5 rounded-md sm:rounded-lg mb-5 sm:mb-6 border border-blue-500/20 font-normal tracking-widest">
                    {{ $registration->regis_no }}
                </p>

                {{-- Details --}}
                <div class="w-full space-y-2.5 sm:space-y-3 text-xs sm:text-sm border-t border-dashed border-white/10 pt-5 sm:pt-6">
                    <div class="flex justify-between items-start gap-4">
                        <span class="text-gray-500 shrink-0">รายการแข่งขัน</span>
                        <span
                            class="font-normal text-gray-200 text-right">{{ $registration->competition->name }}</span>
                    </div>
                    <div class="flex justify-between items-start gap-4">
                        <span class="text-gray-500 shrink-0">รุ่นการแข่งขัน</span>
                        <span class="font-normal text-gray-200 text-right">
                            @if($registration->category_name)
                                {{ $registration->category_name }} &bull; 
                            @endif
                            {{ $registration->competitionClass->name }}
                        </span>
                    </div>
                    <div class="flex justify-between items-start gap-4">
                        <span class="text-gray-500 shrink-0">สถาบันการศึกษา</span>
                        <span
                            class="font-normal text-gray-200 text-right">{{ $registration->team->school_name ?? '-' }}</span>
                    </div>
                </div>

                {{-- Members --}}
                <div class="w-full border-t border-dashed border-white/10 pt-4 sm:pt-5 mt-4 sm:mt-5">
                    <p
                        class="text-[10px] sm:text-[11px] font-normal text-gray-500 uppercase tracking-widest mb-2 sm:mb-3 text-center sm:text-left">
                        รายชื่อสมาชิกในทีม:</p>
                    <div class="flex flex-wrap gap-1.5 justify-center sm:justify-start">
                        @foreach ($registration->team->members as $member)
                            <span
                                class="text-[10px] sm:text-xs bg-[#1a1a1a] text-gray-300 px-2 sm:px-2.5 py-1 rounded-md font-normal border border-white/5">
                                {{ $member->first_name_th }} {{ $member->last_name_th }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Action / Print Button (ซ่อนตอนสั่งพิมพ์จริง) --}}
            <div
                class="bg-[#0a0a0a] p-4 sm:p-5 text-center border-t border-white/5 print:hidden">
                <button onclick="window.print()"
                    class="inline-flex items-center justify-center gap-2 bg-white hover:bg-gray-200 text-black px-5 py-2.5 sm:px-6 sm:py-3 rounded-xl text-xs sm:text-sm font-normal transition-colors shadow-lg w-full sm:w-auto focus:outline-none">
                    <i class="fas fa-print"></i> พิมพ์เอกสาร / บันทึกเป็น PDF
                </button>
                <p class="text-[9px] sm:text-[10px] text-gray-500 mt-2.5 sm:mt-3">กรุณานำเอกสารฉบับนี้มาแสดง ณ จุดลงทะเบียนในวันแข่งขัน</p>
            </div>

            {{-- โซนปุ่มเทสต์ (ซ่อนตอนปรินต์จริง) --}}
            <div class="mt-2 sm:mt-4 pb-4 sm:pb-6 text-center print:hidden">
                <a href="{{ URL::signedRoute('verify.ticket', ['reg_no' => $registration->regis_no]) }}" target="_blank"
                    class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 sm:px-4 sm:py-2 bg-purple-500/10 text-purple-400 text-[10px] sm:text-xs font-normal rounded-lg hover:bg-purple-500/20 transition-colors border border-purple-500/20">
                    <i class="fas fa-search"></i> ทดสอบระบบจำลองการตรวจสอบคิวอาร์โค้ด
                </a>
            </div>
        </div>

        {{-- 🔔 INSTRUCTION MODAL (Popup) 🔔 --}}
        <div x-show="showModal" 
             style="display: none;" 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 print:hidden"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            {{-- Backdrop (ฉากหลังสีดำ) --}}
            <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" @click="showModal = false"></div>
            
            {{-- Modal Content --}}
            <div class="relative bg-[#121212] w-full max-w-lg mx-auto rounded-2xl sm:rounded-[2rem] shadow-2xl border border-white/10 overflow-hidden transform transition-all flex flex-col max-h-[90vh]"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                
                {{-- Modal Header --}}
                <div class="bg-[#0a0a0a] p-4 sm:p-5 flex items-center justify-between border-b border-white/5 shrink-0">
                    <div class="flex items-center gap-2.5 sm:gap-3">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 shrink-0">
                            <i class="fas fa-info-circle text-sm sm:text-base"></i>
                        </div>
                        <h3 class="text-sm sm:text-base font-normal text-white">ข้อปฏิบัติสำหรับการลงทะเบียนหน้างาน</h3>
                    </div>
                    <button @click="showModal = false" class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-[#1a1a1a] border border-white/5 flex items-center justify-center text-gray-500 hover:text-white hover:bg-white/10 transition-colors shrink-0 focus:outline-none">
                        <i class="fas fa-times text-xs sm:text-sm"></i>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="p-5 sm:p-8 space-y-4 sm:space-y-5 text-gray-400 overflow-y-auto custom-scrollbar">
                    <p class="text-xs sm:text-sm font-normal text-gray-300">เพื่อความสะดวกรวดเร็วในการลงทะเบียน กรุณาเตรียมความพร้อมดังรายละเอียดต่อไปนี้:</p>
                    
                    <ul class="space-y-3 sm:space-y-4 font-normal">
                        <li class="flex items-start gap-2.5 sm:gap-3">
                            <span class="flex-shrink-0 w-5 h-5 sm:w-6 sm:h-6 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 flex items-center justify-center text-[10px] sm:text-xs font-normal mt-0.5">1</span>
                            <span class="text-xs sm:text-sm leading-relaxed"><span class="text-white">แสดงเอกสารการสมัคร:</span> ตัวแทนทีมจำนวน 1 ท่าน (ผู้เข้าแข่งขัน หรือ ครูที่ปรึกษา) โปรดเตรียม E-Ticket ฉบับนี้เพื่อแสดงต่อเจ้าหน้าที่ (สามารถแสดงผ่านหน้าจอโทรศัพท์มือถือ หรือเอกสารฉบับพิมพ์)</span>
                        </li>
                        <li class="flex items-start gap-2.5 sm:gap-3">
                            <span class="flex-shrink-0 w-5 h-5 sm:w-6 sm:h-6 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 flex items-center justify-center text-[10px] sm:text-xs font-normal mt-0.5">2</span>
                            <span class="text-xs sm:text-sm leading-relaxed"><span class="text-white">การยืนยันตัวตน:</span> สมาชิกในทีม<span class="text-white">ทุกคน</span>โปรดเตรียม <u>บัตรประจำตัวประชาชน</u> หรือ <u>บัตรประจำตัวนักเรียน/นักศึกษา</u> เพื่อแสดงต่อเจ้าหน้าที่สำหรับการตรวจสอบสิทธิ์</span>
                        </li>
                        <li class="flex items-start gap-2.5 sm:gap-3">
                            <span class="flex-shrink-0 w-5 h-5 sm:w-6 sm:h-6 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 flex items-center justify-center text-[10px] sm:text-xs font-normal mt-0.5">3</span>
                            <span class="text-xs sm:text-sm leading-relaxed"><span class="text-white">การตรวจสอบอุปกรณ์ (หุ่นยนต์):</span> โปรดนำหุ่นยนต์ที่ใช้ในการแข่งขันมาแสดงต่อคณะกรรมการ เพื่อตรวจสอบน้ำหนัก ขนาด และคุณสมบัติให้เป็นไปตามกติกาของรุ่นการแข่งขัน</span>
                        </li>
                    </ul>

                    <div class="mt-5 sm:mt-6 bg-amber-500/10 p-3 sm:p-4 rounded-xl sm:rounded-2xl border border-amber-500/20">
                        <p class="text-[10px] sm:text-xs text-amber-400 font-normal leading-relaxed">
                            <i class="fas fa-exclamation-triangle mr-1"></i> หากมีข้อสงสัยหรือพบปัญหาประการใด กรุณาติดต่อเจ้าหน้าที่ ณ จุดลงทะเบียน ขอขอบพระคุณที่ให้ความร่วมมือ
                        </p>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="bg-[#0a0a0a] p-4 sm:p-5 border-t border-white/5 flex justify-end shrink-0">
                    <button @click="showModal = false" class="px-5 sm:px-6 py-2 sm:py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-xs sm:text-sm font-normal rounded-xl transition-colors w-full sm:w-auto shadow-sm focus:outline-none">
                        รับทราบข้อปฏิบัติ
                    </button>
                </div>
            </div>
        </div>

    </div>

    <style>
        /* สกอร์บาร์สำหรับ Modal */
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }

        /* ระบบปริ้นต์ (Print Stylesheet) บังคับให้เป็นพื้นขาว-ตัวอักษรดำ */
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
            /* บังคับสี Header ให้เป็นสีขาว/ดำ หรือเทา เพื่อประหยัดหมึก */
            #printable-ticket > div:first-child {
                background-color: #f3f4f6 !important; border-bottom: 2px solid #e5e7eb !important;
            }
            #printable-ticket > div:first-child h1, #printable-ticket > div:first-child p {
                color: black !important;
            }
            /* สีของ badge ย่อยต่างๆ */
            .bg-blue-500\/10 { background-color: white !important; border-color: #9ca3af !important; color: black !important; }
            .bg-\[\#1a1a1a\] { background-color: white !important; border-color: #9ca3af !important; color: black !important; }
            .text-gray-500, .text-gray-400, .text-gray-200, .text-white { color: black !important; }
            .border-white\/10 { border-color: #e5e7eb !important; }
        }
    </style>
</x-user-layout>
