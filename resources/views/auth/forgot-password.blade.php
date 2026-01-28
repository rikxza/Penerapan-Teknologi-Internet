<!DOCTYPE html>
<html lang="en" x-data="{ darkMode: false }" :class="{ 'dark': darkMode }">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MoneyGement - Forgot Password</title>
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

        .blob2 {
            border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%;
            animation: morph 10s ease-in-out infinite reverse;
        }

        .blob3 {
            border-radius: 40% 60% 60% 40% / 70% 30% 70% 30%;
            animation: morph 12s ease-in-out infinite;
        }

        @keyframes morph {

            0%,
            100% {
                border-radius: 42% 58% 70% 30% / 45% 45% 55% 55%;
            }

            25% {
                border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%;
            }

            50% {
                border-radius: 30% 70% 70% 30% / 30% 52% 48% 70%;
            }

            75% {
                border-radius: 50% 50% 30% 70% / 50% 50% 70% 30%;
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

        .float-animation-delay {
            animation: float 8s ease-in-out infinite;
            animation-delay: -2s;
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
        <div class="flex items-center gap-4">
            <button @click="darkMode = !darkMode"
                class="p-2.5 rounded-xl glass-panel text-emerald-700 dark:text-emerald-400 transition-all hover:scale-110">
                <svg x-show="!darkMode" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
                <svg x-show="darkMode" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 3v1m0 16v1m9-9h-1M4 9h-1m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 5a7 7 0 100 14 7 7 0 000-14z" />
                </svg>
            </button>
            <a href="{{ route('login') }}"
                class="glass-panel text-emerald-700 dark:text-emerald-400 px-5 py-2.5 rounded-full text-sm font-bold transition-all hover:scale-105">
                Login
            </a>
            <a href="{{ route('register') }}"
                class="glass-panel text-emerald-700 dark:text-emerald-400 px-5 py-2.5 rounded-full text-sm font-bold transition-all hover:scale-105">
                Register
            </a>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="flex-1 flex items-center justify-center p-6 md:p-8 relative">
        <div class="w-full max-w-5xl relative">
            <div class="flex gap-6">

                {{-- Left Panel (Decorative) --}}
                <div
                    class="w-1/2 glass-panel rounded-[32px] p-8 relative overflow-hidden min-h-[500px] hidden md:block">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div
                            class="blob w-48 h-48 bg-gradient-to-br from-emerald-400 to-teal-500 opacity-80 float-animation shadow-2xl">
                        </div>
                        <div
                            class="blob2 w-36 h-36 bg-gradient-to-br from-emerald-300 to-green-400 opacity-70 absolute top-20 right-16 float-animation-delay shadow-xl">
                        </div>
                        <div
                            class="blob3 w-28 h-28 bg-gradient-to-br from-teal-400 to-cyan-500 opacity-60 absolute bottom-24 left-16 float-animation shadow-lg">
                        </div>
                        <div
                            class="blob w-20 h-20 bg-gradient-to-br from-emerald-500 to-green-600 opacity-50 absolute top-32 left-24 float-animation-delay">
                        </div>
                        <div
                            class="absolute -bottom-20 -right-20 w-64 h-64 rounded-full bg-gradient-to-tr from-emerald-400/30 to-teal-300/20 blur-xl">
                        </div>
                        <div
                            class="absolute -top-10 -left-10 w-40 h-40 rounded-full bg-gradient-to-br from-green-400/20 to-emerald-300/30 blur-2xl">
                        </div>
                    </div>
                </div>

                {{-- Right Panel (Forgot Password Form) --}}
                <div class="w-full md:w-1/2 glass-panel rounded-[32px] p-8 relative overflow-hidden min-h-[500px]">
                    <div class="h-full flex flex-col justify-center md:p-4">
                        <div class="mb-8">
                            <h1 class="text-3xl md:text-4xl font-black text-emerald-800 dark:text-white leading-tight">
                                Forgot<br>Password?
                            </h1>
                            <p class="mt-4 text-emerald-700/80 dark:text-emerald-300/80 text-base">
                                Don't worry! It happens. Please enter the email associated with your account.
                            </p>
                        </div>

                        {{-- Session Status --}}
                        @if (session('status'))
                            <div
                                class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-sm shadow-sm flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ session('status') }}</span>
                            </div>
                        @endif

                        {{-- Validation Errors --}}
                        @if ($errors->any())
                            <div
                                class="mb-4 p-4 rounded-2xl bg-red-500/10 border border-red-500/20 text-red-600 dark:text-red-400 text-xs">
                                <ul class="list-disc ml-4">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('password.email') }}" method="POST" class="space-y-6">
                            @csrf
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-emerald-600/50">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </span>
                                <input type="email" name="email" value="{{ old('email') }}" placeholder="Email Address"
                                    class="w-full bg-white/50 dark:bg-white/10 border border-emerald-200/50 dark:border-white/10 rounded-2xl pl-12 pr-4 py-4 text-sm text-emerald-900 dark:text-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all placeholder:text-emerald-600/50"
                                    required autofocus>
                            </div>

                            <button type="submit"
                                class="w-full money-gradient text-white font-black py-4 rounded-2xl text-sm uppercase tracking-wider shadow-lg shadow-emerald-500/30 transition-all hover:scale-[1.02] active:scale-[0.98]">
                                Email Password Reset Link
                            </button>
                        </form>

                        <p class="mt-8 text-center text-sm text-emerald-700/60 dark:text-emerald-300/60">
                            Remember your password? <a href="{{ route('login') }}"
                                class="text-emerald-600 dark:text-emerald-400 font-bold hover:underline">Login</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    {{-- Footer --}}
    <footer class="p-6 text-center relative z-20">
        <p class="text-sm text-emerald-700/50 dark:text-emerald-400/50">© 2026 MoneyGement Inc. All rights reserved.</p>
    </footer>

</body>

</html>