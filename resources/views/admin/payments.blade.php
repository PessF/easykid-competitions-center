<x-admin-layout>
    <x-slot name="title">ตรวจสอบการชำระเงิน | Payments</x-slot>

    @php
        // เตรียมข้อมูลรายชื่องานแข่งให้ Alpine.js
        $alpineCompetitions = $competitions
            ->map(function ($comp) {
                return [
                    'id' => (string) $comp->id,
                    'name' => $comp->name,
                ];
            })
            ->values()
            ->all();
    @endphp

    <div class="min-h-screen bg-[#0a0a0a] font-kanit p-4 sm:p-6" x-data="paymentsPage()">
        <div class="max-w-7xl mx-auto">

            {{-- Page Header --}}
            <div class="mb-8">
                <h1 class="text-3xl font-normal text-white">ตรวจสอบการชำระเงิน</h1>
                <p class="text-gray-400 mt-2 font-normal">จัดการและอนุมัติหลักฐานการโอนเงิน</p>
            </div>

            {{-- Stats Cards --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                @php
                    $statCards = [
                        [
                            'label' => 'รอตรวจสอบ',
                            'count' => $stats['waiting_verify'] ?? 0,
                            'class' => 'amber',
                            'icon' => 'fa-clock',
                            'val' => 'waiting_verify',
                        ],
                        [
                            'label' => 'อนุมัติแล้ว',
                            'count' => $stats['approved'] ?? 0,
                            'class' => 'emerald',
                            'icon' => 'fa-check-circle',
                            'val' => 'approved',
                        ],
                        [
                            'label' => 'ปฏิเสธ',
                            'count' => $stats['rejected'] ?? 0,
                            'class' => 'red',
                            'icon' => 'fa-times-circle',
                            'val' => 'rejected',
                        ],
                        [
                            'label' => 'บิลทั้งหมด',
                            'count' => $stats['all'] ?? 0,
                            'class' => 'blue',
                            'icon' => 'fa-layer-group',
                            'val' => 'all',
                        ],
                    ];
                @endphp
                @foreach ($statCards as $card)
                    <div @click="applyFilter('status', '{{ $card['val'] }}')"
                        class="bg-[#121212] rounded-2xl p-4 border border-white/5 shadow-sm cursor-pointer hover:border-{{ $card['class'] }}-500/50 transition-all group">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-400 font-normal">{{ $card['label'] }}</p>
                                <p class="text-2xl font-normal text-white mt-1">{{ $card['count'] }}</p>
                            </div>
                            <div
                                class="w-10 h-10 rounded-lg bg-{{ $card['class'] }}-500/10 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <i class="fas {{ $card['icon'] }} text-{{ $card['class'] }}-400 text-sm"></i>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Filters Section --}}
            <div class="bg-[#121212] rounded-2xl border border-white/5 p-4 mb-6 shadow-sm">
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        {{-- 🔍 Search Box --}}
                        <div class="md:col-span-1">
                            <label class="block text-sm font-normal text-gray-300 mb-2">ค้นหา</label>
                            <div class="relative">
                                <input type="text" x-model="search" @keyup.enter="applyFilter('search', search)"
                                    placeholder="รหัสบิล, ชื่อผู้โอน, ชื่อทีม..."
                                    class="w-full px-4 py-2.5 text-sm border border-white/10 rounded-xl bg-[#0f0f0f] text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors font-normal placeholder-gray-600">
                                <button @click="applyFilter('search', search)"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 p-1.5 text-gray-500 hover:text-blue-400 transition-colors">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Status Filter Dropdown --}}
                        <div x-data="{ open: false }" class="relative">
                            <label class="block text-sm font-normal text-gray-300 mb-2">สถานะ</label>
                            <button @click="open = !open" @click.outside="open = false" type="button"
                                class="w-full flex items-center justify-between px-4 py-2.5 text-sm border border-white/10 rounded-xl bg-[#0f0f0f] text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors font-normal">
                                <span x-text="statusLabels[statusFilter] || 'รอตรวจสอบ'"></span>
                                <i class="fas fa-chevron-down text-gray-500 text-xs transition-transform duration-200"
                                    :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="open" x-transition.opacity.duration.200ms x-cloak
                                class="absolute z-20 w-full mt-2 bg-[#1a1a1a] border border-white/10 rounded-xl shadow-lg overflow-hidden py-1">
                                <template x-for="(label, val) in statusLabels" :key="val">
                                    <button @click="applyFilter('status', val); open = false" type="button"
                                        class="w-full text-left px-4 py-2.5 text-sm transition-colors flex items-center justify-between"
                                        :class="statusFilter === val ? 'bg-blue-500/20 text-blue-400 font-normal' :
                                            'text-gray-300 hover:bg-white/5 font-normal'">
                                        <span x-text="label"></span>
                                        <i x-show="statusFilter === val" class="fas fa-check text-blue-400"></i>
                                    </button>
                                </template>
                            </div>
                        </div>

                        {{-- Competition Filter --}}
                        <div x-data="{ open: false }" class="relative">
                            <label class="block text-sm font-normal text-gray-300 mb-2">งานแข่งขัน</label>
                            <div class="flex items-center gap-3">
                                <div class="relative w-full">
                                    <button @click="open = !open" @click.outside="open = false" type="button"
                                        class="w-full flex items-center justify-between px-4 py-2.5 text-sm border border-white/10 rounded-xl bg-[#0f0f0f] text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors font-normal">
                                        <span class="truncate pr-4 font-normal"
                                            x-text="getCompName(competitionFilter)"></span>
                                        <i class="fas fa-chevron-down text-gray-500 text-xs transition-transform duration-200"
                                            :class="open ? 'rotate-180' : ''"></i>
                                    </button>
                                    <div x-show="open" x-transition.opacity.duration.200ms x-cloak
                                        class="absolute z-20 w-full mt-2 bg-[#1a1a1a] border border-white/10 rounded-xl shadow-lg overflow-y-auto max-h-60 custom-scrollbar py-1">
                                        <button @click="applyFilter('competition_id', ''); open = false" type="button"
                                            class="w-full text-left px-4 py-2.5 text-sm transition-colors flex items-center justify-between"
                                            :class="competitionFilter === '' ?
                                                'bg-blue-500/20 text-blue-400 font-normal' :
                                                'text-gray-300 hover:bg-white/5 font-normal'">
                                            <span class="truncate pr-2 font-normal">ทั้งหมด</span>
                                            <i x-show="competitionFilter === ''"
                                                class="fas fa-check text-blue-400 shrink-0"></i>
                                        </button>
                                        <template x-for="comp in compList" :key="comp.id">
                                            <button @click="applyFilter('competition_id', comp.id); open = false"
                                                type="button"
                                                class="w-full text-left px-4 py-2.5 text-sm transition-colors flex items-center justify-between"
                                                :class="competitionFilter === comp.id ?
                                                    'bg-blue-500/20 text-blue-400 font-normal' :
                                                    'text-gray-300 hover:bg-white/5 font-normal'">
                                                <span class="truncate pr-2 font-normal" x-text="comp.name"></span>
                                                <i x-show="competitionFilter === comp.id"
                                                    class="fas fa-check text-blue-400 shrink-0"></i>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                                <button @click="clearFilters()" type="button"
                                    class="px-4 py-2.5 bg-white/5 hover:bg-white/10 text-gray-300 text-sm font-normal rounded-xl transition-all whitespace-nowrap">ล้างค่า</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Status Tabs --}}
            <div
                class="flex flex-wrap items-center bg-[#121212] border border-white/5 rounded-xl p-1 w-full lg:w-auto shrink-0 z-10 mb-6">
                @foreach ([
        'waiting_verify' => ['label' => 'รอตรวจสอบ', 'icon' => 'fa-clock'],
        'approved' => ['label' => 'อนุมัติแล้ว', 'icon' => 'fa-check-circle'],
        'rejected' => ['label' => 'ไม่ผ่าน', 'icon' => 'fa-times-circle'],
        'all' => ['label' => 'ทั้งหมด', 'icon' => 'fa-list'],
    ] as $val => $data)
                    <button @click="applyFilter('status', '{{ $val }}')"
                        :class="statusFilter === '{{ $val }}' ?
                            'bg-[#1a1a1a] text-white shadow-sm font-normal border-white/10' :
                            'text-gray-500 hover:text-gray-300 font-normal border-transparent'"
                        class="flex items-center gap-1.5 px-4 py-2 text-sm rounded-lg border transition-all flex-1 sm:flex-none justify-center whitespace-nowrap">
                        <i class="fas {{ $data['icon'] }} text-xs opacity-70"></i>
                        {{ $data['label'] }}
                    </button>
                @endforeach
            </div>

            {{-- Data Table --}}
            <div class="bg-[#121212] rounded-2xl border border-white/5 shadow-sm overflow-hidden">
                <div class="overflow-x-auto custom-scrollbar pb-3">
                    <table class="w-full min-w-[850px]">
                        <thead>
                            <tr class="bg-[#0a0a0a] border-b border-white/5">
                                <th
                                    class="px-6 py-4 text-left text-xs font-normal text-gray-400 uppercase tracking-wider whitespace-nowrap">
                                    รหัสบิล / ผู้โอน</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-normal text-gray-400 uppercase tracking-wider whitespace-nowrap min-w-[200px]">
                                    งานแข่งขัน</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-normal text-gray-400 uppercase tracking-wider whitespace-nowrap">
                                    ยอดชำระสุทธิ</th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-normal text-gray-400 uppercase tracking-wider whitespace-nowrap">
                                    สถานะ</th>
                                <th
                                    class="px-6 py-4 text-right text-xs font-normal text-gray-400 uppercase tracking-wider whitespace-nowrap">
                                    การจัดการ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 relative">
                            @forelse ($transactions as $tx)
                                <tr class="hover:bg-white/[0.02] transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-mono font-normal text-blue-400">
                                            {{ $tx->tx_no }}
                                            @if ($tx->is_tax_invoice_requested)
                                                <span
                                                    class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-normal bg-blue-500/20 text-blue-400"
                                                    title="ขอใบกำกับภาษี">
                                                    <i class="fas fa-file-invoice"></i>
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-400 mt-1 font-normal">
                                            <i class="far fa-user mr-1"></i>{{ $tx->user->name ?? '-' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-300 font-normal">
                                        {{ $tx->competition->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-normal text-white">
                                            {{ number_format($tx->total_amount) }} ฿
                                        </div>
                                        <div class="text-xs text-gray-400 mt-1 font-normal">
                                            จ่ายสำหรับ {{ $tx->registrations->count() }} ทีม
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center font-normal whitespace-nowrap">
                                        @php
                                            $statusMap = [
                                                'waiting_verify' => [
                                                    'badge' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                                                    'text' => 'รอตรวจสอบ',
                                                ],
                                                'approved' => [
                                                    'badge' =>
                                                        'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                                    'text' => 'อนุมัติแล้ว',
                                                ],
                                                'rejected' => [
                                                    'badge' => 'bg-red-500/10 text-red-400 border-red-500/20',
                                                    'text' => 'ปฏิเสธ',
                                                ],
                                            ];
                                            $status = $statusMap[$tx->status] ?? [
                                                'badge' => 'bg-white/5 text-gray-400 border-white/10',
                                                'text' => $tx->status,
                                            ];
                                        @endphp
                                        <span
                                            class="inline-flex px-3 py-1.5 text-xs font-normal rounded-lg border {{ $status['badge'] }}">{{ $status['text'] }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap">
                                        <button type="button"
                                            @click="openModal({
                                                id: '{{ $tx->id }}',
                                                txNo: '{{ $tx->tx_no }}',
                                                userName: {{ Js::from($tx->user->name ?? '-') }},
                                                compName: {{ Js::from($tx->competition->name ?? '-') }},
                                                totalAmount: {{ $tx->total_amount }},
                                                teamCount: {{ $tx->registrations->count() }},
                                                slipUrl: '{{ $tx->payment_slip_path ? route('admin.payments.slip', $tx->id) : '' }}',
                                                status: '{{ $tx->status }}',
                                                rejectReason: {{ Js::from($tx->reject_reason) }},
                                                actionUrl: '{{ route('admin.payments.update', $tx->id) }}',
                                                isTaxRequested: {{ $tx->is_tax_invoice_requested ? 'true' : 'false' }},
                                                taxName: {{ Js::from($tx->tax_payer_name) }},
                                                taxId: {{ Js::from($tx->tax_id) }},
                                                taxBranch: {{ Js::from($tx->tax_payer_branch) }},
                                                taxAddress: {{ Js::from($tx->tax_payer_address) }},
                                                taxPhone: {{ Js::from($tx->tax_payer_phone) }},
                                                taxEmail: {{ Js::from($tx->tax_payer_email) }},
                                                teamList: [
                                                    @foreach ($tx->registrations as $reg)
                                                        { 
                                                            teamName: {{ Js::from($reg->team->name ?? '-') }}, 
                                                            className: {{ Js::from($reg->competitionClass->name ?? '-') }}, 
                                                            categoryName: {{ Js::from($reg->category_name ?? '') }}, 
                                                            regNo: '{{ $reg->regis_no }}',
                                                            fee: {{ $reg->competitionClass->entry_fee ?? 0 }}
                                                        }, @endforeach
                                                ]
                                            })"
                                            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500/10 text-blue-400 hover:bg-blue-500/20 text-sm font-normal rounded-lg transition-colors focus:outline-none">
                                            <i class="fas fa-search text-xs"></i> ตรวจสอบ
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <i class="fas fa-inbox text-4xl text-white/10 mb-3"></i>
                                            <p class="text-gray-500 font-normal">ไม่พบข้อมูลบิลชำระเงิน</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination --}}
            @if ($transactions->hasPages())
                <div class="mt-6 flex justify-center">
                    {{ $transactions->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

        {{-- MODAL ตรวจสอบสลิป (Adjusted Font Size for Desktop) --}}
        <div x-show="modalOpen" class="fixed inset-0 z-50 overflow-hidden flex items-center justify-center p-4"
            aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">

            {{-- Backdrop --}}
            <div x-show="modalOpen" x-transition.opacity @click="closeModal()"
                class="absolute inset-0 bg-black/80 backdrop-blur-sm transition-opacity"></div>

            {{-- Modal Container --}}
            <div x-show="modalOpen" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 scale-95"
                class="relative w-full max-w-4xl h-[80vh] bg-[#121212] rounded-2xl shadow-2xl overflow-hidden border border-white/10 flex flex-col md:flex-row z-10 font-kanit">

                {{-- 🔴 ด้านซ้าย: แสดงรูปสลิป --}}
                <div
                    class="w-full md:w-5/12 h-[30vh] md:h-full bg-black/40 border-b md:border-b-0 md:border-r border-white/5 relative flex flex-col">
                    <div class="flex-1 relative overflow-hidden flex items-center justify-center p-4">
                        <template x-if="modalData.slipUrl">
                            <div class="w-full h-full relative flex items-center justify-center">
                                <div x-show="!modalData.imgLoaded"
                                    class="absolute inset-0 flex flex-col items-center justify-center z-10 font-normal">
                                    <i class="fas fa-spinner fa-spin text-2xl text-blue-500 mb-2"></i>
                                </div>
                                <img :src="modalData.slipUrl" x-on:load="modalData.imgLoaded = true"
                                    x-on:error="modalData.imgError = true; modalData.imgLoaded = true"
                                    class="max-w-full max-h-full object-contain rounded-lg shadow-lg"
                                    x-show="modalData.imgLoaded && !modalData.imgError">
                            </div>
                        </template>
                    </div>
                    <template x-if="modalData.slipUrl">
                        <div class="p-3 text-center bg-black/20">
                            <a :href="modalData.slipUrl" target="_blank"
                                class="text-[11px] lg:text-sm text-blue-400 hover:text-blue-300 transition-colors font-normal">
                                <i class="fas fa-external-link-alt mr-1"></i> เปิดดูรูปขนาดเต็ม
                            </a>
                        </div>
                    </template>
                </div>

                {{-- 🔵 ด้านขวา: ข้อมูล (Font Adjusted) --}}
                <div class="w-full md:w-7/12 flex flex-col bg-[#121212] min-h-0">

                    {{-- Header --}}
                    <div
                        class="px-6 py-5 border-b border-white/5 bg-[#171717] flex justify-between items-center shrink-0">
                        <div class="min-w-0">
                            <div class="flex items-center gap-3 mb-1.5">
                                <span
                                    class="px-2 py-0.5 rounded text-[10px] lg:text-xs font-mono bg-blue-500/20 text-blue-400 border border-blue-500/20 font-normal"
                                    x-text="modalData.txNo"></span>
                                <span class="text-[11px] lg:text-sm text-gray-400 font-normal"
                                    x-text="'โอนโดย: ' + modalData.userName"></span>
                            </div>
                            <h2 class="text-base lg:text-lg font-normal text-white truncate"
                                x-text="modalData.compName"></h2>
                        </div>
                        <button @click="closeModal()"
                            class="text-gray-500 hover:text-white w-10 h-10 rounded-full bg-white/5 flex items-center justify-center transition-colors">
                            <i class="fas fa-times text-base"></i>
                        </button>
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 overflow-y-auto custom-scrollbar p-6 lg:p-8 space-y-6 lg:space-y-8">

                        {{-- ยอดเงิน (เด่นขึ้นมาก) --}}
                        <div
                            class="flex items-center justify-between p-4 lg:p-5 rounded-2xl bg-[#1a1a1a] border border-white/5 shadow-inner">
                            <span class="text-xs lg:text-lg text-gray-400">ยอดเงินที่ต้องตรวจสอบ</span>
                            <span class="text-2xl lg:text-xl font-normal text-emerald-400 tracking-tight"
                                x-text="new Intl.NumberFormat('th-TH').format(modalData.totalAmount) + ' ฿'"></span>
                        </div>

                        {{-- ข้อมูลภาษี (ตัวใหญ่ขึ้นอ่านง่าย) --}}
                        <template x-if="modalData.isTaxRequested">
                            <div class="p-5 lg:p-6 rounded-2xl bg-blue-900/10 border border-blue-500/20">
                                <p
                                    class="text-[10px] lg:text-xs font-normal text-blue-400 uppercase tracking-widest mb-4">
                                    <i class="fas fa-file-invoice mr-1.5"></i> ข้อมูลใบกำกับภาษี</p>
                                <div class="space-y-4 lg:space-y-5 text-xs lg:text-base">
                                    <div class="flex flex-col gap-1">
                                        <span
                                            class="text-gray-500 text-[10px] lg:text-md uppercase">ชื่อผู้เสียภาษี</span>
                                        <span class="text-white font-normal lg:text-lg"
                                            x-text="modalData.taxName"></span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="flex flex-col gap-1">
                                            <span
                                                class="text-gray-500 text-[10px] lg:text- uppercase">เลขประจำตัว</span>
                                            <span class="text-blue-300 font-mono lg:text-lg font-normal"
                                                x-text="modalData.taxId"></span>
                                        </div>
                                        <div class="flex flex-col gap-1">
                                            <span class="text-gray-500 text-[10px] lg:text-xs uppercase">สาขา</span>
                                            <span class="text-white font-normal lg:text-lg"
                                                x-text="modalData.taxBranch"></span>
                                        </div>
                                    </div>
                                    <div class="flex flex-col gap-1">
                                        <span
                                            class="text-gray-500 text-[10px] lg:text-xs uppercase">ที่อยู่จดทะเบียน</span>
                                        <span class="text-white leading-relaxed font-normal" x-text="modalData.taxAddress"></span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="flex flex-col gap-1">
                                            <span
                                                class="text-gray-500 text-[10px] lg:text-xs uppercase">เบอร์โทรติดต่อ</span>
                                            <span class="text-white lg:text-lg font-normal" x-text="modalData.taxPhone"></span>
                                        </div>
                                        <div class="flex flex-col gap-1">
                                            <span
                                                class="text-gray-500 text-[10px] lg:text-xs uppercase">อีเมลรับเอกสาร</span>
                                            <span class="text-white truncate lg:text-lg font-normal"
                                                x-text="modalData.taxEmail"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        {{-- รายการทีม --}}
                        <div>
                            <p
                                class="text-[10px] lg:text-xs font-normal text-gray-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                                <i class="fas fa-users"></i> ทีมที่สมัคร (<span x-text="modalData.teamCount"></span>)
                            </p>
                            <div class="space-y-3">
                                <template x-for="team in modalData.teamList">
                                    <div
                                        class="p-4 bg-[#1a1a1a] rounded-xl border border-white/5 flex justify-between items-center group transition-colors hover:border-white/10">
                                        <div class="min-w-0 flex-1">
                                            <p class="font-normal text-white lg:text-lg"
                                                x-text="team.teamName"></p>
                                            <p class="text-[10px] lg:text-sm text-gray-500 mt-0.5 font-normal">
                                                <template x-if="team.categoryName">
                                                    <span x-text="team.categoryName + ' • '" class="text-blue-400 font-normal"></span>
                                                </template>
                                                <span x-text="team.className"></span>
                                            </p>
                                        </div>
                                        <div class="text-right ml-4 shrink-0">
                                            <span
                                                class="text-xs lg:text-base font-normal text-gray-300 whitespace-nowrap"
                                                x-text="team.fee == 0 ? 'ฟรี' : new Intl.NumberFormat('th-TH').format(team.fee) + ' ฿'"></span>
                                            <p class="text-[9px] lg:text-[11px] text-gray-600 font-mono mt-1 font-normal"
                                                x-text="team.regNo"></p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Footer (Buttons Adjusted) --}}
                    <div class="px-6 py-5 lg:px-8 lg:py-6 border-t border-white/5 bg-[#171717] shrink-0 shadow-2xl">
                        <form method="POST" :action="modalData.actionUrl" id="paymentForm">
                            @csrf @method('PUT')
                            <input type="hidden" name="action" x-model="modalData.currentAction">

                            <template x-if="modalData.status === 'waiting_verify'">
                                <div class="space-y-4">
                                    <input type="text" name="reason" x-model="modalData.rejectReasonInput"
                                        placeholder="ระบุเหตุผลกรณีต้องปฏิเสธบิลนี้..."
                                        class="w-full text-sm lg:text-base rounded-xl border border-white/10 bg-[#0a0a0a] text-white focus:border-red-500 outline-none px-4 py-3.5 transition-all font-normal">
                                    <div class="flex gap-3 font-normal">
                                        <button type="button" @click.prevent="confirmAction('reject')"
                                            class="flex-1 py-3 lg:py-4 bg-red-600/90 hover:bg-red-500 text-white rounded-xl text-sm lg:text-base font-normal transition-all shadow-lg active:scale-95">
                                            <i class="fas fa-times mr-2"></i> ปฏิเสธ
                                        </button>
                                        <button type="button" @click.prevent="confirmAction('approve')"
                                            class="flex-[1.8] py-3 lg:py-4 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-sm lg:text-base font-normal transition-all shadow-lg active:scale-95">
                                            <i class="fas fa-check mr-2"></i> อนุมัติการชำระเงิน
                                        </button>
                                    </div>
                                </div>
                            </template>

                            <template x-if="modalData.status === 'approved'">
                                <div
                                    class="p-4 lg:p-5 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-center text-sm lg:text-lg text-emerald-500 font-normal">
                                    <i class="fas fa-check-circle mr-2 text-xl"></i>
                                    บิลนี้ได้รับการอนุมัติเรียบร้อยแล้ว
                                </div>
                            </template>

                            <template x-if="modalData.status === 'rejected'">
                                <div class="p-4 lg:p-5 rounded-xl bg-red-500/10 border border-red-500/20">
                                    <div class="text-red-500 font-normal lg:text-lg flex items-center gap-2 mb-1">
                                        <i class="fas fa-times-circle text-xl"></i> ปฏิเสธแล้ว
                                    </div>
                                    <div class="text-xs lg:text-base text-gray-400 font-normal"
                                        x-text="'สาเหตุ: ' + (modalData.rejectReason || 'ไม่ได้ระบุเหตุผล')"></div>
                                </div>
                            </template>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Alpine JS Logic & SweetAlert --}}
    <script>
        function paymentsPage() {
            return {
                search: '{{ request('search') }}',
                statusFilter: '{{ request('status') ?: 'waiting_verify' }}',
                competitionFilter: '{{ request('competition_id', '') }}',
                compList: @json($alpineCompetitions),

                statusLabels: {
                    'waiting_verify': 'รอตรวจสอบ',
                    'approved': 'อนุมัติแล้ว',
                    'rejected': 'ปฏิเสธ',
                    'all': 'บิลทั้งหมด'
                },

                modalOpen: false,
                modalData: {
                    id: '',
                    txNo: '',
                    userName: '',
                    compName: '',
                    totalAmount: 0,
                    teamCount: 0,
                    teamList: [],
                    slipUrl: '',
                    status: '',
                    actionUrl: '',
                    rejectReason: '',
                    rejectReasonInput: '',
                    currentAction: '',
                    imgLoaded: false,
                    imgError: false,
                    isTaxRequested: false,
                    taxName: '',
                    taxId: '',
                    taxBranch: '',
                    taxAddress: '',
                    taxPhone: '',
                    taxEmail: ''
                },

                applyFilter(key, value) {
                    const url = new URL(window.location.href);
                    if (value) {
                        url.searchParams.set(key, value);
                    } else {
                        url.searchParams.delete(key);
                    }
                    url.searchParams.delete('page');
                    window.location.href = url.toString();
                },

                clearFilters() {
                    window.location.href = window.location.pathname;
                },

                getCompName(id) {
                    if (!id) return 'ทั้งหมด';
                    const found = this.compList.find(c => c.id === id);
                    return found ? found.name : 'ทั้งหมด';
                },

                openModal(data) {
                    this.modalData = {
                        ...data,
                        rejectReasonInput: '',
                        currentAction: '',
                        imgLoaded: false,
                        imgError: false
                    };
                    this.modalOpen = true;
                    document.body.style.overflow = 'hidden';
                },

                closeModal() {
                    this.modalOpen = false;
                    document.body.style.overflow = '';
                    setTimeout(() => {
                        this.modalData = {
                            id: '',
                            txNo: '',
                            userName: '',
                            compName: '',
                            totalAmount: 0,
                            teamCount: 0,
                            teamList: [],
                            slipUrl: '',
                            status: '',
                            actionUrl: '',
                            rejectReason: '',
                            rejectReasonInput: '',
                            currentAction: '',
                            imgLoaded: false,
                            imgError: false,
                            isTaxRequested: false,
                            taxName: '',
                            taxId: '',
                            taxBranch: '',
                            taxAddress: '',
                            taxPhone: '',
                            taxEmail: ''
                        };
                    }, 300);
                },

                confirmAction(type) {
                    let titleText = '';
                    let htmlText = '';
                    let confirmColor = '';
                    let confirmButtonText = '';
                    let iconType = '';

                    this.modalData.currentAction = type;

                    if (type === 'approve') {
                        titleText = 'ยืนยันการอนุมัติ?';
                        htmlText =
                            `คุณต้องการ <strong>อนุมัติ</strong> สลิปของ <strong class="text-blue-400">${this.modalData.userName}</strong> (รหัส ${this.modalData.txNo})<br><br><span class="text-gray-400 text-sm">ระบบจะเปลี่ยนสถานะทุกทีมในบิลนี้เป็น 'ชำระเงินแล้ว'</span>`;
                        confirmColor = '#10b981';
                        confirmButtonText = '<i class="fas fa-check mr-1"></i> ยืนยันการอนุมัติ';
                        iconType = 'success';
                    } else if (type === 'reject') {
                        if (this.modalData.rejectReasonInput.trim() === '') {
                            Swal.fire({
                                icon: 'warning',
                                title: 'กรุณาระบุเหตุผล',
                                text: 'คุณต้องระบุหมายเหตุก่อนทำการปฏิเสธบิลนี้',
                                confirmButtonColor: '#3b82f6',
                                background: '#1e1e1e',
                                color: '#ffffff',
                                customClass: {
                                    popup: 'rounded-[2rem] font-kanit border border-white/10'
                                }
                            });
                            return;
                        }
                        titleText = 'ยืนยันการปฏิเสธ?';
                        htmlText =
                            `คุณกำลัง <strong>ปฏิเสธ</strong> บิลนี้<br><br><span class="text-sm text-red-400">เหตุผล: ${this.modalData.rejectReasonInput}</span><br><br><span class="text-gray-400 text-sm">ระบบจะแจ้งให้ผู้ใช้ทราบเพื่อทำการแก้ไข</span>`;
                        confirmColor = '#ef4444';
                        confirmButtonText = '<i class="fas fa-times mr-1"></i> ยืนยันการปฏิเสธ';
                        iconType = 'warning';
                    }

                    Swal.fire({
                        title: titleText,
                        html: htmlText,
                        icon: iconType,
                        showCancelButton: true,
                        confirmButtonColor: confirmColor,
                        cancelButtonColor: '#374151',
                        confirmButtonText: confirmButtonText,
                        cancelButtonText: 'ยกเลิก',
                        reverseButtons: true,
                        background: '#1e1e1e',
                        color: '#ffffff',
                        customClass: {
                            popup: 'rounded-[2rem] border border-white/10 shadow-2xl font-kanit',
                            confirmButton: 'rounded-xl px-6 py-2.5 font-normal tracking-wide transition-all hover:scale-105',
                            cancelButton: 'rounded-xl px-6 py-2.5 font-normal tracking-wide transition-all'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('paymentForm').submit();
                        }
                    });
                }
            };
        }
    </script>

    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 4px;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</x-admin-layout>
