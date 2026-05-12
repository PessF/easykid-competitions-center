<x-user-layout>
    {{-- 🚀 1. เพิ่ม State ของ Cart (ตะกร้า) เข้าไปใน Alpine.js --}}
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 font-kanit pb-32" x-data="{
        filterStatus: 'all',
        selectedItems: [],
        totalFee: 0,
        activeCompId: null,
    
        // ฟังก์ชันจัดการตอนติ๊ก Checkbox
        toggleSelect(id, fee, compId) {
            if (this.selectedItems.includes(id)) {
                // เอาออก
                this.selectedItems = this.selectedItems.filter(i => i !== id);
                this.totalFee -= fee;
                // ถ้าตะกร้าว่าง ให้ปลดล็อคการเลือกงานแข่ง
                if (this.selectedItems.length === 0) this.activeCompId = null;
            } else {
                // เลือกเพิ่ม
                if (this.activeCompId === null) this.activeCompId = compId;
                if (this.activeCompId === compId) {
                    this.selectedItems.push(id);
                    this.totalFee += fee;
                }
            }
        },
    
        // เช็คว่า Checkbox นี้อนุญาตให้กดได้ไหม
        isSelectable(compId) {
            if (this.activeCompId === null) return true;
            return this.activeCompId === compId;
        }
    }">

        {{-- ===== HEADER ===== --}}
        <div class="mb-6 sm:mb-8">
            <div class="flex items-center gap-2.5 sm:gap-3 mb-1">
                <div class="w-1.5 h-5 sm:h-6 bg-blue-500 rounded-full"></div>
                <h1 class="text-xl sm:text-2xl font-normal text-white tracking-tight">ประวัติการลงทะเบียนเข้าร่วมการแข่งขัน
                </h1>
            </div>
            <p class="text-xs sm:text-sm text-gray-400 ml-3.5 sm:ml-4 pl-1 font-normal">รายการแข่งขันที่ท่านได้ลงทะเบียนไว้ทั้งหมด</p>
        </div>

        @if ($registrations->isEmpty())
            <div
                class="flex flex-col items-center justify-center py-16 sm:py-20 bg-[#121212] border border-white/5 rounded-2xl sm:rounded-[2rem] text-center shadow-sm">
                <div
                    class="w-16 h-16 sm:w-20 sm:h-20 bg-[#1a1a1a] rounded-2xl sm:rounded-[1.5rem] flex items-center justify-center mb-4 sm:mb-5 border border-white/5">
                    <i class="fas fa-folder-open text-2xl sm:text-3xl text-blue-500/80"></i>
                </div>
                <h3 class="text-base sm:text-lg font-normal text-white mb-1.5 sm:mb-2">ยังไม่มีประวัติการลงทะเบียน</h3>
                <p class="text-xs sm:text-sm text-gray-500 mb-6 sm:mb-8 font-normal">ท่านยังไม่ได้ทำการลงทะเบียนเข้าร่วมการแข่งขันใดๆ</p>
                <a href="{{ route('user.dashboard') }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 sm:px-6 sm:py-3 bg-blue-600 hover:bg-blue-500 text-white text-xs sm:text-sm font-normal rounded-xl sm:rounded-2xl transition-colors shadow-lg shadow-blue-500/20">
                    <i class="fas fa-search text-[10px] sm:text-xs"></i> ค้นหารายการแข่งขัน
                </a>
            </div>
        @else
            {{-- FILTER TABS (Pills Style) --}}
            <div class="flex items-center gap-2 mb-6 overflow-x-auto custom-scrollbar pb-2 -mx-1 px-1">
                @php
                    $filterOptions = [
                        'all' => ['label' => 'ทั้งหมด', 'dot' => ''],
                        'pending_payment' => ['label' => 'รอการชำระเงิน', 'dot' => 'bg-red-500'],
                        'waiting_verify' => ['label' => 'รอตรวจสอบหลักฐาน', 'dot' => 'bg-amber-500'],
                        'approved' => ['label' => 'ได้รับการอนุมัติ', 'dot' => 'bg-emerald-500'],
                        'rejected' => ['label' => 'ไม่อนุมัติ / ยกเลิก', 'dot' => 'bg-gray-500'],
                    ];
                @endphp
                @foreach ($filterOptions as $val => $opt)
                    <button @click="filterStatus = '{{ $val }}'" type="button"
                        class="shrink-0 flex items-center gap-1.5 px-3 py-1.5 sm:px-4 sm:py-2 rounded-full text-xs sm:text-sm font-normal transition-all focus:outline-none border whitespace-nowrap"
                        :class="filterStatus === '{{ $val }}'
                            ?
                            'bg-white text-black border-transparent shadow-sm' :
                            'bg-[#121212] text-gray-400 border-white/5 hover:bg-white/5 hover:text-white'">
                        @if ($opt['dot'])
                            <span class="w-1.5 h-1.5 rounded-full {{ $opt['dot'] }} shrink-0"></span>
                        @endif
                        {{ $opt['label'] }}
                        @if ($val !== 'all')
                            @php $cnt = $registrations->where('status', $val)->count(); @endphp
                            @if ($cnt > 0)
                                <span
                                    class="ml-1 text-[9px] sm:text-[10px] font-normal opacity-80 bg-white/10 text-gray-300 px-1.5 py-0.5 rounded-md">{{ $cnt }}</span>
                            @endif
                        @endif
                    </button>
                @endforeach
            </div>

            {{-- REGISTRATION LIST --}}
            <div class="space-y-4">
                @foreach ($registrations as $regis)
                    @php
                        // ตั้งค่าสีและสถานะ UI แบบ Modern Dark Mode
                        $statusConfig = match ($regis->status) {
                            'pending_payment' => [
                                'pill' => 'bg-red-500/10 text-red-400 border-red-500/20',
                                'icon' => 'fa-file-invoice-dollar',
                                'text' => 'รอการชำระเงิน',
                                'strip' => 'bg-red-500',
                            ],
                            'waiting_verify' => [
                                'pill' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                                'icon' => 'fa-hourglass-half',
                                'text' => 'รอตรวจสอบหลักฐาน',
                                'strip' => 'bg-amber-500',
                            ],
                            'approved' => [
                                'pill' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                'icon' => 'fa-check-circle',
                                'text' => 'ได้รับการอนุมัติ',
                                'strip' => 'bg-emerald-500',
                            ],
                            'rejected' => [
                                'pill' => 'bg-white/5 text-gray-400 border-white/10',
                                'icon' => 'fa-times-circle',
                                'text' => 'ไม่อนุมัติ / ยกเลิก',
                                'strip' => 'bg-gray-600',
                            ],
                            default => [
                                'pill' => 'bg-white/5 text-gray-400 border-white/10',
                                'icon' => 'fa-info-circle',
                                'text' => 'ไม่ทราบสถานะ',
                                'strip' => 'bg-gray-600',
                            ],
                        };
                    @endphp

                    <div x-show="filterStatus === 'all' || filterStatus === '{{ $regis->status }}'"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="border rounded-xl sm:rounded-2xl overflow-hidden flex relative transition-colors duration-300 shadow-sm"
                        :class="selectedItems.includes({{ $regis->id }}) ?
                            'bg-blue-900/10 border-blue-500/50' :
                            'bg-[#121212] border-white/5 hover:border-white/10'"
                        @if (in_array($regis->status, ['pending_payment', 'rejected'])) @click="if(isSelectable({{ $regis->competition_id }})) toggleSelect({{ $regis->id }}, {{ $regis->competitionClass->entry_fee }}, {{ $regis->competition_id }})"
                            class="cursor-pointer" @endif>

                        {{-- แถบสีด้านซ้าย --}}
                        <div class="absolute left-0 top-0 bottom-0 w-1 sm:w-1.5 transition-colors duration-300"
                            :class="selectedItems.includes({{ $regis->id }}) ? 'bg-blue-500' :
                                '{{ $statusConfig['strip'] }}'">
                        </div>

                        {{-- โครงสร้าง Card ด้านใน --}}
                        <div class="flex-1 p-4 sm:p-6 flex flex-col sm:flex-row gap-4 sm:gap-6 min-w-0 pl-5 sm:pl-8 items-stretch sm:items-center">
                            
                            {{-- ฝั่งซ้าย: Checkbox + ข้อมูลการแข่งขัน --}}
                            <div class="flex flex-1 items-start sm:items-center gap-3 sm:gap-4 min-w-0">
                                
                                {{-- CHECKBOX CART SYSTEM --}}
                                @if (in_array($regis->status, ['pending_payment', 'rejected']))
                                    <div class="shrink-0 mt-0.5 sm:mt-0" @click.stop>
                                        <div class="relative flex items-center justify-center w-5 h-5 sm:w-6 sm:h-6 rounded sm:rounded-md border-2 transition-all duration-200 cursor-pointer"
                                            :class="selectedItems.includes({{ $regis->id }}) ?
                                                'border-blue-500 bg-blue-500' :
                                                (!isSelectable({{ $regis->competition_id }}) ?
                                                    'border-gray-700 bg-gray-800 opacity-50 cursor-not-allowed' :
                                                    'border-gray-600 bg-[#0a0a0a]')"
                                            @click="if(isSelectable({{ $regis->competition_id }})) toggleSelect({{ $regis->id }}, {{ $regis->competitionClass->entry_fee }}, {{ $regis->competition_id }})">

                                            <svg x-show="selectedItems.includes({{ $regis->id }})"
                                                class="w-3 h-3 sm:w-4 sm:h-4 text-white pointer-events-none" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                    </div>
                                @endif

                                {{-- ข้อมูลหลัก --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-2 sm:gap-2.5 mb-2 sm:mb-3">
                                        <span
                                            class="text-[9px] sm:text-[10px] font-mono font-normal px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-md tracking-widest transition-colors duration-300"
                                            :class="selectedItems.includes({{ $regis->id }}) ?
                                                'border border-blue-500/30 bg-blue-500/10 text-blue-400' :
                                                'bg-white/5 text-gray-400 border border-white/5'">
                                            {{ $regis->regis_no }}
                                        </span>
                                        <span
                                            class="inline-flex items-center gap-1 sm:gap-1.5 px-2 sm:px-3 py-0.5 sm:py-1 rounded-full text-[9px] sm:text-[10px] font-normal border {{ $statusConfig['pill'] }} transition-colors duration-300"
                                            :class="selectedItems.includes({{ $regis->id }}) ?
                                                'border-blue-500/30 bg-blue-500/10 text-blue-400' :
                                                ''">
                                            <i class="fas {{ $statusConfig['icon'] }}"></i> {{ $statusConfig['text'] }}
                                        </span>
                                    </div>

                                    <h3 class="text-sm sm:text-base font-normal leading-snug mb-1.5 sm:mb-2 truncate transition-colors duration-300"
                                        :class="selectedItems.includes({{ $regis->id }}) ?
                                            'text-blue-400' : 'text-white'">
                                        {{ $regis->competition->name }}
                                    </h3>

                                    <div class="flex flex-wrap items-center gap-x-3 sm:gap-x-5 gap-y-1.5 sm:gap-y-2 text-[10px] sm:text-xs font-normal transition-colors duration-300"
                                        :class="selectedItems.includes({{ $regis->id }}) ?
                                            'text-blue-400/70' : 'text-gray-500'">
                                        <span class="flex items-center gap-1 sm:gap-1.5"><i
                                                class="fas fa-robot opacity-60"></i>{{ $regis->competitionClass->name }}</span>
                                        <span class="flex items-center gap-1 sm:gap-1.5"><i
                                                class="fas fa-users opacity-60"></i>{{ $regis->team->name }}</span>
                                        <span class="flex items-center gap-1 sm:gap-1.5 hidden sm:flex"><i
                                                class="far fa-clock opacity-60"></i>{{ $regis->created_at->translatedFormat('d M y · H:i') }} น.</span>
                                    </div>
                                </div>
                            </div>

                            {{-- ฝั่งขวา: ราคาและปุ่ม Action --}}
                            <div class="shrink-0 flex flex-col sm:justify-center items-start sm:items-end gap-2 sm:gap-3 pt-3 sm:pt-0 border-t sm:border-t-0 sm:border-l sm:pl-6 w-full sm:w-auto transition-colors duration-300 border-white/5"
                                :class="selectedItems.includes({{ $regis->id }}) ?
                                    'sm:border-blue-500/20' : ''">

                                @if (in_array($regis->status, ['pending_payment', 'rejected']))
                                    <div class="text-left sm:text-right w-full sm:w-auto flex flex-row sm:flex-col justify-between sm:justify-start items-center sm:items-end">
                                        <p class="text-[9px] sm:text-[10px] font-normal mb-0 sm:mb-0.5 transition-colors duration-300"
                                            :class="selectedItems.includes({{ $regis->id }}) ?
                                                'text-blue-400/70' : 'text-gray-500'">
                                            ยอดชำระเงิน</p>
                                        <p class="text-lg sm:text-xl font-normal leading-none transition-colors duration-300"
                                            :class="selectedItems.includes({{ $regis->id }}) ?
                                                'text-blue-400' :
                                                'text-red-400'">
                                            {{ $regis->competitionClass->entry_fee == 0 ? 'ฟรี' : number_format($regis->competitionClass->entry_fee) . ' ฿' }}
                                        </p>
                                    </div>

                                    <div class="flex flex-col sm:flex-row gap-2 mt-1 w-full sm:w-auto">
                                        <form action="{{ route('user.registrations.destroy', $regis->id) }}"
                                            method="POST" class="w-full sm:w-auto delete-form relative z-10"
                                            @click.stop>
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="delete-btn flex items-center justify-center gap-1.5 sm:gap-2 px-3 py-2 sm:px-4 sm:py-2 bg-[#0a0a0a] hover:bg-red-500/10 text-gray-400 hover:text-red-400 text-[10px] sm:text-xs font-normal rounded-lg sm:rounded-xl border border-white/10 hover:border-red-500/30 transition-colors w-full focus:outline-none">
                                                <i class="fas fa-trash-alt"></i> ยกเลิกการลงทะเบียน
                                            </button>
                                        </form>
                                    </div>

                                    @if ($regis->status === 'rejected')
                                        <div
                                            class="mt-1 sm:mt-2 p-2 sm:p-3 bg-red-500/10 rounded-lg sm:rounded-xl border border-red-500/20 text-left sm:text-right w-full sm:max-w-[200px]">
                                            <p
                                                class="text-[9px] sm:text-[10px] font-normal text-red-400 uppercase tracking-wide mb-1 flex items-center sm:justify-end gap-1.5">
                                                <i class="fas fa-exclamation-circle"></i> สาเหตุที่ไม่อนุมัติ
                                            </p>
                                            <p
                                                class="text-[10px] sm:text-xs text-gray-300 font-normal break-words overflow-hidden">
                                                {{ $regis->paymentTransaction->reject_reason ?? 'เอกสารไม่ถูกต้อง กรุณาติดต่อผู้ดูแลระบบ' }}
                                            </p>
                                        </div>
                                    @endif
                                @elseif ($regis->status === 'waiting_verify')
                                    <div
                                        class="inline-flex items-center gap-1.5 sm:gap-2 px-3 py-2 sm:px-4 sm:py-2.5 bg-amber-500/10 text-amber-400 text-[10px] sm:text-xs font-normal rounded-lg sm:rounded-xl border border-amber-500/20 w-full sm:w-auto justify-center">
                                        <i class="fas fa-hourglass-half animate-pulse"></i> อยู่ระหว่างการตรวจสอบ
                                    </div>
                                @elseif ($regis->status === 'approved')
                                    <div class="flex flex-col sm:flex-row items-center gap-2 sm:gap-2.5 w-full sm:w-auto">
                                        @if (!empty($regis->checked_in_at))
                                            <div title="เช็คอินเมื่อ: {{ \Carbon\Carbon::parse($regis->checked_in_at)->format('H:i น.') }}"
                                                class="inline-flex items-center justify-center gap-1.5 px-3 py-2 sm:px-4 sm:py-2.5 bg-blue-600 text-white text-[10px] sm:text-xs font-normal rounded-lg sm:rounded-xl shadow-sm w-full sm:w-auto cursor-default">
                                                <i class="fas fa-user-check"></i> ยืนยันการเข้าร่วมงานแล้ว
                                            </div>
                                        @endif
                                        <a href="{{ route('user.registrations.e-ticket', $regis->id) }}"
                                            target="_blank"
                                            class="inline-flex items-center justify-center gap-1.5 sm:gap-2 px-4 py-2 sm:px-5 sm:py-2.5 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 text-[10px] sm:text-xs font-normal rounded-lg sm:rounded-xl border border-emerald-500/20 transition-colors w-full sm:w-auto focus:outline-none">
                                            <i class="far fa-id-card text-xs sm:text-sm"></i> พิมพ์บัตรประจำตัว
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach

                <div x-show="filterStatus !== 'all' && document.querySelectorAll('[x-show][style*=\'display: none\']').length === {{ $registrations->count() }}"
                    style="display:none"
                    class="py-12 sm:py-16 text-center bg-[#121212] rounded-2xl">
                    <i class="far fa-folder-open text-2xl sm:text-3xl text-gray-700 mb-2 sm:mb-3"></i>
                    <p class="text-xs sm:text-sm font-normal text-gray-500 mb-2 sm:mb-3">ไม่พบรายการในสถานะที่ท่านเลือก</p>
                    <button @click="filterStatus = 'all'"
                        class="text-xs sm:text-sm font-normal text-blue-400 hover:text-blue-300 hover:underline focus:outline-none">แสดงรายการทั้งหมด</button>
                </div>
            </div>
        @endif


        {{-- 🚀 แถบสรุปยอดรวม (Cart Summary Bar) --}}
        <div x-show="selectedItems.length > 0" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-full" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-full"
            class="fixed bottom-0 left-0 right-0 z-40 bg-[#1a1a1a]/95 backdrop-blur-xl border-t border-white/10 shadow-[0_-15px_40px_-15px_rgba(0,0,0,0.5)] py-3 sm:py-4 px-4 sm:px-8 rounded-t-2xl sm:rounded-t-3xl">

            <div class="max-w-4xl mx-auto flex items-center justify-between">
                <div>
                    <p class="text-[10px] sm:text-xs font-normal text-gray-400 mb-0.5">รวมรายการที่เลือก (<span
                            class="font-normal text-blue-400"
                            x-text="selectedItems.length"></span> รายการ)</p>
                    <p
                        class="text-xl sm:text-3xl font-normal text-white leading-none tracking-tight">
                        <span x-text="new Intl.NumberFormat('th-TH').format(totalFee)"></span> <span
                            class="text-base sm:text-lg font-normal text-gray-500">฿</span>
                    </p>
                </div>

                <button type="button" @click="$dispatch('open-payment-modal')"
                    class="inline-flex items-center justify-center gap-1.5 sm:gap-2 px-5 sm:px-8 py-2.5 sm:py-3.5 bg-blue-600 hover:bg-blue-500 text-white text-xs sm:text-base font-normal rounded-xl sm:rounded-2xl shadow-lg transition-all active:scale-95 focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                    <i class="fas fa-wallet text-xs sm:text-sm"></i> <span class="hidden sm:inline">ดำเนินการ</span>ชำระเงิน
                </button>
            </div>
        </div>

        {{-- ===== 🚀 PAYMENT MODAL ===== --}}
        <div @open-payment-modal.window="$dispatch('open-modal', 'payment-modal');">

            <x-modal name="payment-modal" focusable maxWidth="3xl">
                {{-- 🚀 ย้าย x-data เข้ามาไว้ในนี้ เพื่อแก้ปัญหา Alpine หลุด Scope --}}
                <div x-data="{
                        slipPreview: null,
                        copied: false,
                        requestTax: false, 
                        handleFile(event) {
                            const file = event.target.files?.[0] ?? event.dataTransfer?.files?.[0];
                            this.slipPreview = (file && file.type.startsWith('image/')) ? URL.createObjectURL(file) : null;
                        },
                        copyAccount() {
                            navigator.clipboard.writeText('123-4-56789-0').then(() => {
                                this.copied = true;
                                setTimeout(() => this.copied = false, 2000);
                            });
                        }
                    }"
                    @open-modal.window="if($event.detail == 'payment-modal') { slipPreview = null; copied = false; requestTax = false; }"
                    class="bg-[#121212] rounded-t-[1.5rem] sm:rounded-[2rem] overflow-hidden flex flex-col max-h-[90vh] font-kanit border border-white/10 shadow-2xl">

                    {{-- ── HEADER ── --}}
                    <div class="flex items-center justify-between px-5 sm:px-8 py-4 sm:py-5 shrink-0 border-b border-white/5 bg-[#0a0a0a]">
                        <div>
                            <p class="text-[10px] sm:text-xs text-gray-500 font-normal mb-0.5">การชำระเงินค่าลงทะเบียนแบบรวมรายการ</p>
                            <h2 class="text-base sm:text-lg font-normal text-white">รวม <span x-text="selectedItems.length" class="text-blue-400"></span> รายการ</h2>
                        </div>
                        <button @click="$dispatch('close-modal', 'payment-modal')" type="button"
                            class="w-8 h-8 sm:w-10 sm:h-10 flex items-center justify-center rounded-xl bg-[#1a1a1a] text-gray-500 border border-white/5 hover:text-white hover:bg-white/10 transition-colors focus:outline-none shadow-sm">
                            <i class="fas fa-times text-sm sm:text-base"></i>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('user.registrations.payment') }}" enctype="multipart/form-data" class="flex flex-col min-h-0 overflow-hidden">
                        @csrf

                        <template x-for="id in selectedItems" :key="id">
                            <input type="hidden" name="registration_ids[]" :value="id">
                        </template>

                        <div class="flex-1 overflow-y-auto custom-scrollbar p-5 sm:p-8">

                            {{-- FREE flow --}}
                            <template x-if="totalFee == 0">
                                <div class="flex flex-col items-center justify-center py-8 sm:py-10 text-center">
                                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-emerald-500/10 rounded-[1.5rem] flex items-center justify-center mb-4 sm:mb-5 border border-emerald-500/20">
                                        <i class="fas fa-gift text-2xl sm:text-3xl text-emerald-400"></i>
                                    </div>
                                    <h3 class="text-lg sm:text-xl font-normal text-white mb-1.5 sm:mb-2">รายการทั้งหมดนี้ไม่มีค่าธรรมเนียมการลงทะเบียน</h3>
                                    <p class="text-xs sm:text-sm text-gray-500 font-normal">กรุณากดยืนยันด้านล่างเพื่อส่งข้อมูลให้แก่ผู้ดูแลระบบ</p>
                                </div>
                            </template>

                            {{-- PAID flow --}}
                            <template x-if="totalFee > 0">
                                <div class="space-y-6 sm:space-y-8">
                                    
                                    {{-- ส่วนของการสแกน QR และโอนเงิน --}}
                                    <div class="flex flex-col md:flex-row gap-6 sm:gap-8 min-h-0">
                                        {{-- ── LEFT: QR Panel ── --}}
                                        <div class="md:w-64 shrink-0 flex flex-col items-center justify-center">
                                            <p class="text-[9px] sm:text-[10px] font-normal text-gray-500 uppercase tracking-widest mb-1.5 sm:mb-2">ยอดสุทธิที่ต้องชำระ</p>
                                            <p class="text-2xl sm:text-3xl font-normal text-blue-400 leading-none mb-5 sm:mb-6">
                                                <span x-text="new Intl.NumberFormat('th-TH').format(totalFee)"></span><span class="text-base sm:text-lg font-normal ml-1 text-gray-500">฿</span>
                                            </p>
                                            <div class="p-2 sm:p-3 bg-white rounded-xl sm:rounded-2xl w-full max-w-[180px] sm:max-w-[220px]">
                                                <img src="{{ asset('images/qr-code-payment.jpg') }}" alt="QR Code ชำระเงิน" class="w-full h-auto object-contain rounded-lg sm:rounded-xl">
                                            </div>
                                            <p class="text-[10px] sm:text-xs font-normal text-gray-500 mt-3 sm:mt-4 flex items-center gap-1.5"><i class="fas fa-qrcode"></i> สแกนเพื่อชำระเงิน</p>
                                        </div>

                                        {{-- ── RIGHT: Bank + Slip ── --}}
                                        <div class="flex-1 flex flex-col min-w-0 border-t md:border-t-0 md:border-l border-white/5 pt-5 md:pt-0 md:pl-8">
                                            <div class="mb-5 sm:mb-6">
                                                <p class="text-[9px] sm:text-[10px] font-normal text-gray-500 uppercase tracking-widest mb-2.5 sm:mb-3">
                                                    <i class="fas fa-university mr-1"></i> หรือโอนเงินผ่านบัญชีธนาคาร
                                                </p>
                                                <div class="bg-[#1a1a1a] border border-white/5 rounded-xl sm:rounded-2xl p-3 sm:p-4 flex items-center justify-between gap-3 sm:gap-4 shadow-sm">
                                                    <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                                                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-[#1ba642] flex items-center justify-center shrink-0">
                                                            <i class="fas fa-leaf text-white text-xs sm:text-base"></i>
                                                        </div>
                                                        <div class="min-w-0">
                                                            <p class="text-xs sm:text-sm font-normal text-white truncate">ธ.กสิกรไทย</p>
                                                            <p class="text-[10px] sm:text-xs font-normal text-gray-500 truncate">บจก. อีซี่คิดส์ โรโบติกส์</p>
                                                        </div>
                                                    </div>
                                                    <div class="mt-2.5 sm:mt-3 flex items-center gap-2 bg-[#0a0a0a] border border-white/5 rounded-lg sm:rounded-xl pl-3 sm:pl-4 pr-1 sm:pr-1.5 py-1 sm:py-1.5">
                                                        <span class="flex-1 font-mono font-normal text-sm sm:text-base text-white tracking-widest truncate">123-4-56789-0</span>
                                                        <button type="button" @click="copyAccount()" class="shrink-0 flex items-center justify-center w-7 h-7 sm:w-8 sm:h-8 rounded-md sm:rounded-lg transition-colors focus:outline-none" :class="copied ? 'bg-emerald-500/20 text-emerald-400' : 'bg-[#1a1a1a] text-gray-400 hover:text-white'">
                                                            <i class="fas text-[10px] sm:text-sm" :class="copied ? 'fa-check' : 'fa-copy'"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="flex-1 flex flex-col">
                                                <p class="text-xs sm:text-sm font-normal text-white mb-1.5 sm:mb-2">แนบหลักฐานการชำระเงิน <span class="text-red-500">*</span></p>
                                                <div class="relative flex-1 min-h-[120px] sm:min-h-[140px] rounded-xl sm:rounded-2xl border border-dashed transition-all cursor-pointer bg-[#0f0f0f] flex items-center justify-center overflow-hidden group shadow-sm"
                                                    :class="slipPreview ? 'border-emerald-500/50' : 'border-white/10 hover:border-blue-500/50 hover:bg-[#1a1a1a]'"
                                                    @dragover.prevent="$el.classList.add('border-blue-500/50','bg-[#1a1a1a]')"
                                                    @dragleave.prevent="$el.classList.remove('border-blue-500/50','bg-[#1a1a1a]')"
                                                    @drop.prevent="$refs.slipFile.files = $event.dataTransfer.files; handleFile($event); $el.classList.remove('border-blue-500/50','bg-[#1a1a1a]')">
                                                    <input type="file" name="payment_slip" x-ref="slipFile" accept="image/jpeg,image/png,image/jpg" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20" required @change="handleFile">
                                                    
                                                    <div x-show="!slipPreview" class="text-center p-3 sm:p-4">
                                                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-[#1a1a1a] border border-white/5 flex items-center justify-center mx-auto mb-2 sm:mb-3 group-hover:scale-110 transition-transform">
                                                            <i class="fas fa-image text-gray-500 group-hover:text-blue-400 transition-colors text-sm sm:text-base"></i>
                                                        </div>
                                                        <p class="text-[10px] sm:text-xs font-normal text-gray-500">คลิกหรือลากไฟล์หลักฐานมาวางบริเวณนี้</p>
                                                    </div>

                                                    <div x-show="slipPreview" style="display:none" class="absolute inset-0 z-10 p-1.5 sm:p-2">
                                                        <img :src="slipPreview" class="w-full h-full object-contain rounded-lg sm:rounded-xl bg-[#0a0a0a]">
                                                        <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center rounded-lg sm:rounded-xl backdrop-blur-sm">
                                                            <span class="text-white text-[10px] sm:text-xs font-normal bg-black/80 px-2.5 py-1 sm:px-3 sm:py-1.5 rounded-full border border-white/10"><i class="fas fa-sync-alt mr-1"></i> เปลี่ยนรูปภาพ</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- 🚀 TAX INVOICE SECTION (แบบฝังตัวแปรตรงๆ) --}}
                                    <div class="border-t border-white/5 pt-6 sm:pt-8 pb-4">
                                        <label class="flex items-start sm:items-center gap-3 cursor-pointer group w-full bg-[#1a1a1a]/50 p-4 rounded-xl border border-white/5 hover:border-blue-500/30 transition-colors">
                                            <div class="relative flex items-center justify-center w-5 h-5 rounded border-2 transition-all mt-0.5 sm:mt-0 shrink-0"
                                                :class="requestTax ? 'border-blue-500 bg-blue-500' : 'border-gray-600 bg-[#0a0a0a]'">
                                                {{-- Checkbox ตรงนี้จะทำการสลับค่า requestTax --}}
                                                <input type="checkbox" name="is_tax_invoice_requested" x-model="requestTax" value="1" class="absolute opacity-0 cursor-pointer w-full h-full">
                                                <svg x-show="requestTax" class="w-3.5 h-3.5 text-white pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-sm sm:text-base text-white font-normal transition-colors" :class="requestTax ? 'text-blue-400' : ''">ต้องการขอรับใบกำกับภาษี / ใบเสร็จรับเงิน</p>
                                                <p class="text-[10px] sm:text-xs text-gray-500 font-normal mt-0.5">ระบบจะจัดส่งเอกสารให้ทางอีเมลที่ระบุไว้ด้านล่าง</p>
                                            </div>
                                        </label>

                                        {{-- แบบฟอร์มข้อมูลภาษี (จะโชว์เมื่อติ๊กถูก) --}}
                                        <div x-show="requestTax" x-transition:enter="transition ease-out duration-300"
                                            x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                                            class="mt-4 bg-[#0f0f0f] p-4 sm:p-6 rounded-xl border border-blue-500/20 space-y-4">
                                            
                                            <div>
                                                <label class="block text-xs sm:text-sm text-gray-400 font-normal mb-1.5">ชื่อบริษัท / นิติบุคคล / ชื่อ-นามสกุล <span class="text-red-500">*</span></label>
                                                <input type="text" name="tax_payer_name" :required="requestTax" placeholder="ระบุชื่อเพื่อออกใบกำกับภาษี"
                                                    class="w-full bg-[#1a1a1a] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500/50 outline-none transition-colors">
                                            </div>

                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-xs sm:text-sm text-gray-400 font-normal mb-1.5">เลขประจำตัวผู้เสียภาษี (13 หลัก) <span class="text-red-500">*</span></label>
                                                    <input type="text" name="tax_id" :required="requestTax" maxlength="13" placeholder="เลข 13 หลัก"
                                                        class="w-full bg-[#1a1a1a] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500/50 outline-none transition-colors">
                                                </div>
                                                <div>
                                                    <label class="block text-xs sm:text-sm text-gray-400 font-normal mb-1.5">สาขา <span class="text-red-500">*</span></label>
                                                    <input type="text" name="tax_payer_branch" :required="requestTax" placeholder="เช่น สำนักงานใหญ่ หรือ 00001"
                                                        class="w-full bg-[#1a1a1a] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500/50 outline-none transition-colors">
                                                </div>
                                            </div>

                                            <div>
                                                <label class="block text-xs sm:text-sm text-gray-400 font-normal mb-1.5">ที่อยู่จดทะเบียน <span class="text-red-500">*</span></label>
                                                <textarea name="tax_payer_address" :required="requestTax" rows="2" placeholder="ระบุที่อยู่ให้ครบถ้วน"
                                                    class="w-full bg-[#1a1a1a] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500/50 outline-none transition-colors resize-none"></textarea>
                                            </div>

                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-xs sm:text-sm text-gray-400 font-normal mb-1.5">เบอร์โทรศัพท์ติดต่อ <span class="text-red-500">*</span></label>
                                                    <input type="text" name="tax_payer_phone" :required="requestTax" placeholder="เบอร์ติดต่อผู้ประสานงาน" value="{{ auth()->user()->phone_number }}"
                                                        class="w-full bg-[#1a1a1a] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500/50 outline-none transition-colors">
                                                </div>
                                                <div>
                                                    <label class="block text-xs sm:text-sm text-gray-400 font-normal mb-1.5">อีเมลสำหรับจัดส่งเอกสาร <span class="text-red-500">*</span></label>
                                                    <input type="email" name="tax_payer_email" :required="requestTax" placeholder="example@email.com" value="{{ auth()->user()->email }}"
                                                        class="w-full bg-[#1a1a1a] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500/50 outline-none transition-colors">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- ข้อความแจ้งเตือน --}}
                        <div class="px-5 sm:px-8 pb-2">
                            <p class="text-[10px] sm:text-xs text-amber-400 bg-amber-500/10 p-3 sm:p-4 rounded-xl border border-amber-500/20 leading-relaxed">
                                <i class="fas fa-exclamation-triangle mr-1.5"></i> <strong>กรุณาตรวจสอบข้อมูลสมาชิกและชื่อทีมให้ถูกต้องก่อนยืนยันการลงทะเบียน</strong><br>
                                <span class="opacity-80 pl-4 sm:pl-5 block mt-0.5">เมื่อท่านกดยืนยัน ระบบจะถือว่าท่านได้ตรวจสอบความถูกต้องของข้อมูลทั้งหมดแล้ว ขอให้ทุกทีมเตรียมความพร้อมสำหรับการแข่งขัน และขออวยพรให้ท่านประสบความสำเร็จ 🏆</span>
                            </p>
                        </div>

                        {{-- ── FOOTER ── --}}
                        <div class="px-5 sm:px-8 py-4 sm:py-5 border-t border-white/5 bg-[#0a0a0a] shrink-0 flex gap-2.5 sm:gap-3">
                            <button type="button" @click="$dispatch('close-modal', 'payment-modal')"
                                class="flex-1 py-2.5 sm:py-3.5 text-xs sm:text-sm font-normal text-gray-400 bg-[#1a1a1a] hover:bg-white/5 rounded-xl sm:rounded-2xl transition-colors focus:outline-none border border-white/5">
                                ยกเลิก
                            </button>
                            <button type="submit"
                                class="flex-[2] py-2.5 sm:py-3.5 bg-blue-600 hover:bg-blue-500 text-white text-xs sm:text-sm font-normal rounded-xl sm:rounded-2xl transition-colors focus:outline-none">
                                <span x-text="totalFee > 0 ? 'ยืนยันและส่งหลักฐานการชำระเงิน' : 'ยืนยันการลงทะเบียน'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </x-modal>
        </div>
    </div>
    
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; height: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
    </style>
</x-user-layout>