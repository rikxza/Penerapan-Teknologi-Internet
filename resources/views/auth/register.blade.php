<!DOCTYPE html>
<html lang="en" x-data="{ darkMode: true }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MoneyGement - Register</title>
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

                <a href="{{ route('login') }}" class="bg-slate-900 dark:bg-white/10 hover:bg-slate-800 dark:hover:bg-white/20 transition-all text-white px-6 py-2 rounded-full text-sm font-semibold">LOGIN</a>
                <a href="{{ route('register') }}" class="text-sm font-bold text-emerald-500 dark:text-emerald-400 border-b-2 border-emerald-500 dark:border-emerald-400 pb-1">REGISTER</a>
            </div>
        </header>

        <div class="flex flex-col md:flex-row p-8 gap-12">
            {{-- Banner Kiri --}}
            <div class="w-full md:w-1/2 money-gradient rounded-[35px] p-12 relative overflow-hidden min-h-[450px] flex flex-col justify-end group shadow-2xl shadow-emerald-500/20">
                <div class="relative z-10">
                    <h1 class="text-5xl font-black leading-tight text-white">Grow your<br>Wealth :)</h1>
                    <p class="mt-4 text-emerald-50 text-lg opacity-90">Kendalikan masa depan finansial Anda mulai hari ini bersama MoneyGement.</p>
                </div>
                
                <div class="absolute bottom-10 right-10 opacity-20 group-hover:translate-x-2 transition-transform duration-500">
                    <svg width="180" height="150" viewBox="0 0 150 150" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20 75H130M130 75L90 35M130 75L90 115" stroke="white" stroke-width="12" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="absolute -top-10 -left-10 w-64 h-64 bg-white/20 rounded-full blur-3xl"></div>
            </div>

            {{-- Form Register Kanan --}}
            <div class="w-full md:w-1/2 flex flex-col justify-center max-w-md mx-auto">
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-slate-900 dark:text-white">Register</h2>
                    <p class="text-slate-500 dark:text-gray-400 text-sm mt-2">Join our financial community today.</p>
                </div>

                @if ($errors->any())
                    <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-600 dark:text-red-400 text-xs shadow-lg transition-all">
                        <div class="flex items-center gap-2 mb-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="font-bold uppercase tracking-widest">Pendaftaran Bermasalah</span>
                        </div>
                        <ul class="list-disc ml-6 opacity-90">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <div class="flex gap-3 mb-8">
                    <button class="flex-1 bg-slate-50 dark:bg-white/5 hover:bg-slate-100 dark:hover:bg-white/10 border border-slate-200 dark:border-white/10 py-2.5 rounded-xl text-[10px] font-bold tracking-wider text-orange-500 transition-all uppercase">GOOGLE ACCOUNT</button>
                </div>

                <div class="relative flex py-4 items-center">
                    <div class="flex-grow border-t border-slate-200 dark:border-white/10"></div>
                    <span class="flex-shrink mx-4 text-slate-400 dark:text-gray-500 text-[10px] uppercase tracking-widest">Or register with email</span>
                    <div class="flex-grow border-t border-slate-200 dark:border-white/10"></div>
                </div>

                <form action="{{ route('register') }}" method="POST" class="space-y-5">
                    @csrf
                    
                    <div>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Full Name" 
                            class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl p-4 text-sm text-slate-900 dark:text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition-all placeholder:text-slate-400">
                    </div>

                    <div>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="Email Address" 
                            class="w-full bg-slate-50 dark:bg-white/5 border @error('email') border-red-500/50 @else border-slate-200 dark:border-white/10 @enderror rounded-xl p-4 text-sm text-slate-900 dark:text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition-all placeholder:text-slate-400">
                    </div>

                    <div class="flex gap-4">
                        <div class="w-1/2">
                            <input type="password" name="password" placeholder="Password" 
                                class="w-full bg-slate-50 dark:bg-white/5 border @error('password') border-red-500/50 @else border-slate-200 dark:border-white/10 @enderror rounded-xl p-4 text-sm text-slate-900 dark:text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition-all placeholder:text-slate-400">
                        </div>
                        <div class="w-1/2">
                            <input type="password" name="password_confirmation" placeholder="Confirm" 
                                class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl p-4 text-sm text-slate-900 dark:text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition-all placeholder:text-slate-400">
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3 py-2">
                        <input type="checkbox" id="terms" required class="w-4 h-4 rounded bg-slate-200 dark:bg-white/10 border-slate-300 dark:border-white/10 text-emerald-500 focus:ring-emerald-500">
                        <label for="terms" class="text-xs text-slate-500 dark:text-gray-400">I agree to the <span class="text-emerald-600 dark:text-emerald-400 underline cursor-pointer">Terms & Privacy Policy *</span></label>
                    </div>

                    <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white dark:text-[#0b0d17] shadow-lg shadow-emerald-500/20 transition-all py-4 rounded-full font-black text-sm uppercase tracking-widest active:scale-95">
                        Create Account
                    </button>
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
                <p class="text-[11px] text-slate-500 dark:text-gray-500 leading-relaxed">Your trusted partner in personal and business financial management. Safe, secure, and smart.</p>
            </div>
            <div class="text-[11px] space-y-2 text-slate-600 dark:text-gray-400">
                <p class="font-bold text-slate-900 dark:text-white mb-3 uppercase tracking-wider">Product</p>
                <p class="hover:text-emerald-500 cursor-pointer transition-colors">UNIKOM</p>
                <p class="hover:text-emerald-500 cursor-pointer transition-colors">About Us</p>
            </div>
            <div class="text-[11px] space-y-2 text-slate-600 dark:text-gray-400">
                <p class="font-bold text-slate-900 dark:text-white mb-3 uppercase tracking-wider">Services</p>
                <p class="hover:text-emerald-500 cursor-pointer transition-colors">Budgeting</p>
                <p class="hover:text-emerald-500 cursor-pointer transition-colors">Reports</p>
            </div>
            <div class="flex flex-col items-end justify-center">
                <p class="text-[10px] text-slate-400 dark:text-gray-600 italic font-medium">© 2026 MoneyGement Inc.</p>
            </div>
        </footer>
    </div>
</body>
</html>