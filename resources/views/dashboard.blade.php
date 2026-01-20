<x-app-layout>
    {{-- 1. HEADER SECTION --}}
    <div class="px-8 py-6">
        <h1 class="text-3xl font-bold text-slate-900 dark:text-white transition-colors duration-500">Selamat Datang, {{ Auth::user()->name }}! 👋</h1>
        <p class="text-slate-500 dark:text-slate-400 uppercase text-[10px] font-bold tracking-[0.2em] transition-colors duration-500">Berikut ringkasan keuangan kamu di bulan <span class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ $currentMonth }}</span></p>
    </div>

    {{-- 2. WADAH UTAMA --}}
    <div class="mx-4 md:mx-8 mb-8 p-6 md:p-10 bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-slate-800 rounded-[2.5rem] md:rounded-[3.5rem] shadow-xl dark:shadow-2xl transition-all duration-500">
        
        {{-- Row Kartu Statistik --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            
            {{-- 1. Pemasukan --}}
            <div class="group bg-emerald-500/5 hover:bg-emerald-500/10 p-6 rounded-[2rem] border border-emerald-500/20 transition-all duration-300 relative overflow-hidden">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] font-black text-emerald-600 dark:text-emerald-500 uppercase tracking-widest">Pemasukan</p>
                        <h2 class="text-2xl font-black text-slate-900 dark:text-white mt-2">{{ Auth::user()->formatCurrency($totalIncome) }}</h2>
                    </div>
                    <div class="p-2.5 bg-emerald-500 rounded-2xl shadow-lg shadow-emerald-500/40">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 19V5m0 0l-7 7m7-7l7 7"/>
                        </svg>
                    </div>
                </div>
                <div class="flex items-center gap-1 mt-3">
                    <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    <p class="text-[10px] font-bold text-emerald-600 dark:text-emerald-500">Normal <span class="text-slate-400 font-medium">Bulan ini</span></p>
                </div>
                <div class="absolute -top-2 -right-2 w-16 h-16 bg-emerald-500/10 rounded-full blur-2xl group-hover:bg-emerald-500/20 transition-all"></div>
            </div>

            {{-- 2. Pengeluaran Transaksi (Riil) --}}
            <div class="group bg-rose-500/5 hover:bg-rose-500/10 p-6 rounded-[2rem] border border-rose-500/20 transition-all duration-300 relative overflow-hidden">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] font-black text-rose-600 dark:text-rose-500 uppercase tracking-widest">Belanja Riil</p>
                        <h2 class="text-2xl font-black text-slate-900 dark:text-white mt-2">{{ Auth::user()->formatCurrency($totalExpense) }}</h2>
                    </div>
                    <div class="p-2.5 bg-rose-500 rounded-2xl shadow-lg shadow-rose-500/40">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m0 0l7-7m-7 7l-7-7"/>
                        </svg>
                    </div>
                </div>
                <div class="flex items-center gap-1 mt-3">
                    <svg class="w-3 h-3 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
                    <p class="text-[10px] font-bold text-rose-600 dark:text-rose-500">Aktif <span class="text-slate-400 font-medium">Bulan ini</span></p>
                </div>
                <div class="absolute -top-2 -right-2 w-16 h-16 bg-rose-500/10 rounded-full blur-2xl group-hover:bg-rose-500/20 transition-all"></div>
            </div>

            {{-- 3. Alokasi Budget --}}
            <div class="group bg-amber-500/5 hover:bg-amber-500/10 p-6 rounded-[2rem] border border-amber-500/20 transition-all duration-300 relative overflow-hidden">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] font-black text-amber-600 dark:text-amber-500 uppercase tracking-widest">Jatah Budget</p>
                        <h2 class="text-2xl font-black text-slate-900 dark:text-white mt-2">{{ Auth::user()->formatCurrency($totalBudgetAllocation) }}</h2>
                    </div>
                    <div class="p-2.5 bg-amber-500 rounded-2xl shadow-lg shadow-amber-500/40">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                </div>
                
                <div class="flex items-center gap-1.5 mt-3">
                    {{-- Icon Gembok --}}
                    <svg class="w-3 h-3 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                    <p class="text-[10px] font-bold text-amber-600 dark:text-amber-500">Dana teralokasi</p>
                </div>

                <div class="absolute -top-2 -right-2 w-16 h-16 bg-amber-500/10 rounded-full blur-2xl group-hover:bg-amber-500/20 transition-all"></div>
            </div>

            {{-- 4. Saldo Bersih --}}
            <div class="group bg-blue-600/10 hover:bg-blue-600/20 p-6 rounded-[2rem] border border-blue-500/20 transition-all duration-300 relative overflow-hidden">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-widest">Sisa Saldo</p>
                        <h2 class="text-2xl font-black text-slate-900 dark:text-white mt-2">{{ Auth::user()->formatCurrency($netSavings) }}</h2>
                    </div>
                    <div class="p-2.5 bg-blue-600 rounded-2xl shadow-lg shadow-blue-600/40">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 11-6 0H5.25A2.25 2.25 0 003 12m18 0v6a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 9m18 0V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v3"/>
                        </svg>
                    </div>
                </div>
                <div class="flex items-center gap-1 mt-3">
                    <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-[10px] font-bold text-blue-600 dark:text-blue-400">Ready to use</p>
                </div>
                <div class="absolute -top-2 -right-2 w-16 h-16 bg-blue-600/10 rounded-full blur-2xl group-hover:bg-blue-600/20 transition-all"></div>
            </div>
        </div>

        {{-- Row Bawah (Chart & Recent Transactions) --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            {{-- Spending Chart --}}
            <div class="bg-slate-50 dark:bg-slate-900/40 p-8 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 transition-colors duration-500">
                <h3 class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-8">Spending by Category</h3>
                <div class="flex flex-col md:flex-row items-center gap-12">
                    <div class="w-full md:w-1/2 h-[240px]">
                        <canvas id="spendingChart"></canvas>
                    </div>
                    <div class="w-full md:w-1/2 space-y-5">
                        @foreach($activeBudgets->take(4) as $budget)
                        <div class="flex items-center justify-between group">
                            <div class="flex items-center gap-3">
                                <div class="w-2.5 h-2.5 rounded-full" style="background-color: {{ ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'][$loop->index % 5] }}"></div>
                                <span class="text-sm font-bold text-slate-500 dark:text-slate-400 group-hover:text-slate-900 dark:group-hover:text-white transition-colors duration-300">{{ $budget->category->name }}</span>
                            </div>
                            <span class="text-sm font-black text-slate-900 dark:text-white transition-colors duration-500">{{ $budget->percentage }}%</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Recent Transactions --}}
            <div class="bg-slate-50 dark:bg-slate-900/40 p-8 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 transition-colors duration-500">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em]">Recent Transactions</h3>
                    <a href="{{ route('transactions.index') }}" class="text-[10px] font-black text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 uppercase tracking-tighter transition-colors">View All Transactions →</a>
                </div>
                <div class="space-y-6">
                    @forelse($transactions as $transaction)
                        <div class="flex items-center justify-between group p-2 hover:bg-white dark:hover:bg-slate-800/30 rounded-2xl transition-all duration-300">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-white dark:bg-slate-800 flex items-center justify-center text-xl border border-slate-200 dark:border-slate-700 shadow-sm transition-colors duration-500">
                                    {{ $transaction->category->icon ?? '💰' }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-300">{{ $transaction->description ?? 'Tanpa Deskripsi' }}</p>
                                    <p class="text-[10px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-tight">{{ \Carbon\Carbon::parse($transaction->transaction_date)->timezone('Asia/Jakarta')->translatedFormat('d M Y | H:i') }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-black {{ $transaction->type == 'income' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                    {{ $transaction->amount < 0 ? '+' : ($transaction->type == 'income' ? '+' : '-') }} {{ Auth::user()->formatCurrency(abs($transaction->amount)) }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-slate-400 py-10 italic">Belum ada transaksi.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- AI Section --}}
        <div class="mt-10 bg-gradient-to-r from-indigo-500/5 to-purple-500/5 dark:from-indigo-500/10 dark:to-purple-500/10 p-8 rounded-[2.5rem] border border-indigo-200 dark:border-indigo-500/20 border-dashed transition-colors duration-500">
            <div class="flex flex-col md:flex-row gap-6 items-center">
                <div class="w-16 h-16 bg-gradient-to-br from-amber-400 to-orange-500 rounded-2xl flex items-center justify-center text-3xl shadow-xl shadow-orange-500/20 shrink-0">💡</div>
                <div class="flex-1 text-center md:text-left">
                    <h4 class="text-xs font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-[0.2em] mb-2">G-Money AI Insights</h4>
                    <div class="text-slate-600 dark:text-slate-300 leading-relaxed">
                        @php $firstName = explode(' ', Auth::user()->name)[0]; @endphp
                        
                        @if($totalExpense > $totalIncome)
                            Waduh <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ $firstName }}</span>, pengeluaranmu <span class="font-bold text-rose-600 dark:text-rose-400 underline decoration-rose-400/30">melebihi pemasukan</span> nih!
                        @else
                            Mantap <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ $firstName }}</span>! Saldo kamu bulan ini <span class="font-bold text-emerald-600 dark:text-emerald-400 underline decoration-emerald-400/30">surplus</span>.
                        @endif
                        
                        <button @click="$dispatch('open-ai-chat')" class="flex items-center gap-2 mt-4 text-indigo-600 dark:text-indigo-400 font-bold text-sm hover:gap-3 transition-all duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                            Chat dengan G-Money <span class="text-lg">›</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- G-Money Floating Chat Widget --}}
        <div x-data="{ 
                open: false, 
                userName: '{{ explode(' ', Auth::user()->name)[0] }}',
                messages: [] 
            }" 
            x-init="messages = [{role: 'bot', text: 'Halo ' + userName + '! Ada yang bisa G-Money bantu soal keuanganmu hari ini?'}]"
            @open-ai-chat.window="open = true"
            class="fixed bottom-6 right-6 z-[9999]">
            
            {{-- Chat Box --}}
            <div x-show="open" 
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-10 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-10 scale-95"
                class="bg-white dark:bg-slate-900 w-[350px] h-[500px] rounded-[2.5rem] shadow-2xl border border-slate-200 dark:border-slate-800 flex flex-col overflow-hidden mb-4">
                
                {{-- Header Ala Instagram --}}
                <div class="p-5 border-b dark:border-slate-800 flex justify-between items-center bg-white dark:bg-slate-900">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-tr from-indigo-600 to-purple-500 rounded-full flex items-center justify-center shadow-lg shadow-indigo-500/20 text-xl">
                            🤖
                        </div>
                        <div>
                            <p class="font-black text-sm dark:text-white uppercase tracking-tight">G-Money AI</p>
                            <p class="text-[10px] text-emerald-500 font-bold uppercase tracking-widest flex items-center gap-1">
                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span> Online
                            </p>
                        </div>
                    </div>
                    <button @click="open = false" class="p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Area Pesan --}}
                <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-slate-50/50 dark:bg-slate-900/50 scrollbar-hide">
                    <template x-for="msg in messages">
                        <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                            <div :class="msg.role === 'user' 
                                ? 'bg-indigo-600 text-white rounded-2xl rounded-tr-none px-4 py-2.5 max-w-[85%] shadow-lg shadow-indigo-500/20' 
                                : 'bg-white dark:bg-slate-800 dark:text-slate-200 rounded-2xl rounded-tl-none px-4 py-2.5 max-w-[85%] border border-slate-100 dark:border-slate-700 shadow-sm'">
                                <p class="text-sm font-medium leading-relaxed" x-text="msg.text"></p>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Input Area --}}
                <div class="p-4 bg-white dark:bg-slate-900 border-t dark:border-slate-800">
                    <form @submit.prevent="if($refs.input.value) { messages.push({role: 'user', text: $refs.input.value}); $refs.input.value = ''; }" class="relative flex items-center">
                        <input x-ref="input" type="text" placeholder="Tanya ke G-Money..." 
                            class="w-full bg-slate-100 dark:bg-slate-800 border-none rounded-2xl px-4 py-3 pr-12 text-sm focus:ring-2 focus:ring-indigo-500 dark:text-white placeholder:text-slate-400">
                        <button type="submit" class="absolute right-2 text-indigo-600 dark:text-indigo-400 hover:scale-110 transition-transform p-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Floating Button --}}
            <button x-show="!open" @click="open = true"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="scale-0 rotate-180"
                    x-transition:enter-end="scale-100 rotate-0"
                    class="bg-gradient-to-tr from-indigo-600 to-purple-500 p-4 rounded-[1.5rem] shadow-2xl shadow-indigo-500/40 hover:scale-110 transition-transform group">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
            </button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('spendingChart');
            const isDark = document.documentElement.classList.contains('dark');
            const borderColor = isDark ? '#0f172a' : '#ffffff';

            if (ctx) {
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: {!! json_encode($activeBudgets->pluck('category.name')) !!},
                        datasets: [{
                            data: {!! json_encode($activeBudgets->pluck('realized')) !!},
                            backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
                            borderWidth: 8,
                            borderColor: borderColor,
                            hoverOffset: 15
                        }]
                    },
                    options: {
                        cutout: '75%',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } }
                    }
                });
            }
        });
    </script>
</x-app-layout>