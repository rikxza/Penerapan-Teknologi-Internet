<!DOCTYPE html>
<html lang="en" x-data="{ darkMode: false, isLogin: true }" :class="{ 'dark': darkMode }">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MoneyGement - Login</title>
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

        /* Slide Transitions */
        .slide-container {
            transition: transform 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .slide-left {
            transform: translateX(-50%);
        }

        .slide-right {
            transform: translateX(0);
        }

        /* Form transitions */
        .form-panel {
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .decorative-panel {
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Floating Label Styles */
        .input-group {
            position: relative;
            overflow: visible;
        }

        .input-group label {
            transition: all 0.2s ease;
        }

        .input-group input:placeholder-shown+label {
            top: 50%;
            transform: translateY(-50%) scale(1);
            color: #6b7280;
            background-color: transparent;
            padding: 0;
        }

        .input-group input:focus+label,
        .input-group input:not(:placeholder-shown)+label {
            top: 0;
            transform: translateY(-50%) scale(0.85);
            background: rgba(255, 255, 255, 0.65);
            backdrop-filter: blur(4px);
            padding: 0 6px;
            border-radius: 4px;
            color: #10b981;
            font-weight: 600;
        }

        .dark .input-group input:focus+label,
        .dark .input-group input:not(:placeholder-shown)+label {
            background: rgba(15, 23, 42, 0.8);
        }
    </style>
</head>

<body class="gradient-bg h-screen flex flex-col transition-colors duration-500 overflow-hidden">

    {{-- Header --}}
    <header class="flex justify-between items-center px-6 py-4 md:px-8 md:py-5 relative z-20 shrink-0">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/logo.png') }}" alt="MoneyGement" class="w-10 h-10 object-contain">
            <span class="text-xl font-black tracking-tight text-emerald-700 dark:text-emerald-400">Money<span
                    class="text-slate-800 dark:text-white">Gement</span></span>
        </div>
        <div class="flex items-center gap-4">
            {{-- Toggle Theme --}}
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
            <button @click="isLogin = true"
                :class="isLogin ? 'money-gradient text-white shadow-lg shadow-emerald-500/30' : 'glass-panel text-emerald-700 dark:text-emerald-400'"
                class="px-5 py-2.5 rounded-full text-sm font-bold transition-all hover:scale-105">
                Login
            </button>
            <button @click="isLogin = false"
                :class="!isLogin ? 'money-gradient text-white shadow-lg shadow-emerald-500/30' : 'glass-panel text-emerald-700 dark:text-emerald-400'"
                class="px-5 py-2.5 rounded-full text-sm font-bold transition-all hover:scale-105">
                Register
            </button>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="flex-1 flex items-center justify-center px-6 py-3 md:px-8 md:py-4 relative min-h-0">
        <div class="w-full max-w-5xl relative h-full">

            {{-- Panels Container --}}
            <div class="flex gap-6 h-full">

                {{-- Left Panel (Decorative for Login, Form for Register) --}}
                <div class="w-full md:w-1/2 glass-panel rounded-[32px] p-6 relative overflow-hidden"
                    :class="!isLogin ? 'block' : 'hidden md:block'"> {{-- MOBILE FIX: Show if registering --}}

                    {{-- Decorative Blobs (shown when Login) --}}
                    <div x-show="isLogin" x-transition:enter="transition ease-out duration-500"
                        x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90"
                        class="absolute inset-0 flex items-center justify-center">
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
                    </div>

                    {{-- Register Form (shown when Register) --}}
                    <div x-show="!isLogin" x-transition:enter="transition ease-out duration-500 delay-200"
                        x-transition:enter-start="opacity-0 translate-x-[-20px]"
                        x-transition:enter-end="opacity-100 translate-x-0"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100 translate-x-0"
                        x-transition:leave-end="opacity-0 translate-x-[-20px]"
                        class="h-full flex flex-col justify-center p-4">
                        <div class="mb-6">
                            <h1 class="text-3xl md:text-4xl font-black text-emerald-800 dark:text-white leading-tight">
                                Grow your<br>Wealth :)
                            </h1>
                            <p class="mt-3 text-emerald-700/80 dark:text-emerald-300/80 text-sm">
                                Join our financial community today.
                            </p>
                        </div>

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

                        <form action="{{ route('register') }}" method="POST" class="space-y-4"
                            x-data="{ nameError: '', emailError: '', passwordError: '' }">
                            @csrf

                            {{-- Name --}}
                            <div class="input-group">
                                <input type="text" name="name" id="reg_name" required placeholder=" " autocomplete="off"
                                    class="w-full bg-white/50 dark:bg-white/10 border-2 border-emerald-100 dark:border-white/10 rounded-2xl px-4 py-3.5 text-sm text-emerald-900 dark:text-white focus:border-emerald-500 focus:ring-0 outline-none transition-all">
                                <label for="reg_name"
                                    class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 dark:text-slate-400 text-sm transition-all pointer-events-none">Full
                                    Name</label>
                            </div>

                            {{-- Email --}}
                            <div class="input-group">
                                <input type="email" name="email" id="reg_email" required placeholder=" "
                                    autocomplete="off"
                                    class="w-full bg-white/50 dark:bg-white/10 border-2 border-emerald-100 dark:border-white/10 rounded-2xl px-4 py-3.5 text-sm text-emerald-900 dark:text-white focus:border-emerald-500 focus:ring-0 outline-none transition-all">
                                <label for="reg_email"
                                    class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 dark:text-slate-400 text-sm transition-all pointer-events-none">Email
                                    Address</label>
                            </div>

                            {{-- Password --}}
                            <div class="flex gap-3">
                                <div class="w-1/2 input-group">
                                    <input type="password" name="password" id="reg_password" required placeholder=" "
                                        class="w-full bg-white/50 dark:bg-white/10 border-2 border-emerald-100 dark:border-white/10 rounded-2xl px-4 py-3.5 text-sm text-emerald-900 dark:text-white focus:border-emerald-500 focus:ring-0 outline-none transition-all">
                                    <label for="reg_password"
                                        class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 dark:text-slate-400 text-sm transition-all pointer-events-none">Password</label>
                                </div>
                                <div class="w-1/2 input-group">
                                    <input type="password" name="password_confirmation" id="reg_confirm" required
                                        placeholder=" "
                                        class="w-full bg-white/50 dark:bg-white/10 border-2 border-emerald-100 dark:border-white/10 rounded-2xl px-4 py-3.5 text-sm text-emerald-900 dark:text-white focus:border-emerald-500 focus:ring-0 outline-none transition-all">
                                    <label for="reg_confirm"
                                        class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 dark:text-slate-400 text-sm transition-all pointer-events-none">Confirm</label>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <input type="checkbox" id="terms" required
                                    class="w-4 h-4 rounded border-emerald-300 text-emerald-500 focus:ring-emerald-500">
                                <label for="terms" class="text-xs text-emerald-700/70 dark:text-emerald-300/70">
                                    I agree to the <span
                                        class="text-emerald-600 dark:text-emerald-400 underline cursor-pointer">Terms &
                                        Privacy</span>
                                </label>
                            </div>

                            <button type="submit"
                                class="w-full money-gradient text-white font-black py-4 rounded-2xl text-sm uppercase tracking-wider shadow-lg shadow-emerald-500/30 transition-all hover:scale-[1.02] active:scale-[0.98]">
                                Create Account
                            </button>
                        </form>
                        <p class="mt-4 text-center text-sm text-emerald-700/60 dark:text-emerald-300/60">
                            Already have account? <button @click="isLogin = true"
                                class="text-emerald-600 dark:text-emerald-400 font-bold hover:underline">Login</button>
                        </p>
                    </div>
                </div>

                {{-- Right Panel (Form for Login, Decorative for Register) --}}
                <div class="w-full md:w-1/2 glass-panel rounded-[32px] p-6 relative overflow-hidden"
                    :class="isLogin ? 'block' : 'hidden md:block'"> {{-- MOBILE FIX: Show if logging in --}}

                    {{-- Login Form (shown when Login) --}}
                    <div x-show="isLogin" x-transition:enter="transition ease-out duration-500 delay-200"
                        x-transition:enter-start="opacity-0 translate-x-[20px]"
                        x-transition:enter-end="opacity-100 translate-x-0"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100 translate-x-0"
                        x-transition:leave-end="opacity-0 translate-x-[20px]"
                        class="h-full flex flex-col justify-center md:p-4">
                        <div class="mb-8">
                            <h1 class="text-4xl md:text-5xl font-black text-emerald-800 dark:text-white leading-tight">
                                Welcome<br>back, Smart<br>Earner!
                            </h1>
                            <p class="mt-4 text-emerald-700/80 dark:text-emerald-300/80 text-base">
                                Manage your finances with ease.
                            </p>
                        </div>

                        {{-- Session Status --}}
                        @if (session('status'))
                            <div
                                class="mb-4 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-sm shadow-sm">
                                {{ session('status') }}
                            </div>
                        @endif

                        {{-- Login Validation Errors --}}
                        @if ($errors->any())
                            <div x-data="{ show: true }" x-show="show"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 -translate-y-2"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                class="mb-4 p-4 rounded-2xl bg-red-500/10 border border-red-500/20 text-red-600 dark:text-red-400 text-sm relative">
                                <button @click="show = false" class="absolute top-2 right-2 text-red-400 hover:text-red-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                    <ul class="list-disc list-inside space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif

                        <form action="{{ route('login') }}" method="POST" class="space-y-5"
                            x-data="{ emailError: '', passwordError: '' }">
                            @csrf

                            {{-- Email --}}
                            <div class="input-group">
                                <input type="email" name="email" id="login_email" required placeholder=" "
                                    autocomplete="email"
                                    class="w-full bg-white/50 dark:bg-white/10 border-2 border-emerald-100 dark:border-white/10 rounded-2xl px-4 py-4 text-sm text-emerald-900 dark:text-white focus:border-emerald-500 focus:ring-0 outline-none transition-all">
                                <label for="login_email"
                                    class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 dark:text-slate-400 text-sm transition-all pointer-events-none">Email</label>
                            </div>

                            {{-- Password --}}
                            <div class="input-group">
                                <input type="password" name="password" id="login_password" required placeholder=" "
                                    autocomplete="current-password"
                                    class="w-full bg-white/50 dark:bg-white/10 border-2 border-emerald-100 dark:border-white/10 rounded-2xl px-4 py-4 text-sm text-emerald-900 dark:text-white focus:border-emerald-500 focus:ring-0 outline-none transition-all">
                                <label for="login_password"
                                    class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 dark:text-slate-400 text-sm transition-all pointer-events-none">Password</label>
                            </div>

                            <div class="flex items-center justify-between">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="remember"
                                        class="w-4 h-4 rounded border-emerald-300 text-emerald-500 focus:ring-emerald-500">
                                    <span class="text-sm text-emerald-700/70 dark:text-emerald-300/70">Remember
                                        me</span>
                                </label>
                                <a href="{{ route('password.request') }}"
                                    class="text-sm text-emerald-600 dark:text-emerald-400 hover:underline font-medium">Forgot
                                    Password?</a>
                            </div>

                            <button type="submit"
                                class="w-full money-gradient text-white font-black py-4 rounded-2xl text-sm uppercase tracking-wider shadow-lg shadow-emerald-500/30 transition-all hover:scale-[1.02] active:scale-[0.98]">
                                Sign In
                            </button>
                        </form>
                        <p class="mt-6 text-center text-sm text-emerald-700/60 dark:text-emerald-300/60">
                            Don't have an account? <button @click="isLogin = false"
                                class="text-emerald-600 dark:text-emerald-400 font-bold hover:underline">Register</button>
                        </p>
                    </div>

                    {{-- Decorative Blobs (shown when Register) --}}
                    <div x-show="!isLogin" x-transition:enter="transition ease-out duration-500"
                        x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90"
                        class="absolute inset-0 flex items-center justify-center">
                        <div
                            class="blob w-48 h-48 bg-gradient-to-br from-emerald-400 to-teal-500 opacity-80 float-animation shadow-2xl">
                        </div>
                        <div
                            class="blob2 w-36 h-36 bg-gradient-to-br from-emerald-300 to-green-400 opacity-70 absolute top-20 left-16 float-animation-delay shadow-xl">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    {{-- Footer --}}
    <footer class="px-6 py-3 text-center relative z-20 shrink-0">
        <p class="text-xs text-emerald-700/50 dark:text-emerald-400/50">© 2026 MoneyGement Inc. All rights reserved.</p>
    </footer>

</body>

</html>