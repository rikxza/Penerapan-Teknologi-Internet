<x-app-layout>
    {{-- Alpine.js: filter untuk nyaring data --}}
    <div x-data="{ filter: 'all' }">

        <x-slot name="header">Transaksi</x-slot>
        <x-slot name="subtitle">Kelola pemasukan dan pengeluaran kamu</x-slot>

        {{-- 1. HEADER SECTION --}}
        <div class="px-4 md:px-8 py-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1
                        class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white transition-colors duration-500">
                        Transaksi</h1>
                    <p
                        class="text-slate-500 dark:text-slate-400 uppercase text-[10px] font-bold tracking-[0.2em] transition-colors duration-500">
                        Kelola pemasukan dan pengeluaran kamu dengan cerdas.</p>
                </div>
            </div>
        </div>

        {{-- 2. STATISTICS BAR --}}
        <div class="mx-4 md:mx-8 mb-6">
            {{-- Month Filter Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                <h3 class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">Statistik Bulanan</h3>
                <form method="GET" action="{{ route('transactions.index') }}" class="flex items-center gap-2">
                    <select name="period" onchange="this.form.submit()"
                        class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-white text-sm font-medium rounded-xl px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-transparent cursor-pointer">
                        @foreach($availableMonths as $monthOption)
                            <option
                                value="{{ $monthOption['year'] }}-{{ str_pad($monthOption['month'], 2, '0', STR_PAD_LEFT) }}"
                                {{ $selectedMonth == $monthOption['month'] && $selectedYear == $monthOption['year'] ? 'selected' : '' }}>
                                {{ $monthOption['label'] }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            {{-- Statistics Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Total Transactions --}}
                <div
                    class="bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 flex items-center gap-4 transition-all hover:shadow-lg hover:border-emerald-500/30">
                    <div class="w-12 h-12 bg-blue-500/10 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Transaksi</p>
                        <p class="text-xl font-black text-slate-900 dark:text-white">{{ $monthlyTransactionCount }}
                            <span class="text-sm font-medium text-slate-400">/ bulan ini</span></p>
                    </div>
                </div>

                {{-- Total Income --}}
                <div
                    class="bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 flex items-center gap-4 transition-all hover:shadow-lg hover:border-emerald-500/30">
                    <div class="w-12 h-12 bg-emerald-500/10 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 11l5-5m0 0l5 5m-5-5v12" />
                        </svg>
                    </div>
                    <div>
                        <p
                            class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">
                            Pemasukan</p>
                        <p class="text-xl font-black text-emerald-600 dark:text-emerald-400">
                            {{ Auth::user()->formatCurrency($monthlyIncome) }}</p>
                    </div>
                </div>

                {{-- Total Expense --}}
                <div
                    class="bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 flex items-center gap-4 transition-all hover:shadow-lg hover:border-rose-500/30">
                    <div class="w-12 h-12 bg-rose-500/10 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 13l-5 5m0 0l-5-5m5 5V6" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-rose-600 dark:text-rose-400 uppercase tracking-widest">
                            Pengeluaran</p>
                        <p class="text-xl font-black text-rose-600 dark:text-rose-400">
                            {{ Auth::user()->formatCurrency($monthlyExpense) }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. MAIN CONTENT: TWO-COLUMN LAYOUT --}}
        <div class="mx-4 md:mx-8 mb-8 grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- LEFT COLUMN: TRANSACTION LIST (2/3 width) --}}
            <div
                class="lg:col-span-2 bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-slate-800 rounded-[2rem] shadow-xl dark:shadow-2xl transition-all duration-500 overflow-hidden p-6 md:p-8">

                {{-- Filter & Header --}}
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                    <h3 class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">Riwayat Transaksi</h3>

                    <div
                        class="flex bg-slate-100 dark:bg-slate-900/60 p-1 rounded-xl border border-slate-200/50 dark:border-slate-800 w-fit">
                        <button @click="filter = 'all'"
                            :class="filter === 'all' ? 'bg-white dark:bg-slate-800 text-emerald-600 shadow-sm' : 'text-slate-500'"
                            class="px-5 py-1.5 rounded-lg text-xs font-bold transition-all">All</button>
                        <button @click="filter = 'income'"
                            :class="filter === 'income' ? 'bg-emerald-500 text-white shadow-sm' : 'text-slate-500'"
                            class="px-5 py-1.5 rounded-lg text-xs font-bold transition-all">Income</button>
                        <button @click="filter = 'expense'"
                            :class="filter === 'expense' ? 'bg-rose-500 text-white shadow-sm' : 'text-slate-500'"
                            class="px-5 py-1.5 rounded-lg text-xs font-bold transition-all">Expenses</button>
                    </div>
                </div>

                {{-- Transaction List --}}
                <div class="space-y-3">
                    @forelse ($transactions as $transaction)
                        <div x-show="filter === 'all' || filter === '{{ $transaction->type }}'"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform scale-95"
                            x-transition:enter-end="opacity-100 transform scale-100"
                            class="group bg-slate-50 dark:bg-slate-900/40 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 flex items-center justify-between transition-all duration-300 hover:border-emerald-500/50 hover:shadow-md">

                            <div class="flex items-center gap-4">
                                <div
                                    class="w-11 h-11 rounded-xl flex items-center justify-center text-lg shadow-inner shrink-0
                                        {{ $transaction->type == 'income' ? 'bg-emerald-100 dark:bg-emerald-500/10 text-emerald-600' : 'bg-rose-100 dark:bg-rose-500/10 text-rose-600' }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="{{ $transaction->type == 'income' ? 'M7 11l5-5m0 0l5 5m-5-5v12' : 'M17 13l-5 5m0 0l-5-5m5 5V6' }}" />
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p
                                        class="text-sm font-bold text-slate-800 dark:text-white group-hover:text-emerald-500 transition-colors truncate">
                                        {{ $transaction->description ?: ($transaction->category->name ?? 'Transaction') }}
                                    </p>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span
                                            class="px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider rounded-full 
                                                {{ $transaction->type == 'income' ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600' : 'bg-rose-100 dark:bg-rose-500/20 text-rose-600' }}">
                                            {{ $transaction->category->name ?? 'Uncategorized' }}
                                        </span>
                                        <span
                                            class="text-[10px] text-slate-400">{{ \Carbon\Carbon::parse($transaction->transaction_date)->timezone('Asia/Jakarta')->translatedFormat('d M Y') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <div class="text-right">
                                    <p
                                        class="text-base font-black {{ $transaction->type == 'income' ? 'text-emerald-500' : 'text-rose-500' }}">
                                        {{ $transaction->type == 'income' ? '+' : '-' }}{{ Auth::user()->formatCurrency($transaction->amount) }}
                                    </p>
                                    <p class="text-[10px] text-slate-400 font-medium">
                                        {{ \Carbon\Carbon::parse($transaction->transaction_date)->timezone('Asia/Jakarta')->format('H:i') }}
                                    </p>
                                </div>
                                <form action="{{ route('transactions.destroy', $transaction) }}" method="POST"
                                    onsubmit="return confirm('Hapus transaksi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="p-2 text-slate-300 hover:text-rose-500 transition-colors opacity-0 group-hover:opacity-100">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="py-16 text-center">
                            <div
                                class="w-16 h-16 mx-auto mb-4 bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center">
                                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <p class="text-slate-500 dark:text-slate-400 italic">Belum ada transaksi.</p>
                            <p class="text-slate-400 text-sm mt-1">Tambahkan transaksi pertamamu →</p>
                        </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                @if($transactions->hasPages())
                    <div class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-800">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </div>

            {{-- RIGHT COLUMN: ADD TRANSACTION FORM (1/3 width) --}}
            <div class="lg:col-span-1">
                <div
                    class="bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-slate-800 rounded-[2rem] shadow-xl dark:shadow-2xl p-6 lg:sticky lg:top-24">

                    <div class="flex items-center gap-3 mb-6">
                        <div
                            class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/30">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-black text-slate-900 dark:text-white">Tambah Transaksi</h3>
                    </div>

                    <form method="post" action="{{ route('transactions.store') }}" class="space-y-4">
                        @csrf

                        {{-- Type --}}
                        <div class="space-y-1.5">
                            <label
                                class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Tipe</label>
                            <select name="type" required
                                class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl p-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all">
                                <option value="expense">💸 Pengeluaran</option>
                                <option value="income">💰 Pemasukan</option>
                            </select>
                        </div>

                        {{-- Amount --}}
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Jumlah
                                ({{ Auth::user()->currency }})</label>
                            <input type="number" name="amount" required placeholder="0"
                                class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl p-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all">
                        </div>

                        {{-- Category --}}
                        <div class="space-y-1.5">
                            <label
                                class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Kategori</label>
                            <select name="category_id" required
                                class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl p-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all">
                                <option value="">Pilih kategori...</option>
                                @foreach ($categories as $type => $categoryList)
                                    <optgroup label="{{ ucfirst($type) }}">
                                        @foreach ($categoryList as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>

                        {{-- Date --}}
                        <div class="space-y-1.5">
                            <label
                                class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Tanggal</label>
                            <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}"
                                max="{{ date('Y-m-d') }}" required
                                class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl p-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all">
                        </div>

                        {{-- Description --}}
                        <div class="space-y-1.5">
                            <label
                                class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Deskripsi</label>
                            <input type="text" name="description" required placeholder="Makan siang di warteg..."
                                class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl p-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all">
                            @error('description')
                                <p class="text-rose-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Submit Button --}}
                        <button type="submit"
                            class="w-full mt-2 py-3.5 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-bold uppercase tracking-wider rounded-xl shadow-lg shadow-emerald-500/30 transition-all hover:shadow-xl hover:shadow-emerald-500/40 active:scale-[0.98]">
                            <span class="flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Simpan Transaksi
                            </span>
                        </button>
                    </form>
            </div>
        </div>
    </div>
</x-app-layout>