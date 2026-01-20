<!DOCTYPE html>
<html lang="en" x-data="{ darkMode: true }" :class="{ 'dark': darkMode }">
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
</head>
<body class="bg-slate-50 dark:bg-[#0b0d17] text-slate-900 dark:text-white font-sans min-h-screen flex flex-col items-center justify-center p-6 transition-colors duration-500">

    <div class="w-full max-w-6xl glass-card rounded-[40px] overflow-hidden shadow-2xl transition-all duration-500">
        
        <header class="flex justify-between items-center p-8">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 money-gradient rounded-lg rotate-12 flex items-center justify-center shadow-lg shadow-emerald-500/20">
                    <span class="font-bold text-white text-lg -rotate-12">$</span>
                </div>
                <span class="text-xl font-extrabold tracking-tight text-emerald-500 dark:text-emerald-400">Money<span class="text-slate-900 dark:text-white">Gement</span></span>
            </div>
            <div class="flex items-center gap-6">
                {{-- Toggle Theme Button --}}
                <button @click="darkMode = !darkMode" class="p-2 rounded-xl bg-slate-100 dark:bg-white/10 text-slate-600 dark:text-emerald-400 transition-all hover:scale-110">
                    <svg x-show="!darkMode" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                    <svg x-show="darkMode" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 9h-1m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 5a7 7 0 100 14 7 7 0 000-14z" /></svg>
                </button>

                <a href="{{ route('login') }}" class="bg-slate-900 dark:bg-white/10 hover:bg-slate-800 dark:hover:bg-white/20 transition-all text-white px-6 py-2 rounded-full text-sm font-semibold shadow-md">LOGIN</a>
                <a href="{{ route('register') }}" class="bg-slate-900 dark:bg-white/10 hover:bg-slate-800 dark:hover:bg-white/20 transition-all text-white px-6 py-2 rounded-full text-sm font-semibold shadow-md">REGISTER</a>
            </div>
        </header>

        <div class="flex flex-col md:flex-row p-8 gap-12">
            {{-- Banner Kiri --}}
            <div class="w-full md:w-1/2 money-gradient rounded-[35px] p-12 relative overflow-hidden min-h-[450px] flex flex-col justify-end group shadow-2xl shadow-emerald-500/20">
                <div class="relative z-10">
                    <h1 class="text-5xl font-black leading-tight text-white">Don't Worry,<br>We got you!</h1>
                    <p class="mt-4 text-emerald-50 text-lg opacity-90">Recover your account access and get back to managing your wealth safely.</p>
                </div>
                
                <div class="absolute bottom-10 right-10 opacity-20 group-hover:translate-x-2 transition-transform duration-500">
                    <svg width="180" height="150" viewBox="0 0 150 150" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20 75H130M130 75L90 35M130 75L90 115" stroke="white" stroke-width="12" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="absolute -top-10 -left-10 w-64 h-64 bg-white/20 rounded-full blur-3xl"></div>
            </div>

            {{-- Form Forgot Password Kanan --}}
            <div class="w-full md:w-1/2 flex flex-col justify-center max-w-md mx-auto">
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-slate-900 dark:text-white transition-colors">Forgot Password</h2>
                    <p class="text-slate-500 dark:text-gray-400 text-sm mt-2">Enter your email and we'll send a secure link to reset your password.</p>
                </div>

                @if (session('status'))
                    <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-xs shadow-lg flex items-center gap-3 animate-pulse">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-bold uppercase tracking-wider">{{ session('status') }}</span>
                    </div>
                @endif

                <form action="{{ route('password.email') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-2 ml-1">Email Address</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="name@example.com" 
                            class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-2xl p-4 text-sm text-slate-900 dark:text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition-all placeholder:text-slate-400" required autofocus>
                        @error('email')
                            <p class="text-[10px] text-red-500 dark:text-red-400 mt-2 ml-1 font-bold italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full money-gradient hover:brightness-110 text-white shadow-xl shadow-emerald-500/20 transition-all py-4 rounded-full font-black text-sm uppercase tracking-widest active:scale-95">
                        Send Reset Link
                    </button>
                    
                    <div class="text-center pt-4">
                        <a href="{{ route('login') }}" class="group inline-flex items-center gap-2 text-xs font-bold text-slate-400 dark:text-gray-500 hover:text-emerald-500 dark:hover:text-emerald-400 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back to Login
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Footer --}}
        <footer class="grid grid-cols-2 md:grid-cols-4 gap-8 p-12 mt-4 border-t border-slate-100 dark:border-white/5 bg-slate-50 dark:bg-black/20 transition-colors">
            <div class="col-span-1">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-5 h-5 money-gradient rounded flex items-center justify-center">
                        <span class="text-[10px] font-bold text-white">$</span>
                    </div>
                    <span class="font-bold text-emerald-600 dark:text-emerald-400">MoneyGement</span>
                </div>
                <p class="text-[11px] text-slate-500 dark:text-gray-500 leading-relaxed">Securing your financial recovery with enterprise-grade protection.</p>
            </div>
            <div class="text-[11px] space-y-2 text-slate-600 dark:text-gray-400">
                <p class="font-bold text-slate-900 dark:text-white mb-3 uppercase tracking-widest">Product</p>
                <p class="hover:text-emerald-500 cursor-pointer transition-colors">Security Suite</p>
                <p class="hover:text-emerald-500 cursor-pointer transition-colors">Privacy Policy</p>
            </div>
            <div class="text-[11px] space-y-2 text-slate-600 dark:text-gray-400">
                <p class="font-bold text-slate-900 dark:text-white mb-3 uppercase tracking-widest">Help</p>
                <p class="hover:text-emerald-500 cursor-pointer transition-colors">Support Portal</p>
                <p class="hover:text-emerald-500 cursor-pointer transition-colors">Contact Us</p>
            </div>
            <div class="flex flex-col items-end justify-center">
                <p class="text-[10px] text-slate-400 dark:text-gray-600 font-medium italic">© 2026 MoneyGement Inc.</p>
            </div>
        </footer>
    </div>
</body>
</html>