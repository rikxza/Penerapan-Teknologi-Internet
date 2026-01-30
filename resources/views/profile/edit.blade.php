<x-app-layout>
    {{-- Inisialisasi Alpine.js dengan State yang sudah ditambah avatar logic --}}
    <div x-data="{ 
            showDeleteModal: false, 
            isEditing: false,
            hideEmail: $persist(true).as('user_email_hidden'),
            avatarType: '{{ Auth::user()->avatar_type ?? 'male' }}',
            previewUrl: '{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : '' }}'
        }">
        
        <x-slot name="header">Pengaturan</x-slot>
        <x-slot name="subtitle">Kelola profil dan preferensi</x-slot>

        {{-- 1. HEADER SECTION --}}
        <div class="px-8 py-6 flex flex-row items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Pengaturan</h1>
                <p class="text-slate-500 dark:text-slate-400 uppercase text-[10px] font-black tracking-[0.2em]">Konfigurasi akun dan preferensi aplikasi kamu.</p>
            </div>
        </div>

        {{-- 2. WADAH UTAMA --}}
        <div class="mx-4 md:mx-8 mb-12 p-6 md:p-10 bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-slate-800 rounded-[2.5rem] md:rounded-[3.5rem] shadow-xl transition-all duration-500">
            
            <div class="max-w-4xl mx-auto space-y-12">
                
                {{-- SECTION: PROFIL (DENGAN UPDATE AVATAR) --}}
                <div class="space-y-4">
                    <form id="profile-form" method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('patch')

                        {{-- Hidden Input untuk kirim tipe avatar ke controller --}}
                        <input type="hidden" name="avatar_type" :value="avatarType">

                        <div class="p-8 bg-slate-50 dark:bg-slate-900/60 rounded-[2.5rem] border border-slate-200 dark:border-slate-800">
                            <div class="flex flex-col md:flex-row items-center gap-8 mb-10">
                                
                                {{-- KIRI: DISPLAY AVATAR (DENGAN ICON KAMERA) --}}
                                <div class="relative group">
                                    <div class="w-28 h-28 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-[2.5rem] flex items-center justify-center text-white text-4xl font-black shadow-xl shadow-emerald-500/20 overflow-hidden border-4 border-white dark:border-slate-800">
                                        {{-- Tampilan Upload --}}
                                        <template x-if="avatarType === 'upload'">
                                            <img :src="previewUrl" class="w-full h-full object-cover">
                                        </template>
                                        {{-- Tampilan Default Male --}}
                                        <template x-if="avatarType === 'male'">
                                            <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Felix" class="w-full h-full bg-slate-200">
                                        </template>
                                        {{-- Tampilan Default Female --}}
                                        <template x-if="avatarType === 'female'">
                                            <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Aneka" class="w-full h-full bg-slate-200">
                                        </template>
                                    </div>
                                    
                                    {{-- ICON KAMERA FLOATING (Hanya muncul saat Edit) --}}
                                    <label x-show="isEditing" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="scale-0" x-transition:enter-end="scale-100"
                                           class="absolute -bottom-1 -right-1 w-9 h-9 bg-emerald-500 text-white rounded-xl flex items-center justify-center cursor-pointer shadow-lg border-2 border-white dark:border-slate-800 hover:bg-emerald-600 transition-all z-10 active:scale-90">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <input type="file" name="avatar" class="hidden" accept="image/*" @change="
                                            avatarType = 'upload';
                                            const file = $event.target.files[0];
                                            if (file) previewUrl = URL.createObjectURL(file);
                                        ">
                                    </label>
                                </div>
                                
                                {{-- KANAN: INFO & SELECTOR --}}
                                <div class="text-center md:text-left flex-1 w-full">
                                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                        <div>
                                            <h4 class="text-2xl font-black text-slate-900 dark:text-white">{{ Auth::user()->name }}</h4>
                                            
                                            @php
                                                $email = Auth::user()->email;
                                                $parts = explode('@', $email);
                                                $namePart = $parts[0];
                                                $domainPart = $parts[1];
                                                $maskedEmail = substr($namePart, 0, 3) . str_repeat('*', max(0, strlen($namePart) - 5)) . substr($namePart, -2) . '@' . $domainPart;
                                            @endphp
                                            
                                            <p class="text-slate-500 dark:text-slate-400 font-medium text-sm">{{ $maskedEmail }}</p>
                                        </div>

                                        <button @click="isEditing = !isEditing" type="button" 
                                                :class="isEditing ? 'bg-rose-500 text-white border-rose-600' : 'bg-emerald-500 text-white border-emerald-600'"
                                                class="flex items-center justify-center gap-2 px-5 py-2.5 border rounded-xl transition-all shadow-lg active:scale-95">
                                            <svg x-show="!isEditing" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                            <span class="text-[10px] font-black uppercase tracking-widest" x-text="isEditing ? 'Batal' : 'Edit Profil'"></span>
                                        </button>
                                    </div>

                                    {{-- SELECTOR AVATAR DEFAULT (Muncul saat Edit) --}}
                                    <div x-show="isEditing" x-cloak x-transition class="mt-6 flex flex-wrap items-center gap-4 p-4 bg-white dark:bg-slate-800/50 rounded-2xl border border-dashed border-slate-300 dark:border-slate-700">
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest w-full">Opsi Avatar Cepat:</p>
                                        <button type="button" @click="avatarType = 'male'" :class="avatarType === 'male' ? 'ring-2 ring-emerald-500 border-emerald-500' : 'border-slate-200'" class="p-1 bg-slate-100 rounded-xl border-2 transition-all">
                                            <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Felix" class="w-10 h-10">
                                        </button>
                                        <button type="button" @click="avatarType = 'female'" :class="avatarType === 'female' ? 'ring-2 ring-emerald-500 border-emerald-500' : 'border-slate-200'" class="p-1 bg-slate-100 rounded-xl border-2 transition-all">
                                            <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Aneka" class="w-10 h-10">
                                        </button>
                                        <div class="text-[9px] text-slate-400 font-medium italic leading-tight max-w-[120px]">
                                            Klik ikon di atas atau klik ikon kamera di foto profil.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Nama Lengkap</label>
                                    <template x-if="!isEditing">
                                        <div class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-2xl p-4 font-bold">
                                            {{ Auth::user()->name }}
                                        </div>
                                    </template>
                                    <template x-if="isEditing">
                                        <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" required
                                               class="w-full bg-white dark:bg-slate-800 border-2 border-emerald-500 text-slate-900 dark:text-white rounded-2xl p-4 font-bold focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all">
                                    </template>
                                </div>

                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Alamat Email</label>
                                    <div class="relative group">
                                        <input :type="hideEmail ? 'password' : 'text'" 
                                               value="{{ Auth::user()->email }}" 
                                               readonly
                                               class="w-full bg-slate-100 dark:bg-slate-800/40 border-slate-200 dark:border-slate-700 text-slate-400 dark:text-slate-500 rounded-2xl p-4 font-bold cursor-not-allowed outline-none tracking-wider">
                                        <button @click="hideEmail = !hideEmail" type="button" class="absolute right-4 top-1/2 -translate-y-1/2 p-2 text-slate-400 hover:text-emerald-500">
                                            <svg x-show="hideEmail" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.025 10.025 0 014.132-5.411m0 0L21 21m-2.102-2.102L12 12m4.817-4.817A3 3 0 0112 15c-1.657 0-3-1.343-3-3a3 3 0 01.183-1.012M12 5c4.478 0 8.268-2.943 9.542 7a10.025 10.025 0 01-4.132 5.411" /></svg>
                                            <svg x-show="!hideEmail" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268-2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                        </button>
                                    </div>
                                    <p x-show="isEditing" x-cloak class="text-[10px] text-slate-400 font-bold ml-2">Email tidak dapat diubah</p>
                                </div>

                                <div x-show="isEditing" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-2">
                                    <button type="submit" class="flex items-center gap-3 px-8 py-4 bg-emerald-500 hover:bg-emerald-600 text-white rounded-2xl font-black uppercase text-xs tracking-[0.2em] shadow-lg shadow-emerald-500/30 transition-all active:scale-95">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                                        Simpan Perubahan
                                    </button>
                                    @if (session('status') === 'profile-updated')
                                        <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-[10px] font-bold text-emerald-500 mt-2 ml-2 uppercase tracking-widest">Berhasil disimpan!</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- SECTION: PREFERENSI --}}
                <div class="space-y-4">
                    <h3 class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Preferensi Aplikasi</h3>
                    <div class="p-6 bg-slate-50 dark:bg-slate-900/60 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 space-y-4">
                        <div class="bg-white dark:bg-slate-800 p-5 rounded-[1.5rem] flex items-center justify-between border border-slate-100 dark:border-slate-700/50 shadow-sm">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-emerald-500/10 text-emerald-500 rounded-2xl flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-black text-slate-700 dark:text-slate-200">Notifikasi Push</p>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">Dapatkan update pengeluaran real-time</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-slate-200 dark:bg-slate-700 rounded-full peer peer-checked:bg-emerald-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full shadow-inner"></div>
                            </label>
                        </div>

                        <div class="bg-white dark:bg-slate-800 p-5 rounded-[1.5rem] flex items-center justify-between border border-slate-100 dark:border-slate-700/50 shadow-sm">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-blue-500/10 text-blue-500 rounded-2xl flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-black text-slate-700 dark:text-slate-200">Mata Uang Utama</p>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">Pilih format mata uang aplikasi</p>
                                </div>
                            </div>
                            <div class="relative">
                                <select name="currency" class="bg-slate-50 dark:bg-slate-900 border-none rounded-xl text-[11px] font-black text-slate-600 dark:text-slate-300 py-2 pl-3 pr-8 focus:ring-2 focus:ring-emerald-500/20 appearance-none cursor-pointer uppercase tracking-widest outline-none">
                                    <option value="IDR" {{ Auth::user()->currency == 'IDR' ? 'selected' : '' }}>IDR (Rp)</option>
                                    <option value="USD" {{ Auth::user()->currency == 'USD' ? 'selected' : '' }}>USD ($)</option>
                                </select>
                                <div class="absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION: STATISTIK AKUN --}}
                <div class="space-y-4">
                    <h3 class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Statistik Akun</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="p-6 bg-emerald-50 dark:bg-emerald-500/5 rounded-[2rem] border border-emerald-100 dark:border-emerald-500/20 text-center shadow-sm">
                            <p class="text-3xl font-black text-emerald-600 dark:text-emerald-400 mb-1">{{ $totalIncome }}</p>
                            <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">Total Pemasukan</p>
                        </div>
                        <div class="p-6 bg-rose-50 dark:bg-rose-500/5 rounded-[2rem] border border-rose-100 dark:border-rose-500/20 text-center shadow-sm">
                            <p class="text-3xl font-black text-rose-600 dark:text-rose-400 mb-1">{{ $totalExpense }}</p>
                            <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">Total Pengeluaran</p>
                        </div>
                        <div class="p-6 bg-blue-50 dark:bg-blue-500/5 rounded-[2rem] border border-blue-100 dark:border-blue-500/20 text-center shadow-sm">
                            <p class="text-3xl font-black text-blue-600 dark:text-blue-400 mb-1">{{ $activeBudgets }}</p>
                            <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">Budget Aktif</p>
                        </div>
                        <div class="p-6 bg-purple-50 dark:bg-purple-500/5 rounded-[2rem] border border-purple-100 dark:border-purple-500/20 text-center shadow-sm">
                            <p class="text-3xl font-black text-purple-600 dark:text-purple-400 mb-1">{{ $activeDays }}</p>
                            <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">Hari Aktif</p>
                        </div>
                    </div>
                </div>

                {{-- SECTION: EXPORT DATA (BACKUP) --}}
                <div class="space-y-4">
                    <h3 class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Backup Data</h3>
                    <div class="bg-indigo-50 dark:bg-indigo-500/10 rounded-[2.5rem] p-8 border border-indigo-500/20 flex flex-col md:flex-row items-center justify-between gap-6">
                        <div class="text-center md:text-left">
                            <h3 class="text-xl font-black text-indigo-600 dark:text-indigo-400 mb-2">Simpan Data Kamu</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">Unduh arsip lengkap transaksi dan budget dalam format JSON.</p>
                        </div>
                        <a href="{{ route('profile.export') }}" target="_blank"
                                class="w-full md:w-auto px-8 py-4 bg-indigo-500 hover:bg-indigo-600 text-white font-black uppercase tracking-[0.2em] rounded-2xl transition-all shadow-lg shadow-indigo-500/30 active:scale-95 flex items-center justify-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Export Data
                        </a>
                    </div>
                </div>

                {{-- DANGER ZONE --}}
                <div class="bg-rose-500/5 dark:bg-rose-500/10 rounded-[2.5rem] p-8 border border-rose-500/20 text-center relative overflow-hidden group">
                    <div class="relative z-10">
                        <h3 class="text-xl font-black text-rose-500 mb-2 uppercase tracking-tighter">Zona Bahaya</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-8 font-medium">Hapus seluruh data transaksi secara permanen.</p>
                        <button @click="showDeleteModal = true" 
                                class="w-full py-4 bg-rose-500 hover:bg-rose-600 text-white font-black uppercase tracking-[0.2em] rounded-2xl transition-all shadow-lg shadow-rose-500/30 active:scale-95">
                            Reset Semua Data
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL DELETE DATA --}}
        <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-[100] overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 text-center sm:block sm:p-0">
                <div x-show="showDeleteModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" @click="showDeleteModal = false" class="fixed inset-0 bg-slate-900/80 backdrop-blur-md"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div x-show="showDeleteModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" class="inline-block px-6 py-10 overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-[#0f172a] rounded-[3rem] shadow-2xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200 dark:border-slate-800">
                    <div class="text-center">
                        <div class="flex items-center justify-center w-24 h-24 mx-auto mb-6 bg-rose-100 dark:bg-rose-500/10 rounded-[2rem] text-rose-500">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <h3 class="text-2xl font-black text-slate-900 dark:text-white">Hapus Semua Data?</h3>
                        <div class="mt-10 space-y-3">
                            <form action="{{ route('transactions.deleteAll') }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-full py-4 text-sm font-black text-white uppercase tracking-widest bg-rose-500 rounded-2xl hover:bg-rose-600 active:scale-95">Iya, Hapus Selamanya</button>
                            </form>
                            <button @click="showDeleteModal = false" type="button" class="w-full py-4 text-sm font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest bg-slate-100 dark:bg-slate-800 rounded-2xl">Batalkan Saja</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    
    <style>
        [x-cloak] { display: none !important; }
        input[type="password"] {
            letter-spacing: 0.5em;
            font-family: Arial, sans-serif;
        }
    </style>
</x-app-layout>