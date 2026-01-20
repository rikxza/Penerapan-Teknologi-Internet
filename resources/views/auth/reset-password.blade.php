<!DOCTYPE html>
<html lang="en" x-data="{ darkMode: true }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MoneyGement - Reset Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
    <style>
        .dark .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
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
        
        {{-- Header dengan Toggle --}}
        <header class="flex justify-between items-center p-8 pb-0">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 money-gradient rounded-lg rotate-12 flex items-center justify-center">
                    <span class="font-bold text-white text-lg -rotate-12">$</span>
                </div>
                <span class="text-xl font-extrabold tracking-tight text-emerald-500 dark:text-emerald-400">Money<span class="text-slate-900 dark:text-white">Gement</span></span>
            </div>
            <button @click="darkMode = !darkMode" class="p-2 rounded-xl bg-slate-100 dark:bg-white/10 text-slate-600 dark:text-emerald-400 transition-all">
                <svg x-show="!darkMode" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                <svg x-show="darkMode" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 9h-1m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 5a7 7 0 100 14 7 7 0 000-14z" /></svg>
            </button>
        </header>

        <div class="flex flex-col md:flex-row p-8 gap-12">
            {{-- Banner Samping --}}
            <div class="w-full md:w-1/2 money-gradient rounded-[35px] p-12 relative overflow-hidden min-h-[450px] flex flex-col justify-end group shadow-2xl shadow-emerald-500/20">
                <div class="relative z-10">
                    <h1 class="text-5xl font-black leading-tight text-white">Secure Your<br>Wealth!</h1>
                    <p class="mt-4 text-emerald-50 text-lg opacity-90">Perbarui kata sandi Anda untuk menjaga keamanan aset dan data finansial Anda.</p>
                </div>
                <div class="absolute -top-10 -right-10 w-64 h-64 bg-white/20 rounded-full blur-3xl"></div>
                <div class="absolute bottom-10 right-10 opacity-20 transition-transform duration-700 group-hover:scale-110">
                    <svg width="120" height="120" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5">
                        <path d="M12 15V17M6 21H18C19.1046 21 20 20.1046 20 19V13C20 11.8954 19.1046 11 18 11H6C4.89543 11 4 11.8954 4 13V19C4 20.1046 4.89543 21 6 21ZM16 11V7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7V11H16Z" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>

            {{-- Form Reset Password --}}
            <div class="w-full md:w-1/2 flex flex-col justify-center max-w-md mx-auto">
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-slate-900 dark:text-white">Reset Password</h2>
                    <p class="text-slate-500 dark:text-gray-400 text-sm mt-2">Buat password baru yang kuat untuk akun Anda.</p>
                </div>

                <form action="{{ route('password.store') }}" method="POST" class="space-y-5">
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 dark:text-gray-500 uppercase tracking-widest mb-2 ml-1">Email Confirmation</label>
                        <input type="email" name="email" value="{{ old('email', $request->email) }}" placeholder="Email Address" 
                            class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-2xl p-4 text-sm text-slate-900 dark:text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition-all" required>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 dark:text-gray-500 uppercase tracking-widest mb-2 ml-1">New Password</label>
                        <input type="password" name="password" placeholder="••••••••" 
                            class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-2xl p-4 text-sm text-slate-900 dark:text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition-all" required>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 dark:text-gray-500 uppercase tracking-widest mb-2 ml-1">Confirm New Password</label>
                        <input type="password" name="password_confirmation" placeholder="••••••••" 
                            class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-2xl p-4 text-sm text-slate-900 dark:text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition-all" required>
                    </div>

                    <button type="submit" class="w-full money-gradient hover:brightness-110 text-white shadow-xl shadow-emerald-500/20 transition-all py-4 rounded-full font-black text-sm uppercase tracking-widest mt-4 active:scale-95">
                        Update Password
                    </button>
                </form>
            </div>
        </div>
        
        <footer class="p-8 border-t border-slate-100 dark:border-white/5 bg-slate-50 dark:bg-black/20 text-center">
            <p class="text-[10px] text-slate-400 dark:text-gray-600 font-medium">© 2026 MoneyGement Inc. • Secure Password Reset System</p>
        </footer>
    </div>
</body>
</html>