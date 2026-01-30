<x-app-layout>
    {{-- Container Utama --}}
    <div x-data="{ showForm: false, isNewCategory: false }">

        <x-slot name="header">Budget & Goals</x-slot>
        <x-slot name="subtitle">Atur budget dan target keuangan kamu</x-slot>

        {{-- HEADER --}}
        <div class="px-8 py-6 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 dark:text-white">
                    Budget & Goals
                </h1>
                <p class="text-slate-500 dark:text-slate-400 uppercase text-[10px] font-bold tracking-[0.2em]">
                    Atur budget dan target keuangan kamu
                </p>
            </div>

            {{-- ADD BUTTON --}}
            <button @click="showForm = !showForm"
                class="inline-flex items-center px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-black uppercase tracking-widest rounded-2xl shadow-lg transition-all active:scale-95">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path x-show="!showForm" stroke-width="3" stroke-linecap="round" d="M12 4v16m8-8H4" />
                    <path x-show="showForm" stroke-width="3" stroke-linecap="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span x-text="showForm ? 'Cancel' : 'Add Budget'"></span>
            </button>
        </div>

        {{-- WRAPPER --}}
        <div
            class="mx-4 md:mx-8 mb-8 p-8 bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-slate-800 rounded-[3rem] shadow-xl">

            {{-- NOTIF --}}
            @if (session('status'))
                <div
                    class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/50 text-emerald-500 rounded-2xl text-sm font-bold">
                    {{ session('status') }}
                </div>
            @endif

            {{-- NOTIF ERROR (Pesan Budget Sudah Ada) --}}
            @if (session('error_budget'))
                <div
                    class="mb-6 p-4 bg-rose-500/10 border border-rose-500/50 text-rose-500 rounded-2xl text-sm font-bold flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ session('error_budget') }}
                </div>
            @endif

            {{-- VALIDATION ERRORS --}}
            @if ($errors->any())
                <div class="mb-6 p-4 bg-rose-500/10 border border-rose-500/50 text-rose-500 rounded-2xl text-sm font-bold">
                    <ul class="list-disc ml-4">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- FORM CREATE --}}
            <div x-show="showForm" x-transition
                class="mb-12 p-8 bg-slate-50 dark:bg-slate-900/60 rounded-[2rem] border border-slate-200 dark:border-slate-800">
                <h3 class="text-lg font-black text-slate-900 dark:text-white mb-6">New Budget</h3>

                <form method="POST" action="{{ route('budgeting.store') }}"
                    class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @csrf

                    {{-- Ganti blok CATEGORY lama lo dengan ini --}}
                    <div x-data="{ isNewCategory: false }" class="flex flex-col gap-2">
                        <div class="flex justify-between items-center px-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase">Category</label>
                            <button type="button" @click="isNewCategory = !isNewCategory"
                                class="text-[9px] font-black text-emerald-500 uppercase hover:underline">
                                <span x-text="isNewCategory ? 'Choose Existing' : '+ New Category'"></span>
                            </button>
                        </div>

                        {{-- EXISTING --}}
                        <div x-show="!isNewCategory">
                            <select name="category_id" x-bind:required="!isNewCategory" class="w-full rounded-xl p-3">
                                <option value="">Select</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- NEW --}}
                        <div x-show="isNewCategory" x-transition>
                            <input type="text" name="new_category_name" x-bind:required="isNewCategory"
                                placeholder="Enter new category name..." class="w-full rounded-xl p-3">
                        </div>
                    </div>

                    {{-- AMOUNT --}}
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase ml-2">
                            Limit ({{ Auth::user()->currency }})
                        </label>
                        <input type="number" name="amount" value="{{ old('amount') }}" required
                            class="w-full bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl p-3 focus:ring-2 focus:ring-emerald-500">
                    </div>

                    {{-- PERIOD --}}
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase ml-2">Period</label>
                        <select name="period"
                            class="w-full bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl p-3 focus:ring-2 focus:ring-emerald-500">
                            <option value="monthly" {{ old('period') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                            <option value="weekly" {{ old('period') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                        </select>
                    </div>

                    <div class="md:col-span-3 flex gap-3">
                        <button type="submit"
                            class="px-6 py-3 bg-emerald-500 text-white text-xs font-black uppercase rounded-xl hover:bg-emerald-600 transition-all">
                            Add Budget
                        </button>
                        <button type="button" @click="showForm=false"
                            class="px-6 py-3 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-black uppercase rounded-xl">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>

            {{-- GRID --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                @forelse ($budgets as $budget)
                    <div x-data="{ editing: false }"
                        class="bg-slate-50 dark:bg-slate-900/40 p-8 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 group hover:border-emerald-500/50 transition-all">

                        {{-- HEADER CARD --}}
                        <div class="flex justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-black text-slate-900 dark:text-white">{{ $budget->category->name }}
                                </h3>
                                <p class="text-[10px] uppercase text-slate-400 font-bold tracking-widest">
                                    {{ ucfirst($budget->period) }}
                                </p>
                            </div>

                            <div class="flex gap-2">
                                {{-- EDIT BUTTON --}}
                                <button @click="editing = !editing"
                                    class="p-2 text-slate-400 hover:text-blue-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </button>

                                {{-- DELETE BUTTON --}}
                                <form method="POST" action="{{ route('budgeting.destroy', $budget->id) }}">
                                    @csrf @method('DELETE')
                                    <button type="submit" onclick="return confirm('Hapus budget?')"
                                        class="p-2 text-slate-400 hover:text-rose-500 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- INLINE EDIT (Muncul saat pensil diklik) --}}
                        <div x-show="editing" x-transition
                            class="mb-6 p-4 bg-white dark:bg-slate-800 rounded-2xl border-2 border-emerald-500/30">
                            <form method="POST" action="{{ route('budgeting.update', $budget->id) }}" class="flex gap-3">
                                @csrf
                                @method('PATCH')

                                <input type="number" name="amount" value="{{ (int) $budget->amount }}"
                                    class="flex-1 bg-slate-50 dark:bg-slate-900 border-none rounded-xl p-2 font-bold dark:text-white focus:ring-2 focus:ring-emerald-500"
                                    required>

                                <button type="submit"
                                    class="bg-emerald-500 text-white px-4 rounded-xl hover:bg-emerald-600 transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>
                                <button type="button" @click="editing=false"
                                    class="bg-slate-200 dark:bg-slate-700 text-slate-500 px-4 rounded-xl">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </form>

                            <p class="text-[9px] text-slate-400 mt-2 font-bold uppercase italic">
                                *Top up limit otomatis memotong saldo
                            </p>
                        </div>

                        {{-- PROGRESS --}}
                        <div class="flex justify-between items-end mb-2 text-sm font-black dark:text-white">
                            <span class="text-[11px] text-slate-400 uppercase font-bold tracking-wider">Spent</span>
                            <span>
                                {{ Auth::user()->formatCurrency($budget->realized_amount) }}
                                <span class="text-slate-400">/ {{ Auth::user()->formatCurrency($budget->amount) }}</span>
                            </span>
                        </div>

                        <div class="w-full bg-slate-200 dark:bg-slate-800 rounded-full h-2 mb-3">
                            <div class="bg-emerald-500 h-2 rounded-full transition-all duration-1000"
                                style="width: {{ min(100, $budget->percentage) }}%"></div>
                        </div>

                        <div class="flex justify-between items-center text-[11px] font-bold uppercase">
                            <span class="{{ $budget->percentage > 100 ? 'text-rose-500' : 'text-emerald-500' }}">
                                {{ $budget->percentage }}% used
                            </span>
                            <span class="text-slate-400">
                                {{ Auth::user()->formatCurrency($budget->remaining) }} remaining
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center text-slate-500 italic py-20">
                        No budget yet. Click "Add Budget" to start.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>