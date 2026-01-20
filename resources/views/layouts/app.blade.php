<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      x-data="{ 
        darkMode: localStorage.getItem('dark') === 'true',
        isLocked: localStorage.getItem('sidebarLocked') === 'true' 
      }" 
      x-init="
        $watch('darkMode', val => localStorage.setItem('dark', val));
        window.addEventListener('sidebar-toggle-lock', e => { isLocked = e.detail.locked });
      }"
      :class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>
        
        <script>
            if (localStorage.getItem('dark') === 'true') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-[#F8FAFC] dark:bg-slate-900 transition-colors duration-300">
        <div class="min-h-screen flex bg-[#F8FAFC] dark:bg-slate-900">
            
            {{-- Sidebar --}}
            @include('layouts.sidebar')

            {{-- Main Content Area --}}
            <div 
                :class="isLocked ? 'ml-72' : 'ml-0'" 
                class="flex-1 flex flex-col min-h-screen bg-[#F8FAFC] dark:bg-slate-900 transition-all duration-500 ease-in-out"
            > 
                
                {{-- Topbar --}}
                <header class="bg-white dark:bg-slate-800 border-b border-slate-100 dark:border-slate-700 h-20 flex items-center justify-between px-8 sticky top-0 z-20 transition-colors duration-300">
                    <div>
                        <h2 class="text-lg font-bold text-slate-800 dark:text-white leading-tight">
                            {{ $header ?? 'Dashboard' }}
                        </h2>
                        <p class="text-[10px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider">
                            {{ $subtitle ?? 'Ringkasan Keuangan Anda' }}
                        </p>
                    </div>

                    <div class="flex items-center gap-6">
                        {{-- Tombol Toggle Dark Mode --}}
                        <button @click="darkMode = !darkMode" 
                                class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-50 dark:bg-slate-700/50 text-slate-600 dark:text-amber-400 transition-all hover:ring-2 ring-emerald-500/20 shadow-sm">
                            <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 9h-1m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                            </svg>
                            <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                        </button>

                        {{-- Icons Group --}}
                        <div class="flex items-center gap-3 border-r pr-6 border-slate-100 dark:border-slate-700">
                            {{-- Icon Pesan (Envelope) - SUDAH DIPERBAIKI --}}
                            <div class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-emerald-50 dark:hover:bg-emerald-500/10 text-slate-400 hover:text-emerald-600 cursor-pointer transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                </svg>
                            </div>

                            {{-- Icon Notifikasi (Bell) - SUDAH DIPERBAIKI --}}
                            <div class="relative w-10 h-10 flex items-center justify-center rounded-xl hover:bg-emerald-50 dark:hover:bg-emerald-500/10 text-slate-400 hover:text-emerald-600 cursor-pointer transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                                </svg>
                                <span class="absolute top-2.5 right-2.5 w-2 h-2 bg-rose-500 rounded-full border-2 border-white dark:border-slate-800"></span>
                            </div>
                        </div>

                        {{-- User Profile & Logout Section --}}
                        <div class="flex items-center gap-4">
                            {{-- Bungkus Avatar dan Nama dengan Link ke Profile --}}
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 group hover:opacity-80 transition-all cursor-pointer">
                                
                                {{-- Area Avatar Dinamis --}}
                                <div class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center text-white font-black text-sm shadow-lg shadow-emerald-500/40 overflow-hidden group-hover:ring-2 ring-emerald-500 ring-offset-2 dark:ring-offset-slate-900 transition-all">
                                    @if(Auth::user()->avatar_type === 'upload' && Auth::user()->avatar)
                                        <img src="{{ asset('storage/' . Auth::user()->avatar) }}" class="w-full h-full object-cover">
                                    @elseif(Auth::user()->avatar_type === 'male')
                                        <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Felix" class="w-full h-full bg-slate-200">
                                    @elseif(Auth::user()->avatar_type === 'female')
                                        <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Aneka" class="w-full h-full bg-slate-200">
                                    @else
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    @endif
                                </div>

                                {{-- Info Nama & Status --}}
                                <div class="text-left hidden md:block">
                                    <p class="text-xs font-bold text-slate-800 dark:text-slate-200 leading-none group-hover:text-emerald-500 transition-colors">
                                        {{ Auth::user()->name }}
                                    </p>
                                    <p class="text-[9px] text-emerald-500 dark:text-emerald-400 font-bold mt-1 tracking-tighter uppercase">
                                        Premium Account
                                    </p>
                                </div>
                            </a>

                            {{-- Logout Button --}}
                            <form method="POST" action="{{ route('logout') }}" class="ml-2">
                                @csrf
                                <button type="submit" 
                                        onclick="event.preventDefault(); this.closest('form').submit();"
                                        class="w-9 h-9 flex items-center justify-center rounded-xl bg-rose-50 dark:bg-rose-500/10 text-rose-500 hover:bg-rose-500 hover:text-white transition-all duration-300 group"
                                        title="Logout">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </header>

                <main class="transition-colors duration-300">
                    {{ $slot }}
                </main>

            </div>
        </div>
    </body>
</html>