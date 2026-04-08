<x-admin-layout>
    <div class="max-w-7xl mx-auto space-y-6">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white flex items-center gap-3">
                    <div class="p-2.5 bg-blue-50 dark:bg-blue-500/10 rounded-xl text-blue-600 dark:text-blue-400">
                        <i class="fas fa-users-cog"></i>
                    </div>
                    จัดการผู้ใช้งาน
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    ค้นหา กำหนดสิทธิ์การเข้าถึง และดูข้อมูลสมาชิกทั้งหมดในระบบ
                </p>
            </div>
        </div>

        <!-- Search Bar & Filters -->
        <div class="bg-white dark:bg-[#121212] p-5 lg:p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-white/5">
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col md:flex-row gap-4 items-end justify-between">
                
                <div class="flex flex-col md:flex-row gap-4 w-full flex-1">
                    <!-- ช่องค้นหาแบบข้อความ -->
                    <div class="w-full md:w-2/3 relative group">
                        <label class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2 ml-1">ค้นหาบัญชีผู้ใช้</label>
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pt-6 pointer-events-none text-gray-400 group-focus-within:text-blue-500 transition-colors">
                            <i class="fas fa-search"></i>
                        </div>
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="พิมพ์ชื่อ, อีเมล หรือเบอร์โทรศัพท์..." 
                            class="w-full bg-gray-50/50 border border-gray-200 text-gray-900 text-sm font-normal rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 block pl-11 pr-4 py-2.5 dark:bg-[#0a0a0a] dark:border-white/10 dark:placeholder-gray-500 dark:text-white hover:border-gray-300 dark:hover:border-gray-600 transition-all duration-200">
                    </div>
                    
                    <!-- ช่องกรองสิทธิ์ (Custom Select) -->
                    <div class="w-full md:w-1/3 relative" 
                        x-data="{ 
                            open: false, 
                            selected: '{{ request('role', '') }}',
                            options: [
                                { value: '', label: 'ทุกระดับสิทธิ์', icon: 'fa-layer-group' },
                                { value: 'user', label: 'USER (ผู้ใช้ทั่วไป)', icon: 'fa-user' },
                                { value: 'staff', label: 'STAFF (ทีมงาน)', icon: 'fa-user-tie' },
                                { value: 'admin', label: 'ADMIN (ผู้ดูแลระบบ)', icon: 'fa-crown' }
                            ],
                            get selectedOption() {
                                return this.options.find(opt => opt.value === this.selected) || this.options[0];
                            }
                        }" 
                        @click.away="open = false">
                        
                        <label class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2 ml-1">กรองตามสิทธิ์</label>
                        <input type="hidden" name="role" x-model="selected">
                        
                        <button type="button" @click="open = !open" 
                            class="w-full bg-gray-50/50 border border-gray-200 text-gray-900 text-sm font-normal rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 flex justify-between items-center pl-4 pr-3 py-2.5 dark:bg-[#0a0a0a] dark:border-white/10 dark:text-white hover:border-gray-300 dark:hover:border-gray-600 transition-all duration-200"
                            :class="open ? 'border-blue-500 ring-2 ring-blue-500/20 bg-white dark:bg-[#121212]' : ''">
                            <div class="flex items-center gap-2.5">
                                <i class="fas text-gray-400 w-4 text-center" :class="selectedOption.icon"></i>
                                <span x-text="selectedOption.label"></span>
                            </div>
                            <i class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                        </button>

                        <div x-show="open" 
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                            x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                            style="display: none;" 
                            class="absolute z-[100] w-full mt-2 bg-white dark:bg-[#1a1a1a] border border-gray-100 dark:border-white/10 rounded-xl shadow-xl py-2 overflow-hidden">
                            
                            <template x-for="option in options" :key="option.value">
                                <button type="button" @click="selected = option.value; open = false;" 
                                    class="w-full text-left px-4 py-2.5 text-sm transition-colors flex items-center justify-between hover:bg-gray-50 dark:hover:bg-white/5"
                                    :class="selected === option.value ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-500/10 font-semibold' : 'text-gray-700 dark:text-gray-300'">
                                    <div class="flex items-center gap-2.5">
                                        <i class="fas w-4 text-center opacity-70" :class="option.icon"></i>
                                        <span x-text="option.label"></span>
                                    </div>
                                    <i x-show="selected === option.value" class="fas fa-check text-blue-500"></i>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
                
                <!-- Buttons -->
                <div class="flex flex-row gap-3 w-full md:w-auto">
                    <button type="submit" 
                        class="flex-1 md:flex-none px-7 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium transition-all duration-200 text-center shadow-sm flex items-center justify-center whitespace-nowrap active:scale-95">
                        <i class="fas fa-search mr-2"></i> ค้นหา
                    </button>

                    @if(request()->has('search') && (request('search') != '' || request('role') != ''))
                        <a href="{{ route('admin.users.index') }}" 
                            class="flex-1 md:flex-none px-5 py-2.5 rounded-xl bg-gray-100 text-gray-600 hover:bg-red-50 hover:text-red-600 dark:bg-[#1a1a1a] dark:text-gray-300 dark:hover:bg-red-500/10 dark:hover:text-red-400 border border-transparent dark:border-white/5 text-sm font-medium transition-all duration-200 text-center flex items-center justify-center whitespace-nowrap title='ล้างการค้นหา' active:scale-95">
                            <i class="fas fa-eraser mr-2 md:mr-0"></i> <span class="md:hidden">ล้างค่า</span>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Data Table -->
        <div class="bg-white dark:bg-[#121212] rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 overflow-hidden">
            <div class="overflow-x-auto min-h-[350px]">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400 relative">
                    <thead class="text-xs text-gray-400 uppercase bg-gray-50/50 dark:bg-[#0a0a0a] dark:text-gray-500 border-b border-gray-100 dark:border-white/5">
                        <tr>
                            <th scope="col" class="px-6 py-5 font-semibold tracking-wider w-20 text-center">โปรไฟล์</th>
                            <th scope="col" class="px-6 py-5 font-semibold tracking-wider">บัญชีผู้ใช้</th>
                            <th scope="col" class="px-6 py-5 font-semibold tracking-wider">สิทธิ์การเข้าถึง</th>
                            <th scope="col" class="px-6 py-5 font-semibold tracking-wider text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-white/5">
                        @forelse($users as $user)
                            <tr class="bg-white dark:bg-[#121212] hover:bg-blue-50/30 dark:hover:bg-white/[0.02] transition-colors duration-200 group"
                                x-data="{ showUserModal: false }">
                                
                                <td class="px-6 py-4 text-center">
                                    <div class="inline-flex w-11 h-11 rounded-full bg-gray-100 dark:bg-[#1a1a1a] ring-2 ring-transparent group-hover:ring-blue-100 dark:group-hover:ring-blue-500/20 items-center justify-center overflow-hidden shrink-0 transition-all">
                                        @if($user->avatar)
                                            <img src="{{ Str::startsWith($user->avatar, ['http://', 'https://']) ? $user->avatar : asset('storage/' . $user->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                                        @else
                                            <i class="fas fa-user text-gray-400 text-lg"></i>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="text-gray-900 dark:text-white font-semibold text-[15px]">{{ $user->name }}</div>
                                    <div class="text-xs text-gray-500 mt-1 flex items-center gap-1.5">
                                        <i class="fas fa-envelope text-gray-300 dark:text-gray-600"></i> {{ $user->email }}
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    @if($user->role === 'admin')
                                        <div class="inline-flex items-center px-3.5 py-1.5 rounded-xl bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 text-[11px] font-bold tracking-widest border border-red-100 dark:border-red-500/20">
                                            <i class="fas fa-crown mr-1.5"></i> ADMIN
                                        </div>
                                    @else
                                        <form id="role-form-{{ $user->id }}" action="{{ route('admin.users.updateRole', $user->id) }}" method="POST" class="hidden">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="role" id="role-input-{{ $user->id }}">
                                        </form>

                                        <div x-data="{ open: false }" @click.away="open = false" class="relative w-[130px]" :class="open ? 'z-50' : 'z-10'">
                                            
                                            <!-- Role Badge Toggle -->
                                            <button type="button" @click="open = !open" 
                                                class="w-full text-xs font-bold tracking-wide rounded-xl flex justify-between items-center px-3 py-2 border transition-all duration-200 focus:outline-none focus:ring-2
                                                {{ $user->role === 'staff' 
                                                    ? 'bg-indigo-50 border-indigo-100 text-indigo-600 dark:bg-indigo-500/10 dark:border-indigo-500/20 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-500/20 focus:ring-indigo-500/20' 
                                                    : 'bg-gray-50 border-gray-200 text-gray-600 dark:bg-[#1a1a1a] dark:border-white/10 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/5 focus:ring-gray-500/20' }}"
                                                :class="open ? 'ring-2 ring-blue-500/20 border-blue-300 bg-white dark:bg-[#121212]' : ''">
                                                
                                                <div class="flex items-center gap-2">
                                                    @if($user->role === 'staff')
                                                        <i class="fas fa-user-tie"></i>
                                                    @else
                                                        <i class="fas fa-user opacity-70"></i>
                                                    @endif
                                                    <span>{{ strtoupper($user->role) }}</span>
                                                </div>
                                                <i class="fas fa-chevron-down text-[10px] opacity-50 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                                            </button>
                                            
                                            <!-- Dropdown Menu -->
                                            <div x-show="open" 
                                                x-transition:enter="transition ease-out duration-200"
                                                x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                                x-transition:leave="transition ease-in duration-150"
                                                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                                x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                                                style="display: none;" 
                                                class="absolute z-50 w-full mt-1.5 bg-white dark:bg-[#1e1e1e] border border-gray-100 dark:border-white/10 rounded-xl shadow-xl py-1 overflow-hidden">
                                                
                                                <button type="button" @click="confirmRoleChange('{{ $user->id }}', 'user', '{{ $user->name }}'); open=false" 
                                                    class="w-full text-left px-3 py-2 text-xs font-semibold tracking-wide transition-colors flex items-center justify-between text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5">
                                                    <div class="flex items-center gap-2">
                                                        <i class="fas fa-user opacity-50 w-3 text-center"></i> USER
                                                    </div>
                                                    <i x-show="'{{ $user->role }}' === 'user'" class="fas fa-check text-blue-500"></i>
                                                </button>
                                                
                                                <button type="button" @click="confirmRoleChange('{{ $user->id }}', 'staff', '{{ $user->name }}'); open=false" 
                                                    class="w-full text-left px-3 py-2 text-xs font-semibold tracking-wide transition-colors flex items-center justify-between text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-white/5">
                                                    <div class="flex items-center gap-2">
                                                        <i class="fas fa-user-tie opacity-80 w-3 text-center"></i> STAFF
                                                    </div>
                                                    <i x-show="'{{ $user->role }}' === 'staff'" class="fas fa-check text-indigo-500"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <button @click="showUserModal = true" class="w-9 h-9 flex items-center justify-center text-gray-400 hover:text-blue-600 bg-gray-50 hover:bg-blue-50 dark:bg-white/5 dark:hover:bg-blue-500/10 rounded-xl transition-all hover:scale-105 active:scale-95" title="ดูข้อมูล">
                                            <i class="fas fa-id-badge"></i>
                                        </button>

                                        @if($user->id !== auth()->id() && !$user->isAdmin())
                                            <button onclick="confirmDelete('{{ route('admin.users.destroy', $user->id) }}', 'บัญชีของคุณ {{ $user->name }}')" 
                                                class="w-9 h-9 flex items-center justify-center text-gray-400 hover:text-red-500 bg-gray-50 hover:bg-red-50 dark:bg-white/5 dark:hover:bg-red-500/10 rounded-xl transition-all hover:scale-105 active:scale-95" title="ลบผู้ใช้">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        @endif
                                    </div>

                                    <!-- 🚀 Simple & Clean Profile Modal 🚀 -->
                                    <template x-teleport="body">
                                        <div x-show="showUserModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6">
                                            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="showUserModal = false"
                                                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
                                            
                                            <div class="relative w-full max-w-lg bg-white dark:bg-[#121212] rounded-2xl shadow-xl overflow-hidden border border-gray-200 dark:border-white/10"
                                                 x-transition:enter="transition ease-out duration-300"
                                                 x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
                                                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                                 x-transition:leave="transition ease-in duration-200"
                                                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                                 x-transition:leave-end="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95">
                                                
                                                <!-- Header -->
                                                <div class="px-6 py-4 border-b border-gray-100 dark:border-white/5 flex justify-between items-center bg-gray-50/50 dark:bg-[#0a0a0a]">
                                                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">ข้อมูลผู้ใช้งาน</h3>
                                                    <button @click="showUserModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>

                                                <!-- Body -->
                                                <div class="p-6 text-left">
                                                    <!-- Profile Header Row -->
                                                    <div class="flex items-center gap-5 mb-6">
                                                        <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-[#1a1a1a] border border-gray-200 dark:border-white/10 flex items-center justify-center overflow-hidden shrink-0">
                                                            @if($user->avatar)
                                                                <img src="{{ Str::startsWith($user->avatar, ['http://', 'https://']) ? $user->avatar : asset('storage/' . $user->avatar) }}" class="w-full h-full object-cover">
                                                            @else
                                                                <i class="fas fa-user text-2xl text-gray-400"></i>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <h4 class="text-lg font-bold text-gray-900 dark:text-white">{{ $user->name }}</h4>
                                                            <div class="text-sm text-gray-500 mt-1 flex flex-wrap items-center gap-2">
                                                                <span>{{ $user->email }}</span>
                                                                <span class="text-gray-300 dark:text-gray-700 hidden sm:inline">|</span>
                                                                
                                                                @if($user->role === 'admin')
                                                                    <span class="text-red-600 dark:text-red-400 font-semibold"><i class="fas fa-crown text-[10px] mr-1"></i> Admin</span>
                                                                @elseif($user->role === 'staff')
                                                                    <span class="text-indigo-600 dark:text-indigo-400 font-semibold"><i class="fas fa-user-tie text-[10px] mr-1"></i> Staff</span>
                                                                @else
                                                                    <span class="text-gray-600 dark:text-gray-400 font-medium"><i class="fas fa-user text-[10px] mr-1"></i> User</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Information Details List -->
                                                    <div class="bg-gray-50/80 dark:bg-[#1a1a1a] rounded-xl p-5 border border-gray-100 dark:border-white/5">
                                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-5 gap-x-6">
                                                            <div>
                                                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">ชื่อ-นามสกุล (ไทย)</p>
                                                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->first_name_th ? $user->prefix_th.$user->first_name_th.' '.$user->last_name_th : '-' }}</p>
                                                            </div>
                                                            <div>
                                                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">ชื่อ-นามสกุล (EN)</p>
                                                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->first_name_en ? $user->prefix_en.$user->first_name_en.' '.$user->last_name_en : '-' }}</p>
                                                            </div>
                                                            <div>
                                                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">เบอร์โทรศัพท์</p>
                                                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->phone_number ?? '-' }}</p>
                                                            </div>
                                                            <div>
                                                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">ไซส์เสื้อ / วันเกิด</p>
                                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                                    {{ $user->shirt_size ?? '-' }} 
                                                                    <span class="text-gray-300 dark:text-gray-600 mx-1">|</span> 
                                                                    {{ $user->birthday ? \Carbon\Carbon::parse($user->birthday)->format('d/m/Y') : '-' }}
                                                                </p>
                                                            </div>
                                                            <div class="sm:col-span-2 pt-2 border-t border-gray-200/60 dark:border-white/5">
                                                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">วันที่สมัครสมาชิก</p>
                                                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->created_at->format('d/m/Y H:i') }} น.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Footer -->
                                                <div class="px-6 py-4 bg-gray-50/50 dark:bg-[#0a0a0a] text-right border-t border-gray-100 dark:border-white/5">
                                                    <button @click="showUserModal = false" class="px-5 py-2.5 bg-white dark:bg-[#1a1a1a] border border-gray-200 dark:border-white/10 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-medium hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                                                        ปิดหน้าต่าง
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </td>
                            </tr>
                        @empty
                            <!-- Friendly Empty State -->
                            <tr>
                                <td colspan="4" class="px-6 py-24 text-center text-gray-500 dark:text-gray-400 bg-white dark:bg-[#121212]">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-20 h-20 rounded-2xl bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center mb-5 border border-blue-100 dark:border-white/5">
                                            <i class="fas fa-users-slash text-2xl text-blue-400 dark:text-blue-500/50"></i>
                                        </div>
                                        <p class="text-base text-gray-900 dark:text-white font-bold">ไม่พบข้อมูลบัญชีผู้ใช้</p>
                                        <p class="text-sm mt-1.5 font-normal max-w-sm">
                                            @if(request()->has('search') && (request('search') != '' || request('role') != ''))
                                                ขออภัย ไม่พบสมาชิกที่ตรงกับคำค้นหาของคุณ ลองเปลี่ยนคำหรือล้างตัวกรองดูอีกครั้ง
                                            @else
                                                ยังไม่มีผู้ลงทะเบียนเข้าใช้งานในระบบขณะนี้
                                            @endif
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($users->hasPages())
                <div class="px-6 py-5 border-t border-gray-100 dark:border-white/5 bg-gray-50/50 dark:bg-[#0a0a0a]">
                    {{ $users->appends(['search' => request('search'), 'role' => request('role')])->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- SweetAlert Styling -->
    <script>
        function confirmRoleChange(userId, newRole, userName) {
            Swal.fire({
                title: 'เปลี่ยนระดับสิทธิ์',
                html: `คุณต้องการเปลี่ยนให้ <strong>${userName}</strong><br>เป็น <strong class="text-blue-600 dark:text-blue-400">${newRole.toUpperCase()}</strong> ใช่หรือไม่?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2563eb', 
                cancelButtonColor: '#f3f4f6',
                confirmButtonText: 'ยืนยันการเปลี่ยน',
                cancelButtonText: '<span class="text-gray-700 dark:text-gray-300">ยกเลิก</span>',
                reverseButtons: true,
                background: document.documentElement.classList.contains('dark') ? '#1e1e1e' : '#ffffff',
                color: document.documentElement.classList.contains('dark') ? '#ffffff' : '#111827',
                customClass: {
                    popup: 'rounded-2xl border border-gray-100 dark:border-gray-800 shadow-2xl font-kanit',
                    confirmButton: 'rounded-xl px-6 py-2.5 font-semibold tracking-wide transition-all hover:scale-105',
                    cancelButton: 'rounded-xl px-6 py-2.5 font-semibold tracking-wide transition-all dark:bg-white/5 dark:hover:bg-white/10'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('role-input-' + userId).value = newRole;
                    document.getElementById('role-form-' + userId).submit();
                }
            });
        }
    </script>
</x-admin-layout>