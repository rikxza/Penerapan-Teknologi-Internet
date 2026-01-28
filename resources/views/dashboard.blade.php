<x-app-layout>
    {{-- Custom Styles for Glass Effects --}}
    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            transform: translateZ(0);
        }

        .dark .glass-card {
            background: rgba(30, 41, 59, 0.75);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .health-ring {
            stroke-dasharray: 251.2;
            stroke-dashoffset: calc(251.2 - (251.2 * var(--score)) / 100);
            transition: stroke-dashoffset 1.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .bento-card {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                box-shadow 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .bento-card:hover {
            transform: translateY(-4px) translateZ(0);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        /* Hide scrollbar for insight box */
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>

    {{-- Main Dashboard Container --}}
    <div class="min-h-screen p-6 md:p-8">

        {{-- Header Greeting --}}
        <div class="mb-8">
            <h1 class="text-2xl md:text-3xl font-black text-emerald-800 dark:text-white">
                Selamat Datang, {{ explode(' ', Auth::user()->name)[0] }}! 👋
            </h1>
            <p class="text-emerald-600/70 dark:text-emerald-400/70 text-sm font-medium mt-1">
                Ringkasan keuangan bulan <span class="font-bold">{{ $currentMonth }}</span>
            </p>
        </div>

        {{--
        BENTO GRID LAYOUT (Based on 2024 Fintech Best Practices)
        - Row 1: Hero Stats (4 cols, dynamic if no budget)
        - Row 2: Financial Health (Span 2) + AI Insight (Span 1)
        - Row 3: Expense Chart (Span 1) + Recent Transactions (Span 2)
        - Row 4: Budget Progress (Span 3/Full)
        - Row 5: Quick Actions (Full)
        --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- ============================================= --}}
            {{-- ROW 1: HERO STAT CARDS (Most Important First) --}}
            {{-- ============================================= --}}
            <div
                class="lg:col-span-3 grid {{ $totalBudgetAllocation > 0 ? 'grid-cols-2 md:grid-cols-4' : 'grid-cols-1 md:grid-cols-3' }} gap-4">

                {{-- BALANCE (Primary KPI - Largest Visual Weight) --}}
                <div
                    class="glass-card bento-card rounded-2xl p-6 relative overflow-hidden group {{ $totalBudgetAllocation > 0 ? '' : 'md:col-span-1' }}">
                    <div
                        class="absolute -top-4 -right-4 w-24 h-24 bg-blue-500/10 rounded-full blur-2xl group-hover:bg-blue-500/20 transition-colors">
                    </div>
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex flex-col">
                            <p
                                class="text-xs font-black text-blue-600 dark:text-blue-400 uppercase tracking-widest mb-1">
                                Balance</p>
                            <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">Net savings bulan ini
                            </p>
                        </div>
                        <div
                            class="w-10 h-10 bg-blue-100 dark:bg-blue-500/20 rounded-xl flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 11-6 0H5.25A2.25 2.25 0 003 12m18 0v6a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 9m18 0V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v3" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-2xl md:text-3xl font-black text-slate-800 dark:text-white mt-2">
                        {{ Auth::user()->formatCurrency($netSavings) }}
                    </p>
                </div>

                {{-- INCOME --}}
                <div class="glass-card bento-card rounded-2xl p-6 relative overflow-hidden group">
                    <div
                        class="absolute -top-4 -right-4 w-24 h-24 bg-emerald-500/10 rounded-full blur-2xl group-hover:bg-emerald-500/20 transition-colors">
                    </div>
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex flex-col">
                            <p
                                class="text-xs font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest mb-1">
                                Income</p>
                            <p class="text-[10px] text-emerald-600/60 dark:text-emerald-400/60 font-medium">Total
                                pemasukan</p>
                        </div>
                        <div
                            class="w-10 h-10 bg-emerald-100 dark:bg-emerald-500/20 rounded-xl flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19V5m0 0l-7 7m7-7l7 7" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-2xl md:text-3xl font-black text-slate-800 dark:text-white mt-2">
                        {{ Auth::user()->formatCurrency($totalIncome) }}
                    </p>
                </div>

                {{-- EXPENSE --}}
                <div class="glass-card bento-card rounded-2xl p-6 relative overflow-hidden group">
                    <div
                        class="absolute -top-4 -right-4 w-24 h-24 bg-rose-500/10 rounded-full blur-2xl group-hover:bg-rose-500/20 transition-colors">
                    </div>
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex flex-col">
                            <p
                                class="text-xs font-black text-rose-600 dark:text-rose-400 uppercase tracking-widest mb-1">
                                Expense</p>
                            <p class="text-[10px] text-rose-600/60 dark:text-rose-400/60 font-medium">Total pengeluaran
                            </p>
                        </div>
                        <div
                            class="w-10 h-10 bg-rose-100 dark:bg-rose-500/20 rounded-xl flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m0 0l7-7m-7 7l-7-7" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-2xl md:text-3xl font-black text-slate-800 dark:text-white mt-2">
                        {{ Auth::user()->formatCurrency($totalExpense) }}
                    </p>
                </div>

                {{-- BUDGET (Conditional - Only shows if user has budget) --}}
                @if($totalBudgetAllocation > 0)
                    <div class="glass-card bento-card rounded-2xl p-6 relative overflow-hidden group">
                        <div
                            class="absolute -top-4 -right-4 w-24 h-24 bg-amber-500/10 rounded-full blur-2xl group-hover:bg-amber-500/20 transition-colors">
                        </div>
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex flex-col">
                                <p
                                    class="text-xs font-black text-amber-600 dark:text-amber-400 uppercase tracking-widest mb-1">
                                    Budget</p>
                                <p class="text-[10px] text-amber-600/60 dark:text-amber-400/60 font-medium">Terpakai</p>
                            </div>
                            <div
                                class="w-10 h-10 bg-amber-100 dark:bg-amber-500/20 rounded-xl flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-2">
                            <p class="text-2xl md:text-3xl font-black text-slate-800 dark:text-white">
                                {{ $totalBudgetPercentage }}%
                            </p>
                            <div class="w-full bg-slate-200 dark:bg-slate-700 h-1.5 rounded-full mt-2 overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-700 {{ $totalBudgetPercentage > 100 ? 'bg-rose-500' : ($totalBudgetPercentage > 80 ? 'bg-amber-500' : 'bg-emerald-500') }}"
                                    style="width: {{ min($totalBudgetPercentage, 100) }}%"></div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- ============================================= --}}
            {{-- ROW 2: FINANCIAL HEALTH (Span 2) + AI (Span 1) --}}
            {{-- ============================================= --}}

            {{-- Financial Health Score --}}
            <div
                class="lg:col-span-2 glass-card bento-card rounded-[2rem] p-8 relative overflow-hidden flex flex-col justify-center">
                <div class="flex flex-col md:flex-row items-center justify-center gap-8 h-full">
                    {{-- Score Ring --}}
                    <div class="relative w-36 h-36 md:w-44 md:h-44 shrink-0">
                        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                            <circle cx="50" cy="50" r="40" fill="none" stroke="currentColor"
                                class="text-emerald-100 dark:text-emerald-900/50" stroke-width="8" />
                            <circle cx="50" cy="50" r="40" fill="none" class="text-emerald-500 health-ring"
                                stroke="currentColor" stroke-width="8" stroke-linecap="round"
                                style="--score: {{ $healthScore }}" />
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span
                                class="text-4xl md:text-5xl font-black text-emerald-700 dark:text-emerald-400">{{ $healthScore }}</span>
                            <span class="text-xs font-bold text-slate-400 dark:text-slate-500">/100</span>
                        </div>
                    </div>

                    {{-- Score Info --}}
                    <div class="text-center md:text-left flex-1 flex flex-col justify-center">
                        <p
                            class="text-xs font-black text-emerald-600 dark:text-emerald-500 uppercase tracking-widest mb-2">
                            Financial Health Score</p>
                        <h2 class="text-2xl md:text-3xl font-black text-slate-800 dark:text-white mb-2 leading-tight">
                            {{ $healthStatus }}
                        </h2>

                        <div class="flex flex-wrap justify-center md:justify-start gap-2 mt-4">
                            <span
                                class="px-3 py-1.5 rounded-full text-xs font-bold bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300">
                                💰 Savings:
                                {{ $totalIncome > 0 ? round((($totalIncome - $totalExpense) / $totalIncome) * 100) : 0 }}%
                            </span>
                            <span
                                class="px-3 py-1.5 rounded-full text-xs font-bold bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300">
                                📊 {{ $activeBudgets->count() }} Active Budgets
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Decorative Blob --}}
                <div
                    class="absolute -top-10 -right-10 w-40 h-40 bg-emerald-400/20 rounded-full blur-3xl pointer-events-none">
                </div>
            </div>

            {{-- AI Insight Card --}}
            <div class="glass-card bento-card rounded-[2rem] p-6 flex flex-col h-full justify-between">
                <div class="flex items-center gap-3 mb-4 shrink-0">
                    <div
                        class="w-10 h-10 bg-indigo-100 dark:bg-indigo-500/20 rounded-xl flex items-center justify-center text-xl shrink-0">
                        🤖
                    </div>
                    <div>
                        <p class="font-black text-slate-800 dark:text-white text-sm">G-Money AI</p>
                        <div class="flex items-center gap-1.5">
                            <span class="relative flex h-2 w-2">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                            </span>
                            <span
                                class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 tracking-wider">ONLINE</span>
                        </div>
                    </div>
                </div>

                <div class="flex-1 glass-card rounded-xl p-4 mb-4 overflow-y-auto hide-scrollbar max-h-48"
                    x-data="{ insight: '⏳ Menganalisis data keuangan...', loading: true }" x-init="setTimeout(() => {
                        fetch('{{ route('ai.insight') }}', { headers: { 'Accept': 'application/json' } })
                        .then(r => r.json())
                        .then(d => { insight = d.insight || d.message || 'Tidak ada insight'; loading = false })
                        .catch(e => { insight = '⚠️ Gagal memuat insight.'; loading = false })
                    }, 500)">
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed whitespace-pre-line"
                        x-text="insight"></p>
                </div>

                <button @click="$dispatch('open-ai-chat')"
                    class="w-full bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-bold py-3 px-4 rounded-xl text-xs transition-all hover:shadow-lg hover:shadow-emerald-500/25 flex items-center justify-center gap-2">
                    <span>Chat with AI</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </button>
            </div>

            {{-- ============================================= --}}
            {{-- ROW 3: EXPENSE CHART (1) + TRANSACTIONS (2) --}}
            {{-- ============================================= --}}

            {{-- Expense by Category Pie Chart --}}
            <div class="glass-card bento-card rounded-[2rem] p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xs font-black text-emerald-600 dark:text-emerald-500 uppercase tracking-widest">
                        Expense by Category</h3>
                    <span class="text-[10px] font-bold text-slate-400">{{ $currentMonth }}</span>
                </div>

                @if($expenseByCategory->count() > 0)
                    <div class="relative h-44 flex items-center justify-center mb-4">
                        <canvas id="expensePieChart"></canvas>
                    </div>
                    <div class="space-y-2">
                        @foreach($expenseByCategory->take(4) as $category => $amount)
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-medium text-slate-600 dark:text-slate-300 truncate">{{ $category }}</span>
                                <span
                                    class="font-bold text-slate-800 dark:text-white">{{ Auth::user()->formatCurrency($amount) }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="h-48 flex items-center justify-center">
                        <p class="text-slate-400 dark:text-slate-500 text-sm italic">Belum ada pengeluaran</p>
                    </div>
                @endif
            </div>

            {{-- Recent Transactions --}}
            <div class="lg:col-span-2 glass-card bento-card rounded-[2rem] p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xs font-black text-emerald-600 dark:text-emerald-500 uppercase tracking-widest">
                        Recent Transactions</h3>
                    <a href="{{ route('transactions.index') }}"
                        class="text-[10px] font-black text-emerald-500 hover:text-emerald-600 uppercase tracking-tight transition-colors">View
                        All →</a>
                </div>

                <div class="space-y-3">
                    @forelse($transactions->take(5) as $transaction)
                        <div
                            class="flex items-center justify-between p-3 rounded-xl hover:bg-white/50 dark:hover:bg-slate-800/50 transition-all duration-200 group">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-11 h-11 rounded-xl bg-white dark:bg-slate-800 flex items-center justify-center text-lg border border-slate-100 dark:border-slate-700 shadow-sm group-hover:scale-110 transition-transform">
                                    {{ $transaction->category->icon ?? '💰' }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-800 dark:text-white">
                                        {{ $transaction->description ?? 'No Description' }}
                                    </p>
                                    <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">
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
                        <p class="text-center text-slate-400 dark:text-slate-500 py-8 italic">Belum ada transaksi.</p>
                    @endforelse
                </div>
            </div>

            {{-- ============================================= --}}
            {{-- ROW 4: BUDGET PROGRESS (Full Width) --}}
            {{-- ============================================= --}}
            @if($activeBudgets->count() > 0)
                <div class="lg:col-span-3 glass-card bento-card rounded-[2rem] p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xs font-black text-emerald-600 dark:text-emerald-500 uppercase tracking-widest">
                            Budget Progress</h3>
                        <a href="{{ route('budgeting.index') }}"
                            class="text-[10px] font-black text-emerald-500 hover:text-emerald-600 uppercase tracking-tight transition-colors">Manage
                            →</a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        @foreach($activeBudgets->take(4) as $budget)
                            <div
                                class="p-4 rounded-xl bg-white/50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700">
                                <div class="flex justify-between items-center mb-2">
                                    <span
                                        class="text-sm font-bold text-slate-700 dark:text-slate-300">{{ $budget->category->name }}</span>
                                    <span
                                        class="text-xs font-black {{ $budget->percentage > 100 ? 'text-rose-500' : ($budget->percentage > 80 ? 'text-amber-500' : 'text-emerald-600 dark:text-emerald-400') }}">
                                        {{ $budget->percentage }}%
                                    </span>
                                </div>
                                <div class="h-2 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden mb-2">
                                    <div class="h-full rounded-full transition-all duration-500 {{ $budget->percentage > 100 ? 'bg-rose-500' : ($budget->percentage > 80 ? 'bg-amber-500' : 'bg-emerald-500') }}"
                                        style="width: {{ min($budget->percentage, 100) }}%"></div>
                                </div>
                                <p class="text-[10px] text-slate-500 dark:text-slate-400">
                                    {{ Auth::user()->formatCurrency($budget->realized) }} /
                                    {{ Auth::user()->formatCurrency($budget->amount) }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- ============================================= --}}
            {{-- ROW 5: QUICK ACTIONS --}}
            {{-- ============================================= --}}
            <div class="lg:col-span-3 flex flex-wrap gap-4 justify-center">
                <a href="{{ route('scan.receipt') }}"
                    class="glass-card bento-card px-6 py-4 rounded-2xl flex items-center gap-4 group">
                    <div
                        class="w-12 h-12 bg-emerald-100 dark:bg-emerald-500/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform shrink-0">
                        <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-slate-800 dark:text-white">Scan Receipt</p>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">Catat dari struk belanja
                        </p>
                    </div>
                </a>

                <a href="{{ route('transactions.create') }}"
                    class="glass-card bento-card px-6 py-4 rounded-2xl flex items-center gap-4 group">
                    <div
                        class="w-12 h-12 bg-blue-100 dark:bg-blue-500/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform shrink-0">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-slate-800 dark:text-white">Add Transaction</p>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">Catat
                            pemasukan/pengeluaran</p>
                    </div>
                </a>
            </div>

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
                                    'rgba(59, 130, 246, 0.85)',
                                    'rgba(139, 92, 246, 0.85)',
                                    'rgba(249, 115, 22, 0.85)',
                                    'rgba(236, 72, 153, 0.85)',
                                    'rgba(20, 184, 166, 0.85)',
                                    'rgba(245, 158, 11, 0.85)',
                                ],
                                borderColor: 'rgba(255, 255, 255, 0.3)',
                                borderWidth: 2,
                                hoverOffset: 8
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '60%',
                            plugins: {
                                legend: { display: false },
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