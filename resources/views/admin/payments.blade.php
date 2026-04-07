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

    <div class="min-h-screen bg-gray-50/50 dark:bg-[#0a0a0a] font-kanit p-4 sm:p-6" x-data="paymentsPage()">
        <div class="max-w-7xl mx-auto">

            {{-- Page Header --}}
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">ตรวจสอบการชำระเงิน</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-2">จัดการและอนุมัติหลักฐานการโอนเงิน
                </p>
            </div>

            {{-- Stats Cards (ปรับเป็น 4 คอลัมน์ เพราะไม่มีรอชำระเงินแล้ว) --}}
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
                        class="bg-white dark:bg-[#141414] rounded-2xl p-4 border border-gray-200 dark:border-gray-800 shadow-sm cursor-pointer hover:border-{{ $card['class'] }}-500 transition-all">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">{{ $card['label'] }}</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $card['count'] }}
                                </p>
                            </div>
                            <div
                                class="w-10 h-10 rounded-lg bg-{{ $card['class'] }}-50 dark:bg-{{ $card['class'] }}-500/10 flex items-center justify-center">
                                <i
                                    class="fas {{ $card['icon'] }} text-{{ $card['class'] }}-600 dark:text-{{ $card['class'] }}-400 text-sm"></i>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Filters Section --}}
            <div
                class="bg-white dark:bg-[#141414] rounded-2xl border border-gray-200 dark:border-gray-800 p-4 mb-6 shadow-sm">
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        {{-- 🔍 Search Box --}}
                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ค้นหา</label>
                            <div class="relative">
                                <input type="text" x-model="search" @keyup.enter="applyFilter('search', search)"
                                    placeholder="รหัสบิล, ชื่อผู้โอน, ชื่อทีม..."
                                    class="w-full px-4 py-2.5 text-sm border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50 dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors">
                                <button @click="applyFilter('search', search)"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 p-1.5 text-gray-400 hover:text-blue-500 transition-colors">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Status Filter Dropdown --}}
                        <div x-data="{ open: false }" class="relative">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">สถานะ</label>
                            <button @click="open = !open" @click.outside="open = false" type="button"
                                class="w-full flex items-center justify-between px-4 py-2.5 text-sm border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50 dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors">
                                <span x-text="statusLabels[statusFilter] || 'รอตรวจสอบ'"></span>
                                <i class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-200"
                                    :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="open" x-transition.opacity.duration.200ms x-cloak
                                class="absolute z-20 w-full mt-2 bg-white dark:bg-[#1a1a1a] border border-gray-100 dark:border-gray-800 rounded-xl shadow-lg overflow-hidden py-1">
                                <template x-for="(label, val) in statusLabels" :key="val">
                                    <button @click="applyFilter('status', val); open = false" type="button"
                                        class="w-full text-left px-4 py-2.5 text-sm transition-colors flex items-center justify-between"
                                        :class="statusFilter === val ?
                                            'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 font-semibold' :
                                            'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'">
                                        <span x-text="label"></span>
                                        <i x-show="statusFilter === val"
                                            class="fas fa-check text-blue-600 dark:text-blue-400"></i>
                                    </button>
                                </template>
                            </div>
                        </div>

                        {{-- Competition Filter --}}
                        <div x-data="{ open: false }" class="relative">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">งานแข่งขัน</label>
                            <div class="flex items-center gap-3">
                                <div class="relative w-full">
                                    <button @click="open = !open" @click.outside="open = false" type="button"
                                        class="w-full flex items-center justify-between px-4 py-2.5 text-sm border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50 dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors">
                                        <span class="truncate pr-4" x-text="getCompName(competitionFilter)"></span>
                                        <i class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-200"
                                            :class="open ? 'rotate-180' : ''"></i>
                                    </button>
                                    <div x-show="open" x-transition.opacity.duration.200ms x-cloak
                                        class="absolute z-20 w-full mt-2 bg-white dark:bg-[#1a1a1a] border border-gray-100 dark:border-gray-800 rounded-xl shadow-lg overflow-y-auto max-h-60 custom-scrollbar py-1">
                                        <button @click="applyFilter('competition_id', ''); open = false" type="button"
                                            class="w-full text-left px-4 py-2.5 text-sm transition-colors flex items-center justify-between"
                                            :class="competitionFilter === '' ?
                                                'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 font-semibold' :
                                                'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'">
                                            <span class="truncate pr-2">ทั้งหมด</span>
                                            <i x-show="competitionFilter === ''"
                                                class="fas fa-check text-blue-600 dark:text-blue-400 shrink-0"></i>
                                        </button>
                                        <template x-for="comp in compList" :key="comp.id">
                                            <button @click="applyFilter('competition_id', comp.id); open = false"
                                                type="button"
                                                class="w-full text-left px-4 py-2.5 text-sm transition-colors flex items-center justify-between"
                                                :class="competitionFilter === comp.id ?
                                                    'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 font-semibold' :
                                                    'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'">
                                                <span class="truncate pr-2" x-text="comp.name"></span>
                                                <i x-show="competitionFilter === comp.id"
                                                    class="fas fa-check text-blue-600 dark:text-blue-400 shrink-0"></i>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                                <button @click="clearFilters()" type="button"
                                    class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-semibold rounded-xl transition-all whitespace-nowrap">ล้างค่า</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Status Tabs (ตัด รอชำระเงิน ออก) --}}
            <div
                class="flex flex-wrap items-center bg-gray-50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-800 rounded-xl p-1 w-full lg:w-auto shrink-0 z-10 mb-6">
                @foreach ([
        'waiting_verify' => ['label' => 'รอตรวจสอบ', 'icon' => 'fa-clock'],
        'approved' => ['label' => 'อนุมัติแล้ว', 'icon' => 'fa-check-circle'],
        'rejected' => ['label' => 'ไม่ผ่าน', 'icon' => 'fa-times-circle'],
        'all' => ['label' => 'ทั้งหมด', 'icon' => 'fa-list'],
    ] as $val => $data)
                    <button @click="applyFilter('status', '{{ $val }}')"
                        :class="statusFilter === '{{ $val }}' ?
                            'bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm font-medium border-gray-200 dark:border-gray-700' :
                            'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 font-normal border-transparent'"
                        class="flex items-center gap-1.5 px-4 py-2 text-sm rounded-lg border transition-all flex-1 sm:flex-none justify-center">
                        <i class="fas {{ $data['icon'] }} text-xs opacity-70"></i>
                        {{ $data['label'] }}
                    </button>
                @endforeach
            </div>

            {{-- Data Table --}}
            <div
                class="bg-white dark:bg-[#141414] rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
                <div class="overflow-x-auto no-scrollbar">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-800">
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                    รหัสบิล / ผู้โอน
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                    งานแข่งขัน
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                    ยอดชำระสุทธิ
                                </th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                    สถานะ
                                </th>
                                <th
                                    class="px-6 py-4 text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                    การจัดการ
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800/60 relative">
                            @forelse ($transactions as $tx)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.02] transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-mono font-bold text-blue-600 dark:text-blue-400">
                                            {{ $tx->tx_no }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            <i class="far fa-user mr-1"></i>{{ $tx->user->name ?? '-' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                        {{ $tx->competition->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                            {{ number_format($tx->total_amount) }} ฿
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            จ่ายสำหรับ {{ $tx->registrations->count() }} ทีม
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @php
                                            $statusMap = [
                                                'waiting_verify' => [
                                                    'badge' =>
                                                        'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400 border-amber-200 dark:border-amber-900/50',
                                                    'text' => 'รอตรวจสอบ',
                                                ],
                                                'approved' => [
                                                    'badge' =>
                                                        'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 border-emerald-200 dark:border-emerald-900/50',
                                                    'text' => 'อนุมัติแล้ว',
                                                ],
                                                'rejected' => [
                                                    'badge' =>
                                                        'bg-red-50 text-red-700 dark:bg-red-500/10 dark:text-red-400 border-red-200 dark:border-red-900/50',
                                                    'text' => 'ปฏิเสธ',
                                                ],
                                            ];
                                            $status = $statusMap[$tx->status] ?? [
                                                'badge' =>
                                                    'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300 border-gray-200',
                                                'text' => $tx->status,
                                            ];
                                        @endphp
                                        <span
                                            class="inline-flex px-3 py-1.5 text-xs font-semibold rounded-lg border {{ $status['badge'] }}">{{ $status['text'] }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
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
                                                teamList: [
                                                    {{-- 🚀 ดึงค่า fee มาใส่ตรงนี้ --}}
                                                    @foreach ($tx->registrations as $reg)
                                                        { 
                                                            teamName: {{ Js::from($reg->team->name ?? '-') }}, 
                                                            className: {{ Js::from($reg->competitionClass->name ?? '-') }}, 
                                                            regNo: '{{ $reg->regis_no }}',
                                                            fee: {{ $reg->competitionClass->entry_fee ?? 0 }}
                                                        }, @endforeach
                                                ]
                                            })"
                                            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-500/20 text-sm font-semibold rounded-lg transition-colors focus:outline-none">
                                            <i class="fas fa-search text-xs"></i> ตรวจสอบ
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <i class="fas fa-inbox text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                                            <p class="text-gray-500 dark:text-gray-400 font-medium">
                                                ไม่พบข้อมูลบิลชำระเงิน</p>
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

        {{-- MODAL ตรวจสอบสลิป (ดีไซน์ใหม่ รวบบิล) --}}
        <div x-show="modalOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
            role="dialog" aria-modal="true" style="display: none;">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="modalOpen" x-transition.opacity @click="closeModal()"
                    class="fixed inset-0 bg-black/70 backdrop-blur-sm transition-opacity"></div>

                <div x-show="modalOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-[#141414] text-left shadow-2xl transition-all sm:my-8 w-full max-w-4xl border border-gray-200 dark:border-gray-800 flex flex-col md:flex-row">

                    {{-- ด้านซ้าย: รูปสลิป --}}
                    <div class="w-full md:w-1/2 bg-gray-100 dark:bg-black/50 p-4 flex flex-col h-[40vh] md:h-[70vh]">
                        <div class="flex justify-between items-center mb-2 md:hidden">
                            <h3 class="text-sm font-bold dark:text-white" x-text="'รหัสบิล ' + modalData.txNo"></h3>
                            <button @click="closeModal()" class="text-gray-400 hover:text-gray-600"><i
                                    class="fas fa-times"></i></button>
                        </div>
                        <div
                            class="flex-1 relative rounded-xl overflow-hidden flex items-center justify-center bg-gray-200 dark:bg-gray-900/50">
                            <template x-if="modalData.slipUrl">
                                <div class="w-full h-full relative flex items-center justify-center">
                                    <div x-show="!modalData.imgLoaded"
                                        class="absolute inset-0 flex flex-col items-center justify-center bg-gray-100 dark:bg-gray-800 z-10">
                                        <i class="fas fa-spinner fa-spin text-3xl text-blue-500 mb-2"></i>
                                        <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold">
                                            กำลังโหลดรูปภาพ...</p>
                                    </div>
                                    <div x-show="modalData.imgError"
                                        class="absolute inset-0 flex flex-col items-center justify-center bg-gray-100 dark:bg-gray-800 z-10 text-red-500">
                                        <i class="fas fa-exclamation-triangle text-3xl mb-2"></i>
                                        <p class="text-xs font-bold uppercase">ไม่สามารถโหลดรูปภาพได้</p>
                                    </div>
                                    <img :src="modalData.slipUrl" x-on:load="modalData.imgLoaded = true"
                                        x-on:error="modalData.imgError = true; modalData.imgLoaded = true"
                                        class="w-full h-full object-contain"
                                        x-show="modalData.imgLoaded && !modalData.imgError">
                                </div>
                            </template>
                        </div>
                        <template x-if="modalData.slipUrl">
                            <a :href="modalData.slipUrl" target="_blank"
                                class="mt-3 text-center text-xs text-blue-500 hover:text-blue-600 font-medium">
                                <i class="fas fa-external-link-alt mr-1"></i> ดูรูปขนาดเต็ม
                            </a>
                        </template>
                    </div>

                    {{-- ด้านขวา: ข้อมูลและแบบฟอร์ม --}}
                    <div class="w-full md:w-1/2 p-6 flex flex-col h-auto md:h-[70vh]">
                        <div class="hidden md:flex justify-end mb-2">
                            <button @click="closeModal()"
                                class="text-gray-400 hover:text-red-500 w-8 h-8 rounded-full bg-gray-50 dark:bg-gray-800 flex items-center justify-center transition-colors">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        {{-- Header ข้อมูลบิล --}}
                        <div class="mb-4 shrink-0 border-b border-gray-100 dark:border-gray-800 pb-4">
                            <p class="text-xs font-bold text-blue-600 tracking-wider mb-1"
                                x-text="'รหัสบิล: ' + modalData.txNo"></p>
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white leading-tight"
                                x-text="modalData.compName"></h2>
                            <p class="text-sm text-gray-500 mt-1"><i class="far fa-user mr-1"></i> ผู้โอน: <span
                                    x-text="modalData.userName"></span></p>
                            <p class="text-lg font-semibold text-emerald-600 dark:text-emerald-400 mt-2"
                                x-text="'ยอดโอนสุทธิ: ' + new Intl.NumberFormat('th-TH').format(modalData.totalAmount) + ' ฿'">
                            </p>
                        </div>

                        {{-- รายชื่อทีมในบิลนี้ --}}
                        <div class="flex-1 overflow-y-auto custom-scrollbar pr-2 mb-4 space-y-2.5">
                            <p class="text-xs font-bold text-gray-500 uppercase flex items-center gap-2">
                                รายการแข่งขันและทีม
                                <span
                                    class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 px-2 py-0.5 rounded-full text-[10px]"
                                    x-text="modalData.teamCount + ' ทีม'"></span>
                            </p>

                            <template x-for="team in modalData.teamList">
                                <div
                                    class="p-3 bg-white dark:bg-[#1a1a1a] rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm relative overflow-hidden group">
                                    {{-- Accent Bar --}}
                                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-blue-500"></div>

                                    <div class="pl-2">
                                        <div class="flex justify-between items-start mb-1">
                                            <p class="font-bold text-sm text-gray-900 dark:text-white pr-2"
                                                x-text="team.teamName"></p>
                                            <span
                                                class="shrink-0 text-[10px] font-mono font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-500/10 px-1.5 py-0.5 rounded border border-blue-100 dark:border-blue-900/50"
                                                x-text="team.regNo"></span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <p
                                                class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                                                <i class="fas fa-robot opacity-70"></i> <span
                                                    x-text="team.className"></span>
                                            </p>
                                            {{-- 🚀 แสดงยอดค่าสมัครตรงนี้ --}}
                                            <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">
                                                <span
                                                    x-text="team.fee == 0 ? 'ฟรี' : new Intl.NumberFormat('th-TH').format(team.fee) + ' ฿'"></span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- ส่วนจัดการสถานะ (ปุ่มอนุมัติ/ปฏิเสธ) --}}
                        <div class="shrink-0 pt-4 border-t border-gray-100 dark:border-gray-800 mt-auto">
                            <form method="POST" :action="modalData.actionUrl">
                                @csrf @method('PUT')

                                <template x-if="modalData.status === 'waiting_verify'">
                                    <div class="space-y-3">
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">หมายเหตุ
                                                (เฉพาะกรณีปฏิเสธ)</label>
                                            <textarea name="reason" rows="2" placeholder="เช่น สลิปไม่ชัดเจน, ยอดเงินไม่ถูกต้อง..."
                                                class="w-full text-sm rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 dark:text-white focus:ring-blue-500"></textarea>
                                        </div>
                                        <div class="flex gap-2">
                                            <button type="submit" name="action" value="approve"
                                                class="flex-1 bg-emerald-500 hover:bg-emerald-600 text-white py-3 rounded-xl font-semibold text-sm transition-colors shadow-lg shadow-emerald-500/20"><i
                                                    class="fas fa-check mr-1"></i> อนุมัติ</button>
                                            <button type="submit" name="action" value="reject"
                                                class="flex-1 bg-red-500 hover:bg-red-600 text-white py-3 rounded-xl font-semibold text-sm transition-colors shadow-lg shadow-red-500/20"><i
                                                    class="fas fa-times mr-1"></i> ปฏิเสธ</button>
                                        </div>
                                        <p class="text-[10px] text-gray-400 text-center mt-2">*การอนุมัติ/ปฏิเสธ
                                            จะมีผลกับทุกทีมในบิลนี้พร้อมกัน</p>
                                    </div>
                                </template>

                                <template x-if="modalData.status === 'approved'">
                                    <div
                                        class="text-center p-4 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-900/30">
                                        <div class="font-bold text-lg text-emerald-600"><i
                                                class="fas fa-check-circle"></i> <span>อนุมัติเรียบร้อย</span></div>
                                    </div>
                                </template>

                                <template x-if="modalData.status === 'rejected'">
                                    <div
                                        class="text-center p-4 rounded-xl bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-900/30">
                                        <div class="font-bold text-lg text-red-600 mb-2"><i
                                                class="fas fa-times-circle"></i> <span>ปฏิเสธแล้ว</span></div>
                                        <div
                                            class="text-sm text-red-700 dark:text-red-400 bg-white/50 dark:bg-black/20 p-2 rounded-lg text-left">
                                            <span
                                                class="font-semibold text-xs text-red-500 block mb-1 uppercase tracking-wider">เหตุผล:</span>
                                            <span x-text="modalData.rejectReason || 'ไม่มีการระบุเหตุผล'"></span>
                                        </div>
                                    </div>
                                </template>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Alpine JS Logic --}}
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
                    teamList: [], // เก็บรายชื่อทีม
                    slipUrl: '',
                    status: '',
                    actionUrl: '',
                    rejectReason: '',
                    imgLoaded: false,
                    imgError: false
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
                            imgLoaded: false,
                            imgError: false
                        };
                    }, 300);
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
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #334155;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</x-admin-layout>
