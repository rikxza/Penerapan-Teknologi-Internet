<!-- <style>
    /* Glass effect khusus Dark Mode */
    .dark .glass-card {
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    /* Card bersih untuk Light Mode */
    .glass-card {
        background: white;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }
    .money-gradient {
        background: linear-gradient(135deg, #059669 0%, #10b981 50%, #34d399 100%);
    }
</style>

{{-- Tambahkan variabel isLocked di x-data --}}
<div x-data="{
    open: false,
    isLocked: localStorage.getItem('sidebarLocked') === 'true',
    toggleLock() {
        this.isLocked = !this.isLocked;
        localStorage.setItem('sidebarLocked', this.isLocked);
    }
}" class="relative">

    {{-- Hanya trigger mouseenter jika tidak dilock --}}
    <div @mouseenter="if(!isLocked) open = true" class="fixed left-0 top-0 h-screen w-2 z-40"></div>

    <aside
        @mouseleave="if(!isLocked) open = false"
        :class="open || isLocked ? 'translate-x-0' : '-translate-x-full'"
        class="flex flex-col w-72 h-screen bg-white dark:bg-slate-900 border-r border-slate-100 dark:border-slate-800 fixed left-0 top-0 z-50 transition-transform duration-500 ease-in-out shadow-2xl shadow-emerald-500/10"
    >
       
        {{-- Tombol Lock/Pin --}}
        <button @click="toggleLock()"
                class="absolute right-4 top-8 p-2 rounded-xl transition-all duration-300 group"
                :class="isLocked ? 'bg-emerald-500 text-white' : 'text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" x-show="isLocked" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" x-show="!isLocked" />
            </svg>
        </button>

        <div class="p-8 flex items-center gap-4">
            <div class="w-10 h-10 money-gradient rounded-xl rotate-12 flex items-center justify-center shadow-xl shadow-emerald-500/40 shrink-0 transition-transform hover:rotate-0 duration-300">
                <span class="font-black text-white text-xl -rotate-12 group-hover:rotate-0 transition-transform">$</span>
            </div>
            <div>
                <span class="text-xl font-extrabold tracking-tight text-emerald-500 dark:text-emerald-400">Money<span class="text-slate-900 dark:text-white">Gement</span></span>
            </div>
        </div>

        <nav class="flex-1 px-6 mt-4 space-y-4">
           
            <a href="{{ route('dashboard') }}"
               class="flex items-center px-4 py-3 rounded-2xl transition-all duration-300 gap-4 group
                    {{ request()->routeIs('dashboard')
                        ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-bold shadow-sm'
                        : 'text-slate-500 dark:text-slate-400 hover:text-emerald-500 hover:bg-emerald-50/50 dark:hover:bg-emerald-500/5' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                </svg>
                <span class="text-sm tracking-wide">Dashboard</span>
            </a>

            <a href="{{ route('transactions.index') }}"
               class="flex items-center px-4 py-3 rounded-2xl transition-all duration-300 gap-4 group
                    {{ request()->routeIs('transactions.*')
                        ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-bold shadow-sm'
                        : 'text-slate-500 dark:text-slate-400 hover:text-emerald-500 hover:bg-emerald-50/50 dark:hover:bg-emerald-500/5' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.5 4.5L21.75 7.5" />
                </svg>
                <span class="text-sm tracking-wide">Transactions</span>
            </a>

            <a href="#" class="flex items-center px-4 py-3 rounded-2xl text-slate-500 dark:text-slate-400 hover:text-emerald-500 hover:bg-emerald-50/50 dark:hover:bg-emerald-500/5 transition-all duration-300 gap-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                </svg>
                <span class="text-sm tracking-wide">Invoices</span>
            </a>

            <a href="{{ route('budgeting.index') }}"
               class="flex items-center px-4 py-3 rounded-2xl transition-all duration-300 gap-4 group
                    {{ request()->routeIs('budgeting.*')
                        ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-bold shadow-sm'
                        : 'text-slate-500 dark:text-slate-400 hover:text-emerald-500 hover:bg-emerald-50/50 dark:hover:bg-emerald-500/5' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <circle cx="12" cy="12" r="9" />
                    <circle cx="12" cy="12" r="6" />
                    <circle cx="12" cy="12" r="3" />
                </svg>
                <span class="text-sm tracking-wide">Goals</span>
            </a>

            <a href="{{ route('ai.chat') }}" class="flex items-center px-4 py-3 rounded-2xl {{ request()->routeIs('ai.chat') ? 'text-emerald-500 bg-emerald-50/50 dark:bg-emerald-500/5' : 'text-slate-500 dark:text-slate-400' }} hover:text-emerald-500 hover:bg-emerald-50/50 dark:hover:bg-emerald-500/5 transition-all duration-300 gap-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 01.777-.332 48.29 48.29 0 005.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                </svg>
                <span class="text-sm tracking-wide">AI Chat</span>
            </a>

        </nav>

        <div class="p-8 mt-auto flex flex-col items-center">
            <div class="w-9 h-9 money-gradient rounded-xl rotate-12 flex items-center justify-center shadow-lg shadow-emerald-500/30 mb-3 transition-transform hover:rotate-0">
                <span class="font-black text-white text-lg -rotate-12">$</span>
            </div>

            <div class="flex items-center gap-1">
                <span class="text-sm font-black tracking-tighter text-emerald-500 dark:text-emerald-400 leading-none">Money</span>
                <span class="text-sm font-black tracking-tighter text-slate-800 dark:text-white leading-none">Gement</span>
            </div>

            <p class="text-[10px] text-center text-slate-400 dark:text-slate-500 font-medium leading-relaxed mt-2">
                Smart. Simple. Secure.
            </p>
        </div>

    </aside>
</div> -->



<style>
    /* Glass effect khusus Dark Mode */
    .dark .glass-card {
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    /* Card bersih untuk Light Mode */
    .glass-card {
        background: white;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }
    .money-gradient {
        background: linear-gradient(135deg, #059669 0%, #10b981 50%, #34d399 100%);
    }
</style>

{{-- Sidebar Wrapper --}}
<div x-data="{ 
    open: false, 
    isLocked: localStorage.getItem('sidebarLocked') === 'true',
    toggleLock() {
        this.isLocked = !this.isLocked;
        localStorage.setItem('sidebarLocked', this.isLocked);
        // Mengirim sinyal ke app.blade.php agar margin konten berubah otomatis
        window.dispatchEvent(new CustomEvent('sidebar-toggle-lock', { 
            detail: { locked: this.isLocked } 
        }));
    }
}" class="relative">

    {{-- Sensor area (hover) --}}
    <div @mouseenter="if(!isLocked) open = true" class="fixed left-0 top-0 h-screen w-2 z-40"></div>

    <aside 
        @mouseleave="if(!isLocked) open = false"
        :class="open || isLocked ? 'translate-x-0' : '-translate-x-full'"
        class="flex flex-col w-72 h-screen bg-white dark:bg-slate-900 border-r border-slate-100 dark:border-slate-800 fixed left-0 top-0 z-50 transition-transform duration-500 ease-in-out shadow-2xl shadow-emerald-500/10"
    >
        
        {{-- Tombol Lock/Pin --}}
        <button @click="toggleLock()" 
                class="absolute right-4 top-8 p-2 rounded-xl transition-all duration-300 group"
                :class="isLocked ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/30' : 'text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" x-show="isLocked" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" x-show="!isLocked" />
            </svg>
        </button>

        {{-- Logo Section --}}
        <div class="p-8 flex items-center gap-4">
            <div class="w-10 h-10 money-gradient rounded-xl rotate-12 flex items-center justify-center shadow-xl shadow-emerald-500/40 shrink-0 transition-transform hover:rotate-0 duration-300">
                <span class="font-black text-white text-xl -rotate-12 group-hover:rotate-0 transition-transform">$</span>
            </div>
            <div>
                <span class="text-xl font-extrabold tracking-tight text-emerald-500 dark:text-emerald-400">Money<span class="text-slate-900 dark:text-white">Gement</span></span>
            </div>
        </div>

        {{-- Navigation Links --}}
        <nav class="flex-1 px-6 mt-4 space-y-4 overflow-y-auto">
            
            <a href="{{ route('dashboard') }}" 
               class="flex items-center px-4 py-3 rounded-2xl transition-all duration-300 gap-4 group
                    {{ request()->routeIs('dashboard') 
                        ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-bold shadow-sm' 
                        : 'text-slate-500 dark:text-slate-400 hover:text-emerald-500 hover:bg-emerald-50/50 dark:hover:bg-emerald-500/5' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                </svg>
                <span class="text-sm tracking-wide">Dashboard</span>
            </a>

            <a href="{{ route('transactions.index') }}" 
               class="flex items-center px-4 py-3 rounded-2xl transition-all duration-300 gap-4 group
                    {{ request()->routeIs('transactions.*') 
                        ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-bold shadow-sm' 
                        : 'text-slate-500 dark:text-slate-400 hover:text-emerald-500 hover:bg-emerald-50/50 dark:hover:bg-emerald-500/5' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.5 4.5L21.75 7.5" />
                </svg>
                <span class="text-sm tracking-wide">Transactions</span>
            </a>


            <a href="{{ route('budgeting.index') }}" 
               class="flex items-center px-4 py-3 rounded-2xl transition-all duration-300 gap-4 group
                    {{ request()->routeIs('budgeting.*') 
                        ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-bold shadow-sm' 
                        : 'text-slate-500 dark:text-slate-400 hover:text-emerald-500 hover:bg-emerald-50/50 dark:hover:bg-emerald-500/5' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <circle cx="12" cy="12" r="9" />
                    <circle cx="12" cy="12" r="6" />
                    <circle cx="12" cy="12" r="3" />
                </svg>
                <span class="text-sm tracking-wide">Goals</span>
            </a>

            <a href="{{ route('ai.chat') }}" 
               class="flex items-center px-4 py-3 rounded-2xl transition-all duration-300 gap-4 group
                    {{ request()->routeIs('ai.chat') 
                        ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-bold shadow-sm' 
                        : 'text-slate-500 dark:text-slate-400 hover:text-emerald-500 hover:bg-emerald-50/50 dark:hover:bg-emerald-500/5' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 01.777-.332 48.29 48.29 0 005.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                </svg>
                <span class="text-sm tracking-wide">AI Chat</span>
            </a>

            <a href="{{ route('scan.receipt') }}" 
               class="flex items-center px-4 py-3 rounded-2xl transition-all duration-300 gap-4 group
                    {{ request()->routeIs('scan.receipt') 
                        ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-bold shadow-sm' 
                        : 'text-slate-500 dark:text-slate-400 hover:text-emerald-500 hover:bg-emerald-50/50 dark:hover:bg-emerald-500/5' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                </svg>
                <span class="text-sm tracking-wide">Scan Struk</span>
            </a>

            <a href="{{ route('profile.edit') }}" 
                class="flex items-center px-4 py-3 rounded-2xl transition-all duration-300 gap-4 group
                    {{ request()->routeIs('profile.edit') 
                        ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-bold shadow-sm' 
                        : 'text-slate-500 dark:text-slate-400 hover:text-emerald-500 hover:bg-emerald-50/50 dark:hover:bg-emerald-500/5' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.004.827c.422.348.53.954.26 1.43l-1.297 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="text-sm tracking-wide">Settings</span>
            </a>

        </nav>

        {{-- Sidebar Footer --}}
        <div class="p-8 mt-auto flex flex-col items-center">
            <div class="w-9 h-9 money-gradient rounded-xl rotate-12 flex items-center justify-center shadow-lg shadow-emerald-500/30 mb-3 transition-transform hover:rotate-0">
                <span class="font-black text-white text-lg -rotate-12">$</span>
            </div>
            <div class="flex items-center gap-1">
                <span class="text-sm font-black tracking-tighter text-emerald-500 dark:text-emerald-400 leading-none">Money</span>
                <span class="text-sm font-black tracking-tighter text-slate-800 dark:text-white leading-none">Gement</span>
            </div>
            <p class="text-[10px] text-center text-slate-400 dark:text-slate-500 font-medium leading-relaxed mt-2">
                Smart. Simple. Secure.
            </p>
        </div>

    </aside>
</div>