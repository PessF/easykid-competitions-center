<x-user-layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 font-kanit" x-data="{ filterStatus: 'all' }">

        {{-- ===== HEADER ===== --}}
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-1">
                <div class="w-1.5 h-6 bg-blue-600 rounded-full"></div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white tracking-tight">ประวัติการสมัครแข่งขัน
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
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">ยังไม่มีประวัติการสมัคร</h3>
                <p class="text-sm text-gray-500 mb-8">คุณยังไม่ได้ลงทะเบียนเข้าร่วมการแข่งขันใดๆ</p>
                <a href="{{ route('user.dashboard') }}"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-2xl transition-colors shadow-lg shadow-blue-500/20">
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
                        class="shrink-0 flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-medium transition-all focus:outline-none border whitespace-nowrap"
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
                                    class="ml-1 text-[10px] font-bold opacity-60 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 px-1.5 py-0.5 rounded-md">{{ $cnt }}</span>
                            @endif
                        @endif
                    </button>
                @endforeach
            </div>

            {{-- REGISTRATION LIST --}}
            <div class="space-y-4">
                @foreach ($registrations as $regis)
                    @php
                        // ตรวจสอบความถูกต้องของทีมซ้ำ (Real-time Validation)
                        $isValidTeam = true;
                        $invalidReason = '';
                        $class = $regis->competitionClass;
                        $team = $regis->team;

                        // 1. เช็คช่วงจำนวนคน (min-max)
                        $memberCount = $team->members->count();
                        $min = $class->min_members ?? 1;
                        $max = $class->max_members;

                        if ($memberCount < $min || $memberCount > $max) {
                            $isValidTeam = false;
                            $invalidReason =
                                $min === $max
                                    ? "จำนวนสมาชิกเปลี่ยนไป (ต้องมี {$max} คนพอดี)"
                                    : "จำนวนสมาชิกเปลี่ยนไป (ต้องมี {$min}-{$max} คน)";
                        }

                        // 2. เช็คอายุ
                        if ($isValidTeam && !empty($class->allowed_categories)) {
                            $cats = is_string($class->allowed_categories)
                                ? json_decode($class->allowed_categories, true)
                                : $class->allowed_categories;
                            if (!empty($cats)) {
                                $minAge = collect($cats)->min('min_age');
                                $maxAge = collect($cats)->max('max_age');
                                foreach ($team->members as $member) {
                                    if (!$member->birth_date) {
                                        $isValidTeam = false;
                                        $invalidReason = 'พบสมาชิกไม่ได้ระบุวันเกิด';
                                        break;
                                    }
                                    $age = \Carbon\Carbon::parse($member->birth_date)->age;
                                    if ($age < $minAge || $age > $maxAge) {
                                        $isValidTeam = false;
                                        $invalidReason = 'มีสมาชิกอายุไม่เข้าเกณฑ์กติกาปัจจุบัน';
                                        break;
                                    }
                                }
                            }
                        }

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
                        class="group bg-white dark:bg-[#141414] border border-gray-100 dark:border-gray-800 rounded-2xl overflow-hidden hover:shadow-sm transition-all duration-300 flex relative">

                        <div class="absolute left-0 top-0 bottom-0 w-1.5 {{ $statusConfig['strip'] }}"></div>

                        <div
                            class="flex-1 p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center gap-5 min-w-0 pl-6 sm:pl-8">
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2.5 mb-3">
                                    <span
                                        class="text-[10px] font-mono font-semibold px-2.5 py-1 rounded-md bg-gray-50 dark:bg-white/5 text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-800 tracking-widest">
                                        {{ $regis->regis_no }}
                                    </span>
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-medium border {{ $statusConfig['pill'] }}">
                                        <i class="fas {{ $statusConfig['icon'] }}"></i> {{ $statusConfig['text'] }}
                                    </span>
                                </div>

                                <h3
                                    class="text-base sm:text-lg font-medium text-gray-900 dark:text-white leading-snug mb-2 truncate">
                                    {{ $regis->competition->name }}
                                </h3>

                                <div
                                    class="flex flex-wrap items-center gap-x-5 gap-y-2 text-xs text-gray-500 dark:text-gray-400 font-normal">
                                    <span class="flex items-center gap-1.5"><i
                                            class="fas fa-robot text-gray-300"></i>{{ $regis->competitionClass->name }}</span>
                                    <span class="flex items-center gap-1.5"><i
                                            class="fas fa-users text-gray-300"></i>{{ $regis->team->name }}</span>
                                    <span class="flex items-center gap-1.5"><i
                                            class="far fa-clock text-gray-300"></i>{{ $regis->created_at->translatedFormat('d M y · H:i') }}
                                        น.</span>
                                </div>
                            </div>

                            <div
                                class="shrink-0 flex flex-col sm:items-end gap-3 pt-4 sm:pt-0 border-t sm:border-t-0 sm:border-l border-gray-100 dark:border-gray-800 sm:pl-6">
                                @if ($regis->status === 'pending_payment')
                                    <div class="text-left sm:text-right">
                                        <p class="text-[10px] font-medium text-gray-400 mb-0.5">ยอดที่ต้องชำระ</p>
                                        <p class="text-xl font-semibold text-red-500 dark:text-red-400 leading-none">
                                            {{ $regis->competitionClass->entry_fee == 0 ? 'ฟรี' : number_format($regis->competitionClass->entry_fee) . ' ฿' }}
                                        </p>
                                    </div>

                                    {{-- ตรวจสอบสถานะทีมก่อนให้กดจ่ายเงิน --}}
                                    @if (!$isValidTeam)
                                        <div class="text-left sm:text-right w-full mt-1">
                                            <p
                                                class="text-[10px] font-semibold text-red-500 flex items-center sm:justify-end gap-1">
                                                <i class="fas fa-exclamation-triangle"></i> {{ $invalidReason }}
                                            </p>
                                            <a href="{{ route('user.teams.index') }}"
                                                class="text-[10px] font-medium text-blue-500 hover:underline">คลิกเพื่อแก้ไขทีม</a>
                                        </div>
                                        <button disabled
                                            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gray-100 dark:bg-[#111] text-gray-400 text-xs font-medium rounded-xl border border-gray-200 dark:border-gray-800 w-full sm:w-auto mt-1 cursor-not-allowed">
                                            <i class="fas fa-lock"></i> ไม่สามารถชำระเงินได้
                                        </button>
                                    @else
                                        <button type="button"
                                            @click="$dispatch('open-payment-modal', { id: '{{ $regis->id }}', no: '{{ $regis->regis_no }}', fee: {{ $regis->competitionClass->entry_fee }}, compName: '{{ addslashes($regis->competition->name) }}' })"
                                            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl transition-colors shadow-sm w-full sm:w-auto mt-1">
                                            <i class="fas fa-wallet"></i> ชำระเงิน
                                        </button>
                                    @endif
                                @elseif ($regis->status === 'waiting_verify')
                                    <div
                                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 text-xs font-medium rounded-xl border border-amber-100 dark:border-amber-500/20 w-full sm:w-auto justify-center">
                                        <i class="fas fa-hourglass-half animate-pulse"></i> กำลังรอตรวจสอบ
                                    </div>
                                @elseif ($regis->status === 'approved')
                                    <a href="{{ route('user.registrations.e-ticket', $regis->id) }}" target="_blank"
                                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-emerald-50 dark:bg-emerald-500/10 hover:bg-emerald-100 dark:hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-xs font-medium rounded-xl border border-emerald-100 dark:border-emerald-500/20 transition-colors w-full sm:w-auto">
                                        <i class="far fa-id-card text-sm"></i> พิมพ์บัตรประจำตัว
                                    </a>
                                @elseif ($regis->status === 'rejected')
                                    <div class="flex flex-col gap-3 w-full sm:max-w-xs">
                                        <div
                                            class="bg-red-50 dark:bg-red-500/10 p-3 sm:p-4 rounded-xl border border-red-100 dark:border-red-500/20 text-left sm:text-right">
                                            <p
                                                class="text-[10px] font-semibold text-red-600 dark:text-red-400 uppercase tracking-wide mb-1 flex items-center sm:justify-end gap-1.5">
                                                <i class="fas fa-exclamation-circle"></i> สาเหตุที่ถูกปฏิเสธ
                                            </p>
                                            <p
                                                class="text-xs text-gray-700 dark:text-gray-300 font-normal leading-relaxed mb-3">
                                                {{ $regis->reject_reason ?? 'เอกสารไม่ถูกต้อง กรุณาติดต่อแอดมิน' }}
                                            </p>

                                            <button type="button"
                                                @click="$dispatch('open-payment-modal', { 
                    id: '{{ $regis->id }}', 
                    no: '{{ $regis->regis_no }}', 
                    fee: {{ $regis->competitionClass->entry_fee }}, 
                    compName: '{{ addslashes($regis->competition->name) }}',
                    isResubmit: true
                })"
                                                class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-[11px] font-bold rounded-lg transition-all shadow-sm w-full">
                                                <i class="fas fa-sync-alt"></i> แก้ไขและส่งสลิปใหม่
                                            </button>
                                        </div>
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
                    <p class="text-sm font-medium text-gray-500 mb-3">ไม่พบรายการในสถานะที่คุณเลือก</p>
                    <button @click="filterStatus = 'all'"
                        class="text-sm font-medium text-blue-600 hover:underline focus:outline-none">ดูรายการทั้งหมด</button>
                </div>
            </div>
        @endif

        {{-- 🚀 PDPA / Data Retention Info (มินิมอล ไม่รกสายตา) --}}
        <div
            class="mt-12 pt-6 border-t border-gray-100 dark:border-gray-800/60 flex flex-col items-center justify-center text-center opacity-80 hover:opacity-100 transition-opacity">
            <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-100 dark:bg-white/5 rounded-md mb-2.5">
                <i class="fas fa-shield-alt text-gray-400 text-[10px]"></i>
                <span
                    class="text-[9px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Privacy
                    & Policy</span>
            </div>
            <p class="text-[11px] text-gray-400 dark:text-gray-500 max-w-lg leading-relaxed">
                ระบบจะจัดเก็บข้อมูลการสมัครและเอกสารแนบของท่านไว้อย่างปลอดภัย <br class="hidden sm:block">
                และจะถูกลบออกจากระบบโดยอัตโนมัติภายใน <span
                    class="font-medium text-gray-600 dark:text-gray-300 underline decoration-gray-300 dark:decoration-gray-700 underline-offset-2">1
                    เดือน</span> หลังจากการแข่งขันเสร็จสิ้นลง
            </p>
        </div>

    </div>

    {{-- ===== PAYMENT MODAL ===== --}}
    <div x-data="{
        regisId: '',
        regisNo: '',
        fee: 0,
        compName: '',
        isResubmit: false,
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
        @open-payment-modal.window="
             regisId = $event.detail.id;
             regisNo = $event.detail.no;
             fee = $event.detail.fee;
             compName = $event.detail.compName;
             isResubmit = $event.detail.isResubmit || false;
             slipPreview = null;
             copied = false;
             $dispatch('open-modal', 'payment-modal');
         ">

        <x-modal name="payment-modal" focusable maxWidth="3xl">
            <div
                class="bg-white dark:bg-[#141414] rounded-t-[2rem] sm:rounded-[2rem] overflow-hidden flex flex-col max-h-[90vh] font-kanit border border-gray-100 dark:border-gray-800 shadow-2xl">

                {{-- ── HEADER ── --}}
                <div
                    class="flex items-center justify-between px-6 sm:px-8 py-5 shrink-0 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-white/5">
                    <div>
                        <p class="text-xs text-gray-500 font-medium mb-0.5" x-text="regisNo"></p>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white">ชำระเงินค่าสมัคร</h2>
                    </div>
                    <button @click="$dispatch('close-modal', 'payment-modal')" type="button"
                        class="w-10 h-10 flex items-center justify-center rounded-full bg-white dark:bg-[#111] text-gray-400 border border-gray-200 dark:border-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors focus:outline-none">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form method="POST" :action="`/my-registrations/${regisId}/payment`" enctype="multipart/form-data"
                    class="flex flex-col min-h-0 overflow-hidden">
                    @csrf

                    <div class="flex-1 overflow-y-auto ek-scrollbar hide-scroll p-6 sm:p-8">

                        {{-- FREE flow --}}
                        <template x-if="fee == 0">
                            <div class="flex flex-col items-center justify-center py-10 text-center">
                                <div
                                    class="w-20 h-20 bg-emerald-50 dark:bg-emerald-500/10 rounded-full flex items-center justify-center mb-5">
                                    <i class="fas fa-gift text-3xl text-emerald-500"></i>
                                </div>
                                <h3 class="text-xl font-medium text-gray-900 dark:text-white mb-2">
                                    รายการนี้ไม่มีค่าใช้จ่าย!</h3>
                                <p class="text-sm text-gray-500 font-normal">
                                    กดยืนยันด้านล่างเพื่อเสร็จสิ้นขั้นตอนการสมัครได้เลยครับ</p>
                            </div>
                        </template>

                        {{-- PAID flow --}}
                        <template x-if="fee > 0">
                            <div class="flex flex-col md:flex-row gap-8 min-h-0">

                                {{-- ── LEFT: QR Panel ── --}}
                                <div class="md:w-64 shrink-0 flex flex-col items-center justify-center">
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">
                                        ยอดที่ต้องชำระ</p>
                                    <p
                                        class="text-3xl font-semibold text-blue-600 dark:text-blue-400 leading-none mb-6">
                                        <span x-text="new Intl.NumberFormat('th-TH').format(fee)"></span><span
                                            class="text-lg font-medium ml-1">฿</span>
                                    </p>

                                    <div
                                        class="p-3 bg-white rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-700 shadow-sm w-full max-w-[220px]">
                                        <img src="{{ asset('images/qr-code-payment.jpg') }}" alt="QR Code ชำระเงิน"
                                            class="w-full h-auto object-contain rounded-xl">
                                    </div>
                                    <p class="text-xs font-medium text-gray-500 mt-4 flex items-center gap-1.5"><i
                                            class="fas fa-qrcode"></i> สแกนเพื่อจ่าย</p>
                                </div>

                                {{-- ── RIGHT: Bank + Slip ── --}}
                                <div
                                    class="flex-1 flex flex-col min-w-0 border-t md:border-t-0 md:border-l border-gray-100 dark:border-gray-800 pt-6 md:pt-0 md:pl-8">

                                    {{-- Bank account --}}
                                    <div class="mb-6">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">
                                            <i class="fas fa-university mr-1"></i> หรือโอนเข้าบัญชี
                                        </p>
                                        <div
                                            class="bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-gray-800 rounded-2xl p-4 flex items-center justify-between gap-4">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-10 h-10 rounded-full bg-[#1ba642] flex items-center justify-center shrink-0">
                                                    <i class="fas fa-leaf text-white"></i>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        ธ.กสิกรไทย</p>
                                                    <p class="text-xs font-normal text-gray-500">บจก. อีซี่คิดส์
                                                        โรโบติกส์</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            class="mt-3 flex items-center gap-2 bg-white dark:bg-[#111] border border-gray-200 dark:border-gray-700 rounded-xl pl-4 pr-1.5 py-1.5">
                                            <span
                                                class="flex-1 font-mono font-medium text-base text-gray-900 dark:text-white tracking-widest">123-4-56789-0</span>
                                            <button type="button" @click="copyAccount()"
                                                class="shrink-0 flex items-center justify-center w-8 h-8 rounded-lg transition-colors focus:outline-none"
                                                :class="copied ?
                                                    'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400' :
                                                    'bg-gray-100 text-gray-500 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400'">
                                                <i class="fas text-sm" :class="copied ? 'fa-check' : 'fa-copy'"></i>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Upload slip --}}
                                    <div class="flex-1 flex flex-col">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white mb-2">แนบสลิปยืนยัน
                                            <span class="text-red-500">*</span>
                                        </p>

                                        <div class="relative flex-1 min-h-[140px] rounded-2xl border-2 border-dashed transition-all cursor-pointer bg-gray-50 dark:bg-white/5 flex items-center justify-center overflow-hidden group"
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
                                                    <i class="fas fa-image text-gray-400"></i>
                                                </div>
                                                <p class="text-sm font-medium text-gray-600 dark:text-gray-300">
                                                    คลิกเพื่อเลือกไฟล์สลิป</p>
                                            </div>

                                            <div x-show="slipPreview" style="display:none"
                                                class="absolute inset-0 z-10 p-2">
                                                <img :src="slipPreview"
                                                    class="w-full h-full object-contain rounded-xl">
                                                <div
                                                    class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center rounded-xl">
                                                    <span
                                                        class="text-white text-xs font-medium bg-black/50 px-3 py-1.5 rounded-full"><i
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
                            class="flex-1 py-3.5 text-sm font-medium text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 rounded-2xl transition-colors focus:outline-none">
                            ยกเลิก
                        </button>
                        <button type="submit"
                            class="flex-[2] py-3.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-2xl transition-colors shadow-lg shadow-blue-500/20 focus:outline-none">
                            <span x-text="fee > 0 ? 'ยืนยันและส่งหลักฐาน' : 'ยืนยันการเข้าร่วม'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </x-modal>
    </div>
</x-user-layout>
