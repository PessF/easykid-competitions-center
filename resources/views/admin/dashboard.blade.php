<x-admin-layout>
    <x-slot name="title">Admin Dashboard | Easykids Competitions</x-slot>

    <div class="max-w-7xl mx-auto space-y-6 pb-10">
        
        {{-- 1. Header & Greeting --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-normal tracking-tight text-white flex items-center gap-3">
                    สวัสดี, <span class="text-blue-400 font-medium">{{ Auth::user()->name ?? 'แอดมิน' }}</span>
                </h1>
                <p class="text-sm text-gray-400 mt-1 font-normal">
                    สรุปสถานะการแข่งขันและกิจกรรมล่าสุดประจำวันนี้
                </p>
            </div>
            <div class="inline-flex items-center text-xs font-normal text-gray-400 bg-[#121212] px-4 py-2 rounded-lg border border-white/5 shadow-sm">
                <i class="far fa-calendar-alt mr-2 text-blue-500"></i>
                {{ \Carbon\Carbon::now()->translatedFormat('j F Y') }}
            </div>
        </div>

        {{-- 2. Key Metrics (Stats Cards) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            {{-- Card 1: รอตรวจสอบ --}}
            <a href="{{ route('admin.payments.index', ['status' => 'waiting_verify']) }}" 
                class="p-5 bg-[#121212] border-l-4 border-amber-500 border-t border-r border-b border-white/5 rounded-xl shadow-sm hover:bg-white/[0.02] transition-all group flex flex-col justify-between">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <div class="text-gray-400 text-[11px] font-normal uppercase tracking-wider">บิลรอตรวจสอบ</div>
                        <div class="text-3xl font-medium mt-1 text-white">
                            {{ $pendingPayments ?? 0 }}
                        </div>
                    </div>
                    <div class="text-amber-400 opacity-80 bg-amber-500/10 p-2 rounded-lg">
                        <i class="fas fa-clock text-lg"></i>
                    </div>
                </div>
                <div class="mt-2 text-[10px] text-blue-400 font-normal flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                    คลิกเพื่อตรวจสอบ <i class="fas fa-chevron-right text-[8px]"></i>
                </div>
            </a>

            {{-- Card 2: รายได้สุทธิ --}}
            <div class="p-5 bg-[#121212] border-l-4 border-emerald-500 border-t border-r border-b border-white/5 rounded-xl shadow-sm">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="text-gray-400 text-[11px] font-normal uppercase tracking-wider">ยอดเงินที่อนุมัติแล้ว</div>
                        <div class="text-2xl font-medium mt-1 text-white">
                            {{ number_format($totalRevenue ?? 0) }} <span class="text-sm text-gray-500 font-normal ml-0.5">฿</span>
                        </div>
                    </div>
                    <div class="text-emerald-400 opacity-80 bg-emerald-500/10 p-2 rounded-lg">
                        <i class="fas fa-wallet text-lg"></i>
                    </div>
                </div>
            </div>

            {{-- Card 3: ทีมทั้งหมด --}}
            <div class="p-5 bg-[#121212] border-l-4 border-blue-500 border-t border-r border-b border-white/5 rounded-xl shadow-sm">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="text-gray-400 text-[11px] font-normal uppercase tracking-wider">ใบสมัครทั้งหมด</div>
                        <div class="text-3xl font-medium mt-1 text-white">
                            {{ $totalTeams ?? 0 }}
                        </div>
                    </div>
                    <div class="text-blue-400 opacity-80 bg-blue-500/10 p-2 rounded-lg">
                        <i class="fas fa-file-alt text-lg"></i>
                    </div>
                </div>
            </div>

            {{-- Card 4: งานแข่งขันที่เปิดอยู่ --}}
            <div class="p-5 bg-[#121212] border-l-4 border-purple-500 border-t border-r border-b border-white/5 rounded-xl shadow-sm">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="text-gray-400 text-[11px] font-normal uppercase tracking-wider">งานที่กำลังเปิดรับ</div>
                        <div class="text-3xl font-medium mt-1 text-white">
                            {{ $activeCompetitions ?? 0 }}
                        </div>
                    </div>
                    <div class="text-purple-400 opacity-80 bg-purple-500/10 p-2 rounded-lg">
                        <i class="fas fa-trophy text-lg"></i>
                    </div>
                </div>
            </div>

        </div>

        {{-- 🚀 3. Chart Section (กราฟเส้นการเติบโต) --}}
        <div class="bg-[#121212] border border-white/5 rounded-xl shadow-sm p-5 w-full">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h3 class="text-sm font-medium text-gray-200">สถิติการสมัครเข้าร่วมการแข่งขัน</h3>
                    <p class="text-[11px] text-gray-500 font-normal mt-0.5">ภาพรวมจำนวนทีมที่สมัครเข้ามาใน 7 วันล่าสุด</p>
                </div>
                <div class="text-[10px] text-gray-400 font-normal bg-white/5 px-2.5 py-1 rounded-md">
                    7 วันล่าสุด
                </div>
            </div>
            
            {{-- พื้นที่วาดกราฟ --}}
            <div class="relative h-64 w-full">
                <canvas id="growthChart"></canvas>
            </div>
        </div>

        {{-- 4. Two Columns: รายการล่าสุด --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            {{-- Column: บิลล่าสุดที่รอตรวจสอบ --}}
            <div class="bg-[#121212] border border-white/5 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-white/5 flex justify-between items-center bg-[#0a0a0a]">
                    <h3 class="text-sm font-medium text-gray-200 flex items-center gap-2">
                        <i class="fas fa-receipt text-amber-500"></i> รายการชำระเงินล่าสุด
                    </h3>
                    <a href="{{ route('admin.payments.index') }}" class="text-[11px] font-normal text-blue-400 hover:underline">ดูทั้งหมด</a>
                </div>
                <div class="p-0">
                    @if(isset($recentPayments) && $recentPayments->count() > 0)
                        <div class="divide-y divide-white/5">
                            @foreach($recentPayments as $payment)
                                <div class="px-5 py-4 hover:bg-white/[0.02] transition-colors flex items-center justify-between">
                                    <div class="space-y-1">
                                        <p class="text-sm font-normal text-white">{{ $payment->tx_no }}</p>
                                        <p class="text-xs text-gray-500 font-normal">{{ $payment->user->name ?? 'ไม่ระบุชื่อ' }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-white">{{ number_format($payment->total_amount) }} ฿</p>
                                        <p class="text-[10px] text-gray-500 font-normal mt-1">{{ $payment->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-12 flex flex-col items-center justify-center text-gray-500">
                            <i class="fas fa-check-circle text-2xl mb-2 opacity-20"></i>
                            <p class="text-xs font-normal tracking-wide">ตรวจสอบครบถ้วนแล้ว</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Column: ทีมที่สมัครล่าสุด --}}
            <div class="bg-[#121212] border border-white/5 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-white/5 flex justify-between items-center bg-[#0a0a0a]">
                    <h3 class="text-sm font-medium text-gray-200 flex items-center gap-2">
                        <i class="fas fa-users text-blue-500"></i> ทีมที่สมัครล่าสุด
                    </h3>
                    <a href="{{ route('admin.teams.index') }}" class="text-[11px] font-normal text-blue-400 hover:underline">ดูทั้งหมด</a>
                </div>
                <div class="p-0">
                    @if(isset($recentRegistrations) && $recentRegistrations->count() > 0)
                        <div class="divide-y divide-white/5">
                            @foreach($recentRegistrations as $reg)
                                <div class="px-5 py-4 hover:bg-white/[0.02] transition-colors flex items-center justify-between">
                                    <div class="space-y-1 pr-4">
                                        <p class="text-sm font-normal text-white truncate max-w-[180px]">{{ $reg->team->name ?? 'ไม่ระบุชื่อ' }}</p>
                                        <p class="text-[11px] text-gray-500 font-normal">{{ $reg->competitionClass->name ?? '-' }}</p>
                                    </div>
                                    <div class="text-right shrink-0">
                                        @php
                                            $statusStyles = [
                                                'approved' => 'text-emerald-400 bg-emerald-500/10 border-emerald-900/30',
                                                'waiting_verify' => 'text-amber-400 bg-amber-500/10 border-amber-900/30',
                                                'rejected' => 'text-red-400 bg-red-500/10 border-red-900/30',
                                                'pending_payment' => 'text-gray-400 bg-white/5 border-gray-800'
                                            ];
                                            $statusLabels = [
                                                'approved' => 'ผ่านแล้ว',
                                                'waiting_verify' => 'รอตรวจ',
                                                'rejected' => 'ไม่ผ่าน',
                                                'pending_payment' => 'รอจ่าย'
                                            ];
                                        @endphp
                                        <span class="text-[10px] font-normal px-2 py-0.5 rounded border {{ $statusStyles[$reg->status] ?? $statusStyles['pending_payment'] }}">
                                            {{ $statusLabels[$reg->status] ?? 'Unknown' }}
                                        </span>
                                        <p class="text-[10px] text-gray-500 font-normal mt-1.5">{{ $reg->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-12 flex flex-col items-center justify-center text-gray-500">
                            <i class="fas fa-user-plus text-2xl mb-2 opacity-20"></i>
                            <p class="text-xs font-normal tracking-wide">ยังไม่มีการสมัครใหม่</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>

    </div>

    {{-- โหลด Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('growthChart').getContext('2d');
            
            // 🚀 บังคับใช้สี Dark Mode แบบ 100% สำหรับ Chart.js
            const gridColor = 'rgba(255, 255, 255, 0.05)';
            const textColor = '#9ca3af'; // gray-400
            
            // สร้าง Gradient ไล่สีจากน้ำเงินไปใส
            let gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(59, 130, 246, 0.4)'); // blue-500
            gradient.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

            // ดึงตัวแปรจาก Controller มาใช้งานจริง
            const rawLabels = @json($chartLabels ?? []);
            const rawData = @json($chartData ?? []);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: rawLabels,
                    datasets: [{
                        label: 'จำนวนทีมที่สมัคร',
                        data: rawData,
                        borderColor: '#3b82f6', // blue-500
                        backgroundColor: gradient,
                        borderWidth: 2.5,
                        pointBackgroundColor: '#121212', // จุดเป็นสีดำ
                        pointBorderColor: '#3b82f6', // ขอบจุดเป็นสีน้ำเงิน
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.4 // ทำให้เส้นกราฟมีความโค้ง
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false // ซ่อนแถบ Legend ด้านบนเพราะมีเส้นเดียว
                        },
                        tooltip: {
                            backgroundColor: '#1f2937',
                            titleColor: '#ffffff',
                            bodyColor: '#d1d5db',
                            borderColor: 'rgba(255,255,255,0.1)',
                            borderWidth: 1,
                            padding: 10,
                            displayColors: false,
                            titleFont: { family: 'Kanit', weight: 'normal' },
                            bodyFont: { family: 'Kanit', weight: 'normal' }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: gridColor,
                                borderDash: [5, 5] // เส้นกริดแบบประ
                            },
                            ticks: {
                                color: textColor,
                                font: { family: 'Kanit', size: 10 },
                                stepSize: 1, // บังคับสเกลให้เป็นจำนวนเต็ม
                                precision: 0 // ห้ามมีทศนิยม
                            },
                            border: { display: false }
                        },
                        x: {
                            grid: {
                                display: false // ซ่อนเส้นกริดแนวตั้งให้ดูสะอาดตา
                            },
                            ticks: {
                                color: textColor,
                                font: { family: 'Kanit', size: 11 }
                            },
                            border: { display: false }
                        }
                    }
                }
            });
        });
    </script>
</x-admin-layout>