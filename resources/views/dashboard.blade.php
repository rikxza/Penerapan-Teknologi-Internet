<x-app-layout>
    {{-- Custom Styles for Glass Effects --}}
    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            transform: translateZ(0);
            will-change: transform;
        }

        .dark .glass-card {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .health-ring {
            stroke-dasharray: 251.2;
            stroke-dashoffset: calc(251.2 - (251.2 * var(--score)) / 100);
            transition: stroke-dashoffset 1.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Smooth card hover */
        .bento-card {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                box-shadow 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .bento-card:hover {
            transform: translateY(-4px) translateZ(0);
        }
    </style>

    {{-- Main Dashboard Container --}}
    <div class="min-h-screen p-6 md:p-8 transition-colors duration-500">

        {{-- Header Greeting --}}
        <div class="mb-8">
            <h1 class="text-2xl md:text-3xl font-black text-emerald-800 dark:text-white">
                Selamat Datang, {{ explode(' ', Auth::user()->name)[0] }}! 👋
            </h1>
            <p class="text-emerald-600/70 dark:text-emerald-400/70 text-sm font-medium mt-1">
                Ringkasan keuangan bulan <span class="font-bold">{{ $currentMonth }}</span>
            </p>
        </div>

        {{-- Bento Grid Layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Financial Health Score Card (Large) --}}
            <div
                class="lg:col-span-2 glass-card bento-card rounded-[2rem] p-8 relative overflow-hidden animate-fade-in">
                <div class="flex flex-col md:flex-row items-center gap-8">
                    {{-- Score Ring --}}
                    <div class="relative w-40 h-40 shrink-0">
                        <svg class="w-40 h-40 transform -rotate-90" viewBox="0 0 100 100">
                            <circle cx="50" cy="50" r="40" fill="none" stroke="currentColor"
                                class="text-emerald-200 dark:text-emerald-900" stroke-width="8" />
                            <circle cx="50" cy="50" r="40" fill="none" class="text-emerald-500 health-ring"
                                stroke="currentColor" stroke-width="8" stroke-linecap="round"
                                style="--score: {{ $healthScore }}" />
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span
                                class="text-4xl font-black text-emerald-700 dark:text-emerald-400">{{ $healthScore }}</span>
                            <span class="text-xs font-bold text-emerald-600/60 dark:text-emerald-400/60">/100</span>
                        </div>
                    </div>

                    {{-- Score Info --}}
                    <div class="text-center md:text-left flex-1">
                        <p
                            class="text-xs font-black text-emerald-600 dark:text-emerald-500 uppercase tracking-widest mb-2">
                            Your Financial Health Score</p>
                        <h2 class="text-3xl md:text-4xl font-black text-emerald-800 dark:text-white">
                            {{ $healthScore }}/100
                        </h2>
                        <p class="text-emerald-600/80 dark:text-emerald-400/80 mt-2 font-medium">{{ $healthStatus }}</p>

                        <div class="flex flex-wrap gap-2 mt-4">
                            <span
                                class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-700 dark:text-emerald-400">
                                💰 Savings:
                                {{ $totalIncome > 0 ? round((($totalIncome - $totalExpense) / $totalIncome) * 100) : 0 }}%
                            </span>
                            <span
                                class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-700 dark:text-emerald-400">
                                📊 {{ $activeBudgets->count() }} Budgets
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Decorative blur --}}
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-emerald-400/20 rounded-full blur-3xl"></div>
            </div>

            {{-- AI Chat Preview --}}
            <div class="glass-card bento-card rounded-[2rem] p-6 flex flex-col animate-fade-in delay-100">
                <div class="flex items-center gap-3 mb-4">
                    <div
                        class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-2xl flex items-center justify-center text-xl shadow-lg shadow-indigo-500/30">
                        🤖
                    </div>
                    <div>
                        <p class="font-black text-emerald-800 dark:text-white text-sm">G-Money AI</p>
                        <p
                            class="text-[10px] text-emerald-500 font-bold uppercase tracking-widest flex items-center gap-1">
                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span> Online
                        </p>
                    </div>
                </div>

                <div class="glass-card rounded-2xl p-4 flex-1 mb-4" x-data="{ insight: 'Memuat insight...', loading: true, loadInsight() { 
                        this.loading = true; 
                        fetch('{{ route('ai.insight') }}', { headers: { 'Accept': 'application/json' } })
                            .then(r => { if(!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
                            .then(d => { this.insight = d.insight || d.message || 'Tidak ada insight'; this.loading = false; })
                            .catch(e => { this.insight = '⚠️ Gagal memuat: ' + e.message; this.loading = false; });
                    } }">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[9px] font-bold text-emerald-500 uppercase tracking-widest">AI Insight</span>
                        <button @click="loadInsight()"
                            class="text-emerald-500 hover:text-emerald-400 transition-colors p-1"
                            title="Refresh Insight">
                            <svg class="w-4 h-4" :class="loading && 'animate-spin'" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </button>
                    </div>
                    <p class="text-sm text-emerald-700 dark:text-emerald-300 leading-relaxed whitespace-pre-line"
                        x-text="insight" x-init="loadInsight()">
                    </p>
                </div>

                <div class="flex gap-2">
                    <button @click="$dispatch('open-ai-chat')"
                        class="flex-1 bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-2.5 px-4 rounded-xl text-xs transition-all hover:scale-[1.02]">
                        Chat with AI
                    </button>
                </div>
            </div>

            {{-- Stat Cards Row --}}
            <div class="lg:col-span-3 grid grid-cols-2 md:grid-cols-4 gap-4">
                {{-- Balance --}}
                <div class="glass-card rounded-2xl p-5 group hover:scale-[1.02] transition-transform">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-[10px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-widest">
                            Balance</p>
                        <div class="w-8 h-8 bg-blue-500 rounded-xl flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 11-6 0H5.25A2.25 2.25 0 003 12m18 0v6a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 9m18 0V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v3" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-xl font-black text-emerald-800 dark:text-white">
                        {{ Auth::user()->formatCurrency($netSavings) }}
                    </p>
                </div>

                {{-- Income --}}
                <div class="glass-card bento-card rounded-2xl p-5">
                    <div class="flex items-center justify-between mb-3">
                        <p
                            class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">
                            Income</p>
                        <div class="w-8 h-8 bg-emerald-500 rounded-xl flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19V5m0 0l-7 7m7-7l7 7" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-xl font-black text-emerald-800 dark:text-white">
                        {{ Auth::user()->formatCurrency($totalIncome) }}
                    </p>
                </div>

                {{-- Expense --}}
                <div class="glass-card bento-card rounded-2xl p-5">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-[10px] font-black text-rose-600 dark:text-rose-400 uppercase tracking-widest">
                            Expense</p>
                        <div class="w-8 h-8 bg-rose-500 rounded-xl flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m0 0l7-7m-7 7l-7-7" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-xl font-black text-emerald-800 dark:text-white">
                        {{ Auth::user()->formatCurrency($totalExpense) }}
                    </p>
                </div>

                {{-- Savings --}}
                <div class="glass-card bento-card rounded-2xl p-5">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-[10px] font-black text-amber-600 dark:text-amber-400 uppercase tracking-widest">
                            Budget</p>
                        <div class="w-8 h-8 bg-amber-500 rounded-xl flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-xl font-black text-emerald-800 dark:text-white">
                        {{ Auth::user()->formatCurrency($totalBudgetAllocation) }}
                    </p>
                </div>
            </div>

            {{-- Expense by Category Pie Chart --}}
            <div class="glass-card bento-card rounded-[2rem] p-6 animate-fade-in delay-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xs font-black text-emerald-600 dark:text-emerald-500 uppercase tracking-widest">
                        Expense by Category</h3>
                    <span class="text-[10px] font-bold text-emerald-500/60">{{ $currentMonth }}</span>
                </div>

                @if($expenseByCategory->count() > 0)
                    <div class="relative h-48 flex items-center justify-center">
                        <canvas id="expensePieChart"></canvas>
                    </div>
                    <div class="mt-4 space-y-2">
                        @foreach($expenseByCategory->take(4) as $category => $amount)
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-medium text-emerald-700 dark:text-emerald-300">{{ $category }}</span>
                                <span
                                    class="font-bold text-emerald-800 dark:text-white">{{ Auth::user()->formatCurrency($amount) }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="h-48 flex items-center justify-center">
                        <p class="text-emerald-600/60 dark:text-emerald-400/60 text-sm italic">Belum ada pengeluaran</p>
                    </div>
                @endif
            </div>

            {{-- Recent Transactions --}}
            <div class="lg:col-span-2 glass-card bento-card rounded-[2rem] p-6 animate-fade-in delay-300">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xs font-black text-emerald-600 dark:text-emerald-500 uppercase tracking-widest">
                        Recent Transactions</h3>
                    <a href="{{ route('transactions.index') }}"
                        class="text-[10px] font-black text-emerald-500 hover:text-emerald-600 uppercase tracking-tight">View
                        All →</a>
                </div>

                <div class="space-y-4">
                    @forelse($transactions as $transaction)
                        <div
                            class="flex items-center justify-between p-3 rounded-2xl hover:bg-white/50 dark:hover:bg-white/5 transition-all duration-200">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-white dark:bg-slate-800 flex items-center justify-center text-xl border border-emerald-200/50 dark:border-slate-700 shadow-sm">
                                    {{ $transaction->category->icon ?? '💰' }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-emerald-800 dark:text-white">
                                        {{ $transaction->description ?? 'No Description' }}
                                    </p>
                                    <p class="text-[10px] text-emerald-600/60 dark:text-emerald-400/60 font-medium">
                                        {{ \Carbon\Carbon::parse($transaction->transaction_date)->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                            <p
                                class="text-sm font-black {{ $transaction->type == 'income' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                {{ $transaction->type == 'income' ? '+' : '-' }}{{ Auth::user()->formatCurrency(abs($transaction->amount)) }}
                            </p>
                        </div>
                    @empty
                        <p class="text-center text-emerald-600/60 dark:text-emerald-400/60 py-8 italic">Belum ada transaksi.
                        </p>
                    @endforelse
                </div>
            </div>

            {{-- Budget Progress --}}
            <div class="glass-card rounded-[2rem] p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xs font-black text-emerald-600 dark:text-emerald-500 uppercase tracking-widest">
                        Budget Progress</h3>
                    <a href="{{ route('budgeting.index') }}"
                        class="text-[10px] font-black text-emerald-500 hover:text-emerald-600 uppercase tracking-tight">Manage
                        →</a>
                </div>

                <div class="space-y-5">
                    @forelse($activeBudgets->take(4) as $budget)
                        <div>
                            <div class="flex justify-between mb-2">
                                <span
                                    class="text-sm font-bold text-emerald-700 dark:text-emerald-300">{{ $budget->category->name }}</span>
                                <span
                                    class="text-xs font-black {{ $budget->percentage > 100 ? 'text-rose-500' : 'text-emerald-600 dark:text-emerald-400' }}">
                                    {{ $budget->percentage }}%
                                </span>
                            </div>
                            <div class="h-2 bg-emerald-200/50 dark:bg-emerald-900/50 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500 {{ $budget->percentage > 100 ? 'bg-rose-500' : ($budget->percentage > 80 ? 'bg-amber-500' : 'bg-emerald-500') }}"
                                    style="width: {{ min($budget->percentage, 100) }}%"></div>
                            </div>
                            <p class="text-[10px] text-emerald-600/60 dark:text-emerald-400/60 mt-1">
                                {{ Auth::user()->formatCurrency($budget->realized) }} /
                                {{ Auth::user()->formatCurrency($budget->amount) }}
                            </p>
                        </div>
                    @empty
                        <p class="text-center text-emerald-600/60 dark:text-emerald-400/60 py-4 italic">Belum ada budget.
                        </p>
                    @endforelse
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="lg:col-span-3 flex flex-wrap gap-4 justify-center">
                <a href="{{ route('scan.receipt') }}"
                    class="glass-card px-6 py-3 rounded-2xl flex items-center gap-3 hover:scale-[1.02] transition-transform group">
                    <div
                        class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                        </svg>
                    </div>
                    <span class="font-bold text-emerald-700 dark:text-emerald-300">Scan Receipt</span>
                </a>

                <a href="{{ route('transactions.create') }}"
                    class="glass-card px-6 py-3 rounded-2xl flex items-center gap-3 hover:scale-[1.02] transition-transform group">
                    <div
                        class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </div>
                    <span class="font-bold text-emerald-700 dark:text-emerald-300">Add Transaction</span>
                </a>
            </div>

        </div>

        {{-- Chart.js CDN --}}
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        {{-- Pie Chart Initialization --}}
        @if($expenseByCategory->count() > 0)
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const ctx = document.getElementById('expensePieChart');
                    if (ctx) {
                        new Chart(ctx, {
                            type: 'doughnut',
                            data: {
                                labels: {!! json_encode($expenseByCategory->keys()) !!},
                                datasets: [{
                                    data: {!! json_encode($expenseByCategory->values()) !!},
                                    backgroundColor: [
                                        'rgba(59, 130, 246, 0.85)',  // blue-500
                                        'rgba(139, 92, 246, 0.85)',  // purple-500
                                        'rgba(249, 115, 22, 0.85)',  // orange-500
                                        'rgba(236, 72, 153, 0.85)',  // pink-500
                                        'rgba(20, 184, 166, 0.85)',  // teal-500
                                        'rgba(245, 158, 11, 0.85)',  // amber-500
                                    ],
                                    borderColor: 'rgba(255, 255, 255, 0.2)',
                                    borderWidth: 2,
                                    hoverOffset: 8
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                cutout: '65%',
                                plugins: {
                                    legend: {
                                        display: false
                                    },
                                    tooltip: {
                                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                        titleColor: '#10b981',
                                        bodyColor: '#fff',
                                        padding: 12,
                                        cornerRadius: 12,
                                        callbacks: {
                                            label: function (context) {
                                                const value = context.parsed;
                                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                                const percentage = ((value / total) * 100).toFixed(1);
                                                return `Rp ${new Intl.NumberFormat('id-ID').format(value)} (${percentage}%)`;
                                            }
                                        }
                                    }
                                },
                                animation: {
                                    animateRotate: true,
                                    animateScale: true,
                                    duration: 1000,
                                    easing: 'easeOutQuart'
                                }
                            }
                        });
                    }
                });
            </script>
        @endif

</x-app-layout>