<x-app-layout>
    {{-- Alpine.js: showForm untuk toggle, filter untuk nyaring data --}}
    <div x-data="{ showForm: {{ session('openForm') ? 'true' : 'false' }}, filter: 'all' }">
        
        <x-slot name="header">Transaksi</x-slot>
        <x-slot name="subtitle">Kelola pemasukan dan pengeluaran kamu</x-slot>

        {{-- 1. HEADER SECTION --}}
        <div class="px-8 py-6 flex flex-row items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 dark:text-white transition-colors duration-500">Transaksi</h1>
                <p class="text-slate-500 dark:text-slate-400 uppercase text-[10px] font-bold tracking-[0.2em] transition-colors duration-500">Kelola pemasukan dan pengeluaran kamu dengan cerdas.</p>
            </div>

            <div class="flex shrink-0">
                <button @click="showForm = !showForm" 
                        class="inline-flex items-center px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-black uppercase tracking-widest rounded-2xl shadow-lg shadow-emerald-500/20 transition-all active:scale-95">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!showForm" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/>
                        <path x-show="showForm" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <span x-text="showForm ? 'Cancel' : 'Add Transaction'"></span>
                </button>
            </div>
        </div>

        {{-- 2. WADAH UTAMA --}}
        <div class="mx-4 md:mx-8 mb-8 p-6 md:p-10 bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-slate-800 rounded-[2.5rem] md:rounded-[3.5rem] shadow-xl dark:shadow-2xl transition-all duration-500">
            
            {{-- FORM NEW TRANSACTION --}}
            <div x-show="showForm" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform -translate-y-4"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 class="mb-12 p-8 bg-slate-50 dark:bg-slate-900/60 rounded-[2rem] border border-slate-200 dark:border-slate-800">
                
                <h3 class="text-lg font-black text-slate-900 dark:text-white mb-6">New Transaction</h3>
                
                <form method="post" action="{{ route('transactions.store') }}" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Type</label>
                            <select name="type" required class="w-full bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl p-3 focus:ring-2 focus:ring-emerald-500">
                                <option value="expense">Expense</option>
                                <option value="income">Income</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Amount ({{ Auth::user()->currency }})</label>
                            <input type="number" name="amount" required placeholder="0.00"
                                   class="w-full bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl p-3 focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Category</label>
                            <select name="category_id" required class="w-full bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl p-3 focus:ring-2 focus:ring-emerald-500">
                                <option value="">Select category</option>
                                @foreach ($categories as $type => $categoryList)
                                    <optgroup label="{{ ucfirst($type) }}">
                                        @foreach ($categoryList as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Date</label>
                            <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required
                                   class="w-full bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl p-3 focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>

                    {{-- INI DIA YANG KETINGGALAN: DESKRIPSI --}}
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Description</label>
                        <input type="text" 
                            name="description" 
                            required 
                            placeholder="Makan siang di warteg..."
                            class="w-full bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl p-3 focus:ring-2 focus:ring-emerald-500">
                        
                        {{-- Tambahin ini buat nampilin error kalau validasi gagal --}}
                        @error('description')
                            <p class="text-rose-500 text-[10px] font-bold mt-1 ml-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="px-6 py-3 bg-emerald-500 text-white text-xs font-black uppercase rounded-xl hover:bg-emerald-600 transition-all shadow-lg shadow-emerald-500/20">
                            Add Transaction
                        </button>
                        <button type="button" @click="showForm = false" class="px-6 py-3 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-black uppercase rounded-xl hover:bg-slate-300 transition-all">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>

            {{-- 3. FILTER & LIST --}}
            <div class="space-y-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <h3 class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Recent Transactions</h3>
                    
                    <div class="flex bg-slate-100 dark:bg-slate-900/60 p-1 rounded-xl border border-slate-200/50 dark:border-slate-800 w-fit">
                        <button @click="filter = 'all'" :class="filter === 'all' ? 'bg-white dark:bg-slate-800 text-emerald-600 shadow-sm' : 'text-slate-500'" class="px-5 py-1.5 rounded-lg text-xs font-bold transition-all">All</button>
                        <button @click="filter = 'income'" :class="filter === 'income' ? 'bg-emerald-500 text-white shadow-sm' : 'text-slate-500'" class="px-5 py-1.5 rounded-lg text-xs font-bold transition-all">Income</button>
                        <button @click="filter = 'expense'" :class="filter === 'expense' ? 'bg-rose-500 text-white shadow-sm' : 'text-slate-500'" class="px-5 py-1.5 rounded-lg text-xs font-bold transition-all">Expenses</button>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    @forelse ($transactions as $transaction)
                        <div x-show="filter === 'all' || filter === '{{ $transaction->type }}'"
                             class="group bg-slate-50 dark:bg-slate-900/40 p-6 rounded-[2rem] border border-slate-200 dark:border-slate-800 flex items-center justify-between transition-all duration-300 hover:border-emerald-500/50">
                            
                            <div class="flex items-center gap-5">
                                <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl shadow-inner
                                    {{ $transaction->type == 'income' ? 'bg-emerald-100 dark:bg-emerald-500/10 text-emerald-600' : 'bg-rose-100 dark:bg-rose-500/10 text-rose-600' }}">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $transaction->type == 'income' ? 'M7 11l5-5m0 0l5 5m-5-5v12' : 'M17 13l-5 5m0 0l-5-5m5 5V6' }}"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-base font-black text-slate-800 dark:text-white group-hover:text-emerald-500 transition-colors">
                                        {{ $transaction->description ?: ($transaction->category->name ?? 'Transaction') }}
                                    </p>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">
                                        {{ $transaction->category->name ?? 'Uncategorized' }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-6">
                                <div class="text-right">
                                    <p class="text-lg font-black {{ $transaction->type == 'income' ? 'text-emerald-500' : 'text-rose-500' }}">
                                        {{ $transaction->type == 'income' ? '+' : '-' }}{{ Auth::user()->formatCurrency($transaction->amount) }}
                                    </p>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">{{ \Carbon\Carbon::parse($transaction->transaction_date)->timezone('Asia/Jakarta')->translatedFormat('d M Y | H:i') }}</p>
                                </div>
                                <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" onsubmit="return confirm('Hapus transaksi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-slate-400 hover:text-rose-500 transition-colors opacity-0 group-hover:opacity-100">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="py-20 text-center text-slate-500 italic">Belum ada transaksi.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>