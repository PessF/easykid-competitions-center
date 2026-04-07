<x-user-layout>
    {{-- 🚀 1. เพิ่ม State ของ Cart (ตะกร้า) เข้าไปใน Alpine.js --}}
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 font-kanit pb-32" x-data="{
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
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-1">
                <div class="w-1.5 h-6 bg-blue-600 rounded-full"></div>
                <h1 class="text-2xl font-normal text-gray-900 dark:text-white tracking-tight">ประวัติการสมัครแข่งขัน
                </h1>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 ml-4 pl-1">รายการแข่งขันที่คุณได้ลงทะเบียนไว้ทั้งหมด</p>
        </div>

        @if ($registrations->isEmpty())
            <div
                class="flex flex-col items-center justify-center py-20 bg-white dark:bg-[#141414] border border-gray-100 dark:border-gray-800 rounded-[2rem] text-center shadow-sm">
                <div
                    class="w-20 h-20 bg-blue-50 dark:bg-blue-950/30 rounded-[1.5rem] flex items-center justify-center mb-5">
                    <i class="fas fa-folder-open text-3xl text-blue-500"></i>
                </div>
                <h3 class="text-lg font-normal text-gray-900 dark:text-white mb-2">ยังไม่มีประวัติการสมัคร</h3>
                <p class="text-sm text-gray-500 mb-8">คุณยังไม่ได้ลงทะเบียนเข้าร่วมการแข่งขันใดๆ</p>
                <a href="{{ route('user.dashboard') }}"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-normal rounded-2xl transition-colors shadow-lg shadow-blue-500/20">
                    <i class="fas fa-search text-xs"></i> ค้นหางานแข่งขัน
                </a>
            </div>
        @else
            {{-- FILTER TABS (Pills Style) --}}
            <div class="flex items-center gap-2 mb-6 overflow-x-auto hide-scroll pb-2 -mx-1 px-1">
                @php
                    $filterOptions = [
                        'all' => ['label' => 'ทั้งหมด', 'dot' => ''],
                        'pending_payment' => ['label' => 'รอชำระเงิน', 'dot' => 'bg-red-400'],
                        'waiting_verify' => ['label' => 'รอตรวจสอบ', 'dot' => 'bg-amber-400'],
                        'approved' => ['label' => 'อนุมัติแล้ว', 'dot' => 'bg-emerald-400'],
                        'rejected' => ['label' => 'ถูกปฏิเสธ/ยกเลิก', 'dot' => 'bg-gray-400'],
                    ];
                @endphp
                @foreach ($filterOptions as $val => $opt)
                    <button @click="filterStatus = '{{ $val }}'" type="button"
                        class="shrink-0 flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-normal transition-all focus:outline-none border whitespace-nowrap"
                        :class="filterStatus === '{{ $val }}'
                            ?
                            'bg-gray-900 dark:bg-white text-white dark:text-gray-900 border-transparent shadow-sm' :
                            'bg-white dark:bg-[#141414] text-gray-600 dark:text-gray-400 border-gray-200 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-white/5'">
                        @if ($opt['dot'])
                            <span class="w-1.5 h-1.5 rounded-full {{ $opt['dot'] }} shrink-0"></span>
                        @endif
                        {{ $opt['label'] }}
                        @if ($val !== 'all')
                            @php $cnt = $registrations->where('status', $val)->count(); @endphp
                            @if ($cnt > 0)
                                <span
                                    class="ml-1 text-[10px] font-normal opacity-60 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 px-1.5 py-0.5 rounded-md">{{ $cnt }}</span>
                            @endif
                        @endif
                    </button>
                @endforeach
            </div>

            {{-- REGISTRATION LIST --}}
            <div class="space-y-4">
                @foreach ($registrations as $regis)
                    @php
                        // ตั้งค่าสีและสถานะ UI
                        $statusConfig = match ($regis->status) {
                            'pending_payment' => [
                                'pill' =>
                                    'bg-red-50 text-red-600 border-red-100 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20',
                                'icon' => 'fa-file-invoice-dollar',
                                'text' => 'รอชำระเงิน',
                                'strip' => 'bg-red-500',
                            ],
                            'waiting_verify' => [
                                'pill' =>
                                    'bg-amber-50 text-amber-600 border-amber-100 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20',
                                'icon' => 'fa-hourglass-half',
                                'text' => 'รอตรวจสอบสลิป',
                                'strip' => 'bg-amber-400',
                            ],
                            'approved' => [
                                'pill' =>
                                    'bg-emerald-50 text-emerald-600 border-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20',
                                'icon' => 'fa-check-circle',
                                'text' => 'อนุมัติแล้ว',
                                'strip' => 'bg-emerald-500',
                            ],
                            'rejected' => [
                                'pill' =>
                                    'bg-gray-100 text-gray-500 border-gray-200 dark:bg-white/5 dark:text-gray-400 dark:border-gray-800',
                                'icon' => 'fa-times-circle',
                                'text' => 'ถูกปฏิเสธ/ยกเลิก',
                                'strip' => 'bg-gray-400',
                            ],
                            default => [
                                'pill' =>
                                    'bg-gray-100 text-gray-500 border-gray-200 dark:bg-white/5 dark:text-gray-400 dark:border-gray-800',
                                'icon' => 'fa-info-circle',
                                'text' => 'ไม่ทราบสถานะ',
                                'strip' => 'bg-gray-300',
                            ],
                        };
                    @endphp

                    <div x-show="filterStatus === 'all' || filterStatus === '{{ $regis->status }}'"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="border rounded-2xl overflow-hidden flex relative transition-colors duration-300"
                        :class="selectedItems.includes({{ $regis->id }}) ?
                            'bg-blue-50/50 dark:bg-blue-900/10 border-blue-400 shadow-sm' :
                            'bg-white dark:bg-[#141414] border-gray-100 dark:border-gray-800'"
                        @if (in_array($regis->status, ['pending_payment', 'rejected'])) @click="if(isSelectable({{ $regis->competition_id }})) toggleSelect({{ $regis->id }}, {{ $regis->competitionClass->entry_fee }}, {{ $regis->competition_id }})"
                            class="cursor-pointer" @endif>

                        <div class="absolute left-0 top-0 bottom-0 w-1.5 transition-colors duration-300"
                            :class="selectedItems.includes({{ $regis->id }}) ? 'bg-blue-500' :
                                '{{ $statusConfig['strip'] }}'">
                        </div>

                        <div
                            class="flex-1 p-5 sm:p-6 flex flex-col sm:flex-row gap-5 min-w-0 pl-6 sm:pl-8 items-start sm:items-center">

                            {{-- 🚀 CHECKBOX CART SYSTEM --}}
                            @if (in_array($regis->status, ['pending_payment', 'rejected']))
                                <div class="shrink-0 pt-1" @click.stop>
                                    <div class="relative flex items-center justify-center w-6 h-6 rounded-md border-2 transition-all duration-200 cursor-pointer"
                                        :class="selectedItems.includes({{ $regis->id }}) ?
                                            'border-blue-500 bg-blue-500' :
                                            (!isSelectable({{ $regis->competition_id }}) ?
                                                'border-gray-200 bg-gray-50 opacity-50 cursor-not-allowed dark:border-gray-700 dark:bg-gray-800' :
                                                'border-gray-300 bg-white dark:border-gray-600 dark:bg-gray-800')"
                                        @click="if(isSelectable({{ $regis->competition_id }})) toggleSelect({{ $regis->id }}, {{ $regis->competitionClass->entry_fee }}, {{ $regis->competition_id }})">

                                        <svg x-show="selectedItems.includes({{ $regis->id }})"
                                            class="w-4 h-4 text-white pointer-events-none" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                </div>
                            @endif

                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2.5 mb-3">
                                    <span
                                        class="text-[10px] font-mono font-normal px-2.5 py-1 rounded-md tracking-widest transition-colors duration-300"
                                        :class="selectedItems.includes({{ $regis->id }}) ?
                                            'border border-blue-200 bg-white dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' :
                                            'bg-gray-50 dark:bg-white/5 text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-800'">
                                        {{ $regis->regis_no }}
                                    </span>
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-normal border {{ $statusConfig['pill'] }} transition-colors duration-300"
                                        :class="selectedItems.includes({{ $regis->id }}) ?
                                            'border-blue-200 bg-blue-50 text-blue-600 dark:border-blue-500/30 dark:bg-blue-500/10 dark:text-blue-400' :
                                            ''">
                                        <i class="fas {{ $statusConfig['icon'] }}"></i> {{ $statusConfig['text'] }}
                                    </span>
                                </div>

                                <h3 class="text-base sm:text-lg font-normal leading-snug mb-2 truncate transition-colors duration-300"
                                    :class="selectedItems.includes({{ $regis->id }}) ?
                                        'text-blue-700 dark:text-blue-400 font-medium' : 'text-gray-900 dark:text-white'">
                                    {{ $regis->competition->name }}
                                </h3>

                                <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-xs font-normal transition-colors duration-300"
                                    :class="selectedItems.includes({{ $regis->id }}) ?
                                        'text-blue-600/70 dark:text-blue-400/70' : 'text-gray-500 dark:text-gray-400'">
                                    <span class="flex items-center gap-1.5"><i
                                            class="fas fa-robot opacity-60"></i>{{ $regis->competitionClass->name }}</span>
                                    <span class="flex items-center gap-1.5"><i
                                            class="fas fa-users opacity-60"></i>{{ $regis->team->name }}</span>
                                    <span class="flex items-center gap-1.5"><i
                                            class="far fa-clock opacity-60"></i>{{ $regis->created_at->translatedFormat('d M y · H:i') }}
                                        น.</span>
                                </div>
                            </div>

                            <div class="shrink-0 flex flex-col sm:items-end gap-3 pt-4 sm:pt-0 border-t sm:border-t-0 sm:border-l sm:pl-6 w-full sm:w-auto transition-colors duration-300"
                                :class="selectedItems.includes({{ $regis->id }}) ?
                                    'border-blue-100 dark:border-blue-900/30' : 'border-gray-100 dark:border-gray-800'">

                                @if (in_array($regis->status, ['pending_payment', 'rejected']))
                                    <div class="text-left sm:text-right">
                                        <p class="text-[10px] font-normal mb-0.5 transition-colors duration-300"
                                            :class="selectedItems.includes({{ $regis->id }}) ?
                                                'text-blue-500/70 dark:text-blue-400/70' : 'text-gray-400'">
                                            ยอดที่ต้องชำระ</p>
                                        <p class="text-xl font-medium leading-none transition-colors duration-300"
                                            :class="selectedItems.includes({{ $regis->id }}) ?
                                                'text-blue-600 dark:text-blue-400 font-semibold' :
                                                'text-red-500 dark:text-red-400'">
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
                                                class="delete-btn inline-flex items-center justify-center gap-2 px-4 py-2 bg-white dark:bg-white/5 hover:bg-red-50 dark:hover:bg-red-500/10 text-red-500 text-xs font-normal rounded-xl border border-gray-200 dark:border-gray-800 transition-colors w-full sm:w-auto">
                                                <i class="fas fa-trash-alt"></i> ยกเลิกใบสมัคร
                                            </button>
                                        </form>
                                    </div>

                                    @if ($regis->status === 'rejected')
                                        <div
                                            class="mt-2 p-3 bg-red-50 dark:bg-red-500/10 rounded-xl border border-red-100 dark:border-red-500/20 text-left sm:text-right w-full sm:max-w-xs">
                                            <p
                                                class="text-[10px] font-normal text-red-600 dark:text-red-400 uppercase tracking-wide mb-1 flex items-center sm:justify-end gap-1.5">
                                                <i class="fas fa-exclamation-circle"></i> สาเหตุที่ถูกปฏิเสธ
                                            </p>
                                            <p
                                                class="text-xs text-gray-700 dark:text-gray-300 font-normal break-words overflow-hidden">
                                                {{ $regis->paymentTransaction->reject_reason ?? 'เอกสารไม่ถูกต้อง กรุณาติดต่อแอดมิน' }}
                                            </p>
                                        </div>
                                    @endif
                                @elseif ($regis->status === 'waiting_verify')
                                    <div
                                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 text-xs font-normal rounded-xl border border-amber-100 dark:border-amber-500/20 w-full sm:w-auto justify-center">
                                        <i class="fas fa-hourglass-half animate-pulse"></i> กำลังรอตรวจสอบ
                                    </div>
                                @elseif ($regis->status === 'approved')
                                    <div class="flex flex-col sm:flex-row items-center gap-2.5 w-full sm:w-auto">
                                        @if (!empty($regis->checked_in_at))
                                            <div title="เช็คอินเมื่อ: {{ \Carbon\Carbon::parse($regis->checked_in_at)->format('H:i น.') }}"
                                                class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-blue-600 text-white text-xs font-normal rounded-xl shadow-sm shadow-blue-500/20 w-full sm:w-auto cursor-default">
                                                <i class="fas fa-user-check"></i> เข้างานแล้ว
                                            </div>
                                        @endif
                                        <a href="{{ route('user.registrations.e-ticket', $regis->id) }}"
                                            target="_blank"
                                            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-emerald-50 dark:bg-emerald-500/10 hover:bg-emerald-100 dark:hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-xs font-normal rounded-xl border border-emerald-100 dark:border-emerald-500/20 transition-colors w-full sm:w-auto">
                                            <i class="far fa-id-card text-sm"></i> พิมพ์บัตรประจำตัว
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach

                <div x-show="filterStatus !== 'all' && document.querySelectorAll('[x-show][style*=\'display: none\']').length === {{ $registrations->count() }}"
                    style="display:none"
                    class="py-16 text-center bg-white dark:bg-[#141414] border border-gray-100 dark:border-gray-800 rounded-2xl">
                    <i class="far fa-folder-open text-3xl text-gray-300 dark:text-gray-700 mb-3"></i>
                    <p class="text-sm font-normal text-gray-500 mb-3">ไม่พบรายการในสถานะที่คุณเลือก</p>
                    <button @click="filterStatus = 'all'"
                        class="text-sm font-normal text-blue-600 hover:underline focus:outline-none">ดูรายการทั้งหมด</button>
                </div>
            </div>
        @endif

        <div
            class="mt-12 pt-6 border-t border-gray-100 dark:border-gray-800/60 flex flex-col items-center justify-center text-center opacity-80 hover:opacity-100 transition-opacity">
            <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-100 dark:bg-white/5 rounded-md mb-2.5">
                <i class="fas fa-shield-alt text-gray-400 text-[10px]"></i>
                <span class="text-[9px] font-normal text-gray-500 dark:text-gray-400 uppercase tracking-widest">Privacy
                    & Policy</span>
            </div>
            <p class="text-[11px] text-gray-400 dark:text-gray-500 max-w-lg leading-relaxed font-normal">
                ระบบจะจัดเก็บข้อมูลการสมัครและเอกสารแนบของท่านไว้อย่างปลอดภัย <br class="hidden sm:block">
                และจะถูกลบออกจากระบบโดยอัตโนมัติภายใน <span
                    class="font-normal text-gray-600 dark:text-gray-300 underline decoration-gray-300 dark:decoration-gray-700 underline-offset-2">1
                    เดือน</span> หลังจากการแข่งขันเสร็จสิ้นลง
            </p>
        </div>

        {{-- 🚀 3. แถบสรุปยอดรวม (Cart Summary Bar) ดีไซน์ใหม่ --}}
        <div x-show="selectedItems.length > 0" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-full" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-full"
            class="fixed bottom-0 left-0 right-0 z-40 bg-white/90 dark:bg-[#1a1a1a]/90 backdrop-blur-xl border-t border-gray-200 dark:border-gray-800 shadow-[0_-15px_40px_-15px_rgba(59,130,246,0.3)] dark:shadow-[0_-15px_40px_-15px_rgba(59,130,246,0.1)] py-4 px-4 sm:px-8 rounded-t-3xl">

            <div class="max-w-4xl mx-auto flex items-center justify-between">
                <div>
                    <p class="text-xs font-normal text-gray-500 dark:text-gray-400 mb-0.5">รวมบิลที่เลือก (<span
                            class="font-semibold text-blue-600 dark:text-blue-400"
                            x-text="selectedItems.length"></span> รายการ)</p>
                    <p
                        class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white leading-none tracking-tight">
                        <span x-text="new Intl.NumberFormat('th-TH').format(totalFee)"></span> <span
                            class="text-lg font-normal text-gray-500">฿</span>
                    </p>
                </div>

                <button type="button" @click="$dispatch('open-payment-modal')"
                    class="inline-flex items-center justify-center gap-2 px-6 sm:px-8 py-3 sm:py-3.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-sm sm:text-base font-normal rounded-2xl shadow-xl shadow-blue-500/30 transition-all transform hover:scale-[1.02] active:scale-95 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <i class="fas fa-wallet text-sm"></i> <span class="hidden sm:inline">ดำเนินการ</span>ชำระเงิน
                </button>
            </div>
        </div>

        {{-- ===== 🚀 4. PAYMENT MODAL ===== --}}
        <div x-data="{
            slipPreview: null,
            copied: false,
            handleFile(event) {
                const file = event.target.files?.[0] ?? event.dataTransfer?.files?.[0];
                this.slipPreview = (file && file.type.startsWith('image/')) ? URL.createObjectURL(file) : null;
            },
            copyAccount() {
                navigator.clipboard.writeText('6789112790').then(() => {
                    this.copied = true;
                    setTimeout(() => this.copied = false, 2000);
                });
            }
        }"
            @open-payment-modal.window="slipPreview = null; copied = false; $dispatch('open-modal', 'payment-modal');">

            <x-modal name="payment-modal" focusable maxWidth="3xl">
                <div
                    class="bg-white dark:bg-[#141414] rounded-t-[2rem] sm:rounded-[2rem] overflow-hidden flex flex-col max-h-[90vh] font-kanit border border-gray-100 dark:border-gray-800 shadow-2xl">

                    {{-- ── HEADER ── --}}
                    <div
                        class="flex items-center justify-between px-6 sm:px-8 py-5 shrink-0 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-white/5">
                        <div>
                            <p class="text-xs text-gray-500 font-normal mb-0.5">ชำระเงินค่าสมัครแบบรวมบิล</p>
                            <h2 class="text-lg font-normal text-gray-900 dark:text-white">รวม <span
                                    x-text="selectedItems.length"></span> รายการ</h2>
                        </div>
                        <button @click="$dispatch('close-modal', 'payment-modal')" type="button"
                            class="w-10 h-10 flex items-center justify-center rounded-full bg-white dark:bg-[#111] text-gray-400 border border-gray-200 dark:border-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors focus:outline-none shadow-sm">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    {{-- 🚀 Form Action ชี้ไปที่ Group Payment Route --}}
                    <form method="POST" action="{{ route('user.registrations.payment') }}"
                        enctype="multipart/form-data" class="flex flex-col min-h-0 overflow-hidden">
                        @csrf

                        {{-- 🚀 ส่ง ID ของใบสมัครทั้งหมดที่เลือกไปด้วยเป็น Array --}}
                        <template x-for="id in selectedItems" :key="id">
                            <input type="hidden" name="registration_ids[]" :value="id">
                        </template>

                        <div class="flex-1 overflow-y-auto ek-scrollbar hide-scroll p-6 sm:p-8">

                            {{-- FREE flow --}}
                            <template x-if="totalFee == 0">
                                <div class="flex flex-col items-center justify-center py-10 text-center">
                                    <div
                                        class="w-20 h-20 bg-emerald-50 dark:bg-emerald-500/10 rounded-full flex items-center justify-center mb-5 shadow-sm">
                                        <i class="fas fa-gift text-3xl text-emerald-500"></i>
                                    </div>
                                    <h3 class="text-xl font-normal text-gray-900 dark:text-white mb-2">
                                        รายการทั้งหมดนี้ไม่มีค่าใช้จ่าย!</h3>
                                    <p class="text-sm text-gray-500 font-normal">
                                        กดยืนยันด้านล่างเพื่อส่งข้อมูลให้ทีมงานได้เลยครับ</p>
                                </div>
                            </template>

                            {{-- PAID flow --}}
                            <template x-if="totalFee > 0">
                                <div class="flex flex-col md:flex-row gap-8 min-h-0">

                                    {{-- ── LEFT: QR Panel ── --}}
                                    <div class="md:w-64 shrink-0 flex flex-col items-center justify-center">
                                        <p
                                            class="text-[10px] font-normal text-gray-400 uppercase tracking-widest mb-2">
                                            ยอดที่ต้องชำระสุทธิ</p>
                                        <p
                                            class="text-3xl font-semibold text-blue-600 dark:text-blue-400 leading-none mb-6">
                                            <span x-text="new Intl.NumberFormat('th-TH').format(totalFee)"></span><span
                                                class="text-lg font-normal ml-1">฿</span>
                                        </p>

                                        <div
                                            class="p-3 bg-white rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-700 shadow-sm w-full max-w-[220px]">
                                            <img src="{{ asset('images/qr-code-payment.jpg') }}"
                                                alt="QR Code ชำระเงิน"
                                                class="w-full h-auto object-contain rounded-xl">
                                        </div>
                                        <p class="text-xs font-normal text-gray-500 mt-4 flex items-center gap-1.5"><i
                                                class="fas fa-qrcode"></i> สแกนเพื่อจ่าย</p>
                                    </div>

                                    {{-- ── RIGHT: Bank + Slip ── --}}
                                    <div
                                        class="flex-1 flex flex-col min-w-0 border-t md:border-t-0 md:border-l border-gray-100 dark:border-gray-800 pt-6 md:pt-0 md:pl-8">

                                        {{-- Bank account --}}
                                        <div class="mb-6">
                                            <p
                                                class="text-[10px] font-normal text-gray-400 uppercase tracking-widest mb-3">
                                                <i class="fas fa-university mr-1"></i> หรือโอนเข้าบัญชี
                                            </p>
                                            <div
                                                class="bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-gray-800 rounded-2xl p-4 flex items-center justify-between gap-4 shadow-sm">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="w-10 h-10 rounded-full bg-[#1ba642] flex items-center justify-center shrink-0 shadow-inner">
                                                        <i class="fas fa-leaf text-white"></i>
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                                            ธ.กสิกรไทย</p>
                                                        <p class="text-xs font-normal text-gray-500">บจก. อีซี่คิดส์
                                                            โรโบติกส์</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div
                                                class="mt-3 flex items-center gap-2 bg-white dark:bg-[#111] border border-gray-200 dark:border-gray-700 rounded-xl pl-4 pr-1.5 py-1.5 shadow-sm">
                                                <span
                                                    class="flex-1 font-mono font-normal text-base text-gray-900 dark:text-white tracking-widest">123-4-56789-0</span>
                                                <button type="button" @click="copyAccount()"
                                                    class="shrink-0 flex items-center justify-center w-8 h-8 rounded-lg transition-colors focus:outline-none"
                                                    :class="copied ?
                                                        'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400' :
                                                        'bg-gray-100 text-gray-500 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400'">
                                                    <i class="fas text-sm"
                                                        :class="copied ? 'fa-check' : 'fa-copy'"></i>
                                                </button>
                                            </div>
                                        </div>

                                        {{-- Upload slip --}}
                                        <div class="flex-1 flex flex-col">
                                            <p class="text-sm font-normal text-gray-900 dark:text-white mb-2">
                                                แนบสลิปยืนยัน <span class="text-red-500">*</span></p>

                                            <div class="relative flex-1 min-h-[140px] rounded-2xl border-2 border-dashed transition-all cursor-pointer bg-gray-50 dark:bg-white/5 flex items-center justify-center overflow-hidden group shadow-sm"
                                                :class="slipPreview ? 'border-emerald-400 dark:border-emerald-600' :
                                                    'border-gray-200 dark:border-gray-700 hover:border-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/10'"
                                                @dragover.prevent="$el.classList.add('border-blue-400','bg-blue-50','dark:bg-blue-900/10')"
                                                @dragleave.prevent="$el.classList.remove('border-blue-400','bg-blue-50','dark:bg-blue-900/10')"
                                                @drop.prevent="$refs.slipFile.files = $event.dataTransfer.files; handleFile($event); $el.classList.remove('border-blue-400','bg-blue-50','dark:bg-blue-900/10')">

                                                <input type="file" name="payment_slip" x-ref="slipFile"
                                                    accept="image/jpeg,image/png,image/jpg"
                                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20"
                                                    required @change="handleFile">

                                                <div x-show="!slipPreview" class="text-center p-4">
                                                    <div
                                                        class="w-12 h-12 rounded-full bg-white dark:bg-gray-800 shadow-sm flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
                                                        <i
                                                            class="fas fa-image text-gray-400 group-hover:text-blue-500 transition-colors"></i>
                                                    </div>
                                                    <p class="text-sm font-normal text-gray-600 dark:text-gray-300">
                                                        คลิกหรือลากไฟล์สลิปมาวาง</p>
                                                </div>

                                                <div x-show="slipPreview" style="display:none"
                                                    class="absolute inset-0 z-10 p-2">
                                                    <img :src="slipPreview"
                                                        class="w-full h-full object-contain rounded-xl">
                                                    <div
                                                        class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center rounded-xl">
                                                        <span
                                                            class="text-white text-xs font-normal bg-black/50 px-3 py-1.5 rounded-full"><i
                                                                class="fas fa-sync-alt mr-1"></i> เปลี่ยนรูป</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- ── FOOTER ── --}}
                        <div
                            class="px-6 sm:px-8 py-5 border-t border-gray-100 dark:border-gray-800 bg-white dark:bg-[#141414] shrink-0 flex gap-3">
                            <button type="button" @click="$dispatch('close-modal', 'payment-modal')"
                                class="flex-1 py-3.5 text-sm font-normal text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 rounded-2xl transition-colors focus:outline-none">
                                ยกเลิก
                            </button>
                            <button type="submit"
                                class="flex-[2] py-3.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-sm font-normal rounded-2xl transition-colors shadow-lg shadow-blue-500/20 focus:outline-none">
                                <span x-text="totalFee > 0 ? 'ยืนยันและส่งหลักฐาน' : 'ยืนยันรายการ'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </x-modal>
        </div>
    </div>
</x-user-layout>