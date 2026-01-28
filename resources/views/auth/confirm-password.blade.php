<!DOCTYPE html>
<html lang="en" x-data="{ darkMode: false }" :class="{ 'dark': darkMode }">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MoneyGement - Confirm Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
    <style>
        .glass-panel {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .dark .glass-panel {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .gradient-bg {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 25%, #6ee7b7 50%, #34d399 75%, #10b981 100%);
        }

        .dark .gradient-bg {
            background: linear-gradient(135deg, #0b0d17 0%, #064e3b 50%, #065f46 100%);
        }

        .money-gradient {
            background: linear-gradient(135deg, #059669 0%, #10b981 50%, #34d399 100%);
        }

        .blob {
            border-radius: 42% 58% 70% 30% / 45% 45% 55% 55%;
            animation: morph 8s ease-in-out infinite;
        }

        @keyframes morph {

            0%,
            100% {
                border-radius: 42% 58% 70% 30% / 45% 45% 55% 55%;
            }

            50% {
                border-radius: 30% 70% 70% 30% / 30% 52% 48% 70%;
            }
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0) rotate(0deg);
            }

            50% {
                transform: translateY(-20px) rotate(5deg);
            }
        }

        .float-animation {
            animation: float 6s ease-in-out infinite;
        }
    </style>
</head>

<body class="gradient-bg min-h-screen flex flex-col transition-colors duration-500 overflow-hidden">

    {{-- Header --}}
    <header class="flex justify-between items-center p-6 md:p-8 relative z-20">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/logo.png') }}" alt="MoneyGement" class="w-10 h-10 object-contain">
            <span class="text-xl font-black tracking-tight text-emerald-700 dark:text-emerald-400">Money<span
                    class="text-slate-800 dark:text-white">Gement</span></span>
        </div>
        <button @click="darkMode = !darkMode"
            class="p-2.5 rounded-xl glass-panel text-emerald-700 dark:text-emerald-400 transition-all hover:scale-110">
            <svg x-show="!darkMode" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
            <svg x-show="darkMode" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 3v1m0 16v1m9-9h-1M4 9h-1m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 5a7 7 0 100 14 7 7 0 000-14z" />
            </svg>
        </button>
    </header>

    {{-- Main Content --}}
    <main class="flex-1 flex items-center justify-center p-6 relative">
        <div class="glass-panel w-full max-w-lg rounded-[40px] p-8 md:p-12 relative overflow-hidden shadow-2xl">

            {{-- Decorative Blobs --}}
            <div
                class="absolute -top-20 -left-20 w-64 h-64 bg-emerald-400/30 rounded-full blur-3xl mix-blend-multiply dark:mix-blend-overlay animate-pulse">
            </div>
            <div
                class="absolute -bottom-20 -right-20 w-64 h-64 bg-teal-400/30 rounded-full blur-3xl mix-blend-multiply dark:mix-blend-overlay animate-pulse delay-1000">
            </div>

            <div class="relative z-10 text-center">
                <div
                    class="w-20 h-20 bg-emerald-100 dark:bg-emerald-900/50 rounded-full flex items-center justify-center mx-auto mb-6 float-animation shadow-lg shadow-emerald-500/20">
                    <svg class="w-10 h-10 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>

                <h1 class="text-3xl font-black text-emerald-900 dark:text-white mb-4">Secure Area</h1>

                <p class="text-emerald-700/80 dark:text-emerald-300/80 mb-8 text-sm leading-relaxed">
                    {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
                </p>

                <form method="POST" action="{{ route('password.confirm') }}" class="space-y-6">
                    @csrf

                    <div class="relative text-left">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-emerald-600/50">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </span>
                        <input type="password" name="password" placeholder="Password"
                            class="w-full bg-white/50 dark:bg-white/10 border border-emerald-200/50 dark:border-white/10 rounded-2xl pl-12 pr-4 py-4 text-sm text-emerald-900 dark:text-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all placeholder:text-emerald-600/50"
                            required autocomplete="current-password">
                        @error('password')
                            <p class="text-red-500 text-xs mt-2 ml-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                        class="w-full money-gradient text-white font-black py-4 rounded-2xl text-sm uppercase tracking-wider shadow-lg shadow-emerald-500/30 transition-all hover:scale-[1.02] active:scale-[0.98]">
                        {{ __('Confirm') }}
                    </button>
                </form>
            </div>
        </div>
    </main>

    {{-- Footer --}}
    <footer class="p-6 text-center relative z-20">
        <p class="text-sm text-emerald-700/50 dark:text-emerald-400/50">© 2026 MoneyGement Inc. All rights reserved.</p>
    </footer>

</body>

</html>