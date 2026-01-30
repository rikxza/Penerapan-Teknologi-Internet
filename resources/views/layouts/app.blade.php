<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data="{ darkMode: localStorage.getItem('dark') === 'true' }"
    x-init="$watch('darkMode', val => localStorage.setItem('dark', val))" :class="{ 'dark': darkMode }">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <script>
        // Persist dark mode across all pages
        (function () {
            const isDark = localStorage.getItem('dark') === 'true';
            if (isDark) {
                document.documentElement.classList.add('dark');
                document.documentElement.style.background = 'linear-gradient(135deg, #0f172a 0%, #064e3b 50%, #065f46 100%)';
            } else {
                document.documentElement.classList.remove('dark');
                document.documentElement.style.background = 'linear-gradient(135deg, #d1fae5 0%, #a7f3d0 25%, #6ee7b7 50%, #34d399 75%, #10b981 100%)';
            }
        })();
    </script>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased transition-colors duration-300">
    <style>
        html {
            transition: background 0.5s ease;
            min-height: 100vh;
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 25%, #6ee7b7 50%, #34d399 75%, #10b981 100%);
        }

        html.dark {
            background: linear-gradient(135deg, #0f172a 0%, #064e3b 50%, #065f46 100%) !important;
        }

        .glass-header {
            background: rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .dark .glass-header {
            background: rgba(15, 23, 42, 0.5);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
    </style>
    <div class="h-screen flex overflow-hidden">

        {{-- Sidebar --}}
        @include('layouts.sidebar')

        {{-- Main Content Area - Scrollable --}}
        <div class="flex-1 flex flex-col h-screen ml-72 overflow-hidden">

            {{-- Topbar - Fixed at top --}}
            <header
                class="glass-header h-20 flex items-center justify-between px-8 shrink-0 z-20 transition-colors duration-300">
                <div>
                    <h2 class="text-lg font-bold text-emerald-800 dark:text-white leading-tight">
                        {{ $header ?? 'Dashboard' }}
                    </h2>
                    <p
                        class="text-[10px] text-emerald-600/70 dark:text-emerald-400/70 font-bold uppercase tracking-wider">
                        {{ $subtitle ?? 'Ringkasan Keuangan Anda' }}
                    </p>
                </div>

                <div class="flex items-center gap-6">
                    {{-- Tombol Toggle Dark Mode --}}
                    <button
                        @click="darkMode = !darkMode; document.documentElement.style.background = darkMode ? 'linear-gradient(135deg, #0f172a 0%, #064e3b 50%, #065f46 100%)' : 'linear-gradient(135deg, #d1fae5 0%, #a7f3d0 25%, #6ee7b7 50%, #34d399 75%, #10b981 100%)'"
                        class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/30 dark:bg-slate-700/30 text-emerald-700 dark:text-amber-400 transition-all hover:scale-105 backdrop-blur-sm">
                        <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 3v1m0 16v1m9-9h-1M4 9h-1m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                        </svg>
                        <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>

                    <div class="flex items-center gap-3 border-r pr-6 border-slate-100 dark:border-slate-700">

                        {{-- Icon Notifikasi (Bell) --}}
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                class="relative w-10 h-10 flex items-center justify-center rounded-xl hover:bg-emerald-50 dark:hover:bg-emerald-500/10 text-slate-400 hover:text-emerald-600 cursor-pointer transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                                </svg>
                                @if(Auth::user()->unreadNotifications->count() > 0)
                                    <span
                                        class="absolute top-2.5 right-2.5 w-2 h-2 bg-rose-500 rounded-full border-2 border-white dark:border-slate-800 animate-pulse"></span>
                                @endif
                            </button>

                            {{-- Dropdown --}}
                            <div x-show="open" @click.outside="open = false"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 translate-y-2"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 translate-y-2"
                                class="absolute right-0 mt-2 w-80 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-xl z-50 overflow-hidden"
                                style="display: none;">
                                <div
                                    class="p-4 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/50">
                                    <span class="text-xs font-bold text-slate-700 dark:text-white">Notifikasi</span>
                                    @if(Auth::user()->unreadNotifications->count() > 0)
                                        <form action="{{ route('notifications.markRead') }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="text-[10px] text-emerald-500 hover:underline font-bold">Tandai sudah
                                                dibaca</button>
                                        </form>
                                    @endif
                                </div>
                                <div class="max-h-64 overflow-y-auto">
                                    @forelse(Auth::user()->notifications as $notification)
                                        <div
                                            class="p-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 border-b border-slate-50 dark:border-slate-800 last:border-0 transition-colors {{ $notification->read_at ? '' : 'bg-emerald-50/30 dark:bg-emerald-500/5' }}">
                                            <div class="flex gap-3">
                                                <div class="text-lg shrink-0">{{ $notification->data['icon'] ?? '🔔' }}
                                                </div>
                                                <div>
                                                    <p class="text-xs font-bold text-slate-800 dark:text-white">
                                                        {{ $notification->data['title'] ?? 'Notification' }}</p>
                                                    <p
                                                        class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5 leading-snug">
                                                        {{ $notification->data['message'] ?? '' }}</p>
                                                    <p class="text-[9px] text-slate-400 mt-1">
                                                        {{ $notification->created_at->diffForHumans() }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="p-8 text-center">
                                            <p class="text-xs text-slate-400 italic">Tidak ada notifikasi.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- User Profile & Logout Section --}}
                    <div class="flex items-center gap-4">
                        {{-- Bungkus Avatar dan Nama dengan Link ke Profile --}}
                        <a href="{{ route('profile.edit') }}"
                            class="flex items-center gap-3 group hover:opacity-80 transition-all cursor-pointer">

                            {{-- Area Avatar Dinamis --}}
                            <div
                                class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center text-white font-black text-sm shadow-lg shadow-emerald-500/40 overflow-hidden group-hover:ring-2 ring-emerald-500 ring-offset-2 dark:ring-offset-slate-900 transition-all">
                                @if(Auth::user()->avatar_type === 'upload' && Auth::user()->avatar)
                                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}"
                                        class="w-full h-full object-cover">
                                @elseif(Auth::user()->avatar_type === 'male')
                                    <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Felix"
                                        class="w-full h-full bg-slate-200">
                                @elseif(Auth::user()->avatar_type === 'female')
                                    <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Aneka"
                                        class="w-full h-full bg-slate-200">
                                @else
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                @endif
                            </div>

                            {{-- Info Nama & Status --}}
                            <div class="text-left hidden md:block">
                                <p
                                    class="text-xs font-bold text-slate-800 dark:text-slate-200 leading-none group-hover:text-emerald-500 transition-colors">
                                    {{ Auth::user()->name }}
                                </p>
                                <p
                                    class="text-[9px] text-emerald-500 dark:text-emerald-400 font-bold mt-1 tracking-tighter uppercase">
                                    Premium Account
                                </p>
                            </div>
                        </a>

                        {{-- Logout Button --}}
                        <form method="POST" action="{{ route('logout') }}" class="ml-2">
                            @csrf
                            <button type="submit" onclick="event.preventDefault(); this.closest('form').submit();"
                                class="w-9 h-9 flex items-center justify-center rounded-xl bg-rose-50 dark:bg-rose-500/10 text-rose-500 hover:bg-rose-500 hover:text-white transition-all duration-300 group"
                                title="Logout">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            {{-- Scrollable Content Area --}}
            <main class="flex-1 overflow-y-auto transition-colors duration-300">
                {{ $slot }}
            </main>

        </div>
    </div>

    {{-- Toast Notification --}}
    @if(session('status'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-y-4"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform translate-y-4" class="fixed top-6 right-6 z-[99998] max-w-sm">
            <div
                class="bg-emerald-500 text-white px-6 py-4 rounded-2xl shadow-2xl shadow-emerald-500/30 flex items-center gap-3">
                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <p class="text-sm font-semibold">{{ session('status') }}</p>
                <button @click="show = false" class="ml-auto p-1 hover:bg-white/20 rounded-full transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    @endif

    {{-- G-Money Floating Chat Widget - FIXED POSITION --}}
    <div x-data="chatWidget()" @open-ai-chat.window="open = true" class="fixed bottom-6 right-6 z-[99999]"
        style="position: fixed !important;">

        {{-- Chat Box --}}
        <div x-show="open" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-10 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-10 scale-95"
            class="bg-white dark:bg-slate-900 w-[350px] h-[480px] rounded-[2rem] shadow-2xl border border-emerald-200/50 dark:border-slate-700 flex flex-col overflow-hidden mb-4">

            {{-- Header --}}
            <div
                class="p-4 border-b border-emerald-100 dark:border-slate-700 flex justify-between items-center bg-gradient-to-r from-emerald-500 to-teal-500">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center text-xl backdrop-blur-sm">
                        🤖
                    </div>
                    <div>
                        <p class="font-black text-sm text-white">G-Money AI</p>
                        <p class="text-[10px] text-white/80 font-medium flex items-center gap-1">
                            <span class="w-1.5 h-1.5 bg-green-300 rounded-full animate-pulse"></span> Online
                        </p>
                    </div>
                </div>
                <button @click="open = false" class="p-2 hover:bg-white/20 rounded-full transition-colors">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Messages --}}
            <div x-ref="messageContainer" class="flex-1 overflow-y-auto p-4 space-y-3 bg-slate-50 dark:bg-slate-800/50">
                <template x-for="(msg, index) in messages" :key="index">
                    <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                        <div
                            :class="msg.role === 'user' 
                                ? 'bg-emerald-500 text-white rounded-2xl rounded-tr-sm px-4 py-2.5 max-w-[85%] shadow-lg' 
                                : 'bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-2xl rounded-tl-sm px-4 py-2.5 max-w-[85%] shadow-sm border border-slate-100 dark:border-slate-600'">
                            <p class="text-sm leading-relaxed whitespace-pre-wrap" x-text="msg.text"></p>
                        </div>
                    </div>
                </template>
                <div x-show="loading" class="flex justify-start">
                    <div
                        class="bg-white dark:bg-slate-700 rounded-2xl px-4 py-3 border border-slate-100 dark:border-slate-600 shadow-sm">
                        <div class="flex gap-1">
                            <span class="w-2 h-2 bg-emerald-400 rounded-full animate-bounce"></span>
                            <span class="w-2 h-2 bg-emerald-400 rounded-full animate-bounce"
                                style="animation-delay: 0.1s"></span>
                            <span class="w-2 h-2 bg-emerald-400 rounded-full animate-bounce"
                                style="animation-delay: 0.2s"></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Input --}}
            <div class="p-4 border-t border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-900">
                <form @submit.prevent="sendMessage()" class="relative flex items-center gap-2">
                    <input x-model="inputText" type="text" placeholder="Tanya ke G-Money..." :disabled="loading"
                        class="flex-1 bg-slate-100 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500 text-slate-800 dark:text-white placeholder:text-slate-400 outline-none">
                    <button type="submit" :disabled="loading || !inputText.trim()"
                        class="bg-emerald-500 hover:bg-emerald-600 disabled:bg-slate-300 text-white p-3 rounded-xl transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>

        {{-- Floating Button --}}
        <button x-show="!open" @click="open = true" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="scale-0" x-transition:enter-end="scale-100"
            class="bg-gradient-to-tr from-emerald-500 to-teal-400 p-4 rounded-full shadow-2xl shadow-emerald-500/40 hover:scale-110 transition-transform">
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
        </button>
    </div>

    <script>
        function chatWidget() {
            return {
                open: false,
                messages: [],
                loading: false,
                inputText: '',
                init() {
                    this.messages = [{
                        role: 'bot',
                        text: 'Halo {{ explode(" ", Auth::user()->name)[0] }}! Ada yang bisa G-Money bantu soal keuanganmu hari ini?'
                    }];
                },
                async sendMessage() {
                    if (!this.inputText.trim() || this.loading) return;

                    const text = this.inputText.trim();
                    this.inputText = '';
                    this.messages.push({ role: 'user', text: text });
                    this.loading = true;

                    // Scroll to bottom
                    this.$nextTick(() => {
                        if (this.$refs.messageContainer) {
                            this.$refs.messageContainer.scrollTop = this.$refs.messageContainer.scrollHeight;
                        }
                    });

                    try {
                        const response = await fetch('{{ route("ai.chat.send") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ message: text })
                        });

                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }

                        const data = await response.json();
                        console.log('API Response:', data); // Debug log

                        // Handle different response structures
                        const botReply = data.reply || data.response || data.message || 'Maaf, tidak ada respons dari AI.';
                        this.messages.push({ role: 'bot', text: botReply });

                    } catch (error) {
                        console.error('Chat Error:', error);
                        this.messages.push({ role: 'bot', text: 'Maaf, terjadi kesalahan koneksi. Coba lagi nanti.' });
                    } finally {
                        this.loading = false;
                        this.$nextTick(() => {
                            if (this.$refs.messageContainer) {
                                this.$refs.messageContainer.scrollTop = this.$refs.messageContainer.scrollHeight;
                            }
                        });
                    }
                }
            }
        }
    </script>
</body>

</html>