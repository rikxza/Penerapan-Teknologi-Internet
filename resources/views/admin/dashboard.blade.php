<x-app-layout>
    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.4);
        }

        .dark .glass-card {
            background: rgba(30, 41, 59, 0.75);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
    </style>

    <div class="min-h-screen p-6 md:p-8">
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-2xl md:text-3xl font-black text-emerald-800 dark:text-white">
                🔒 Admin Dashboard
            </h1>
            <p class="text-emerald-600/70 dark:text-emerald-400/70 text-sm font-medium mt-1">
                Monitor dan kelola sistem Moneygement
            </p>
        </div>

        {{-- Stats Grid --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="glass-card rounded-2xl p-6">
                <p class="text-xs font-black text-blue-600 dark:text-blue-400 uppercase tracking-widest mb-1">Total
                    Users</p>
                <p class="text-3xl font-black text-slate-800 dark:text-white">{{ $totalUsers }}</p>
            </div>
            <div class="glass-card rounded-2xl p-6">
                <p class="text-xs font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest mb-1">User
                    Baru (Bulan Ini)</p>
                <p class="text-3xl font-black text-slate-800 dark:text-white">{{ $newUsersThisMonth }}</p>
            </div>
            <div class="glass-card rounded-2xl p-6">
                <p class="text-xs font-black text-violet-600 dark:text-violet-400 uppercase tracking-widest mb-1">Total
                    Transaksi</p>
                <p class="text-3xl font-black text-slate-800 dark:text-white">{{ number_format($totalTransactions) }}
                </p>
            </div>
            <div class="glass-card rounded-2xl p-6">
                <p class="text-xs font-black text-amber-600 dark:text-amber-400 uppercase tracking-widest mb-1">TXN
                    Bulan Ini</p>
                <p class="text-3xl font-black text-slate-800 dark:text-white">
                    {{ number_format($transactionsThisMonth) }}</p>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <a href="{{ route('admin.users') }}"
                class="glass-card rounded-2xl p-6 flex items-center gap-4 hover:bg-white/80 dark:hover:bg-slate-800/80 transition-all group">
                <div
                    class="w-12 h-12 bg-blue-100 dark:bg-blue-500/20 rounded-xl flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                    👥
                </div>
                <div>
                    <p class="font-bold text-slate-800 dark:text-white">Kelola Users</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Lihat, edit, hapus user</p>
                </div>
            </a>
            <a href="{{ route('admin.logs') }}"
                class="glass-card rounded-2xl p-6 flex items-center gap-4 hover:bg-white/80 dark:hover:bg-slate-800/80 transition-all group">
                <div
                    class="w-12 h-12 bg-amber-100 dark:bg-amber-500/20 rounded-xl flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                    📋
                </div>
                <div>
                    <p class="font-bold text-slate-800 dark:text-white">System Logs</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Lihat error & activity logs</p>
                </div>
            </a>
            <a href="{{ route('dashboard') }}"
                class="glass-card rounded-2xl p-6 flex items-center gap-4 hover:bg-white/80 dark:hover:bg-slate-800/80 transition-all group">
                <div
                    class="w-12 h-12 bg-emerald-100 dark:bg-emerald-500/20 rounded-xl flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                    🏠
                </div>
                <div>
                    <p class="font-bold text-slate-800 dark:text-white">User Dashboard</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Kembali ke dashboard user</p>
                </div>
            </a>
        </div>

        {{-- Recent Users --}}
        <div class="glass-card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-black text-emerald-700 dark:text-emerald-400">User Terbaru</h2>
                <a href="{{ route('admin.users') }}"
                    class="text-xs font-bold text-emerald-500 hover:text-emerald-600 uppercase">View All →</a>
            </div>
            <div class="space-y-3">
                @forelse($recentUsers as $user)
                    <div
                        class="flex items-center justify-between p-3 rounded-xl hover:bg-white/50 dark:hover:bg-slate-800/50 transition-all">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-bold text-slate-800 dark:text-white">{{ $user->name }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $user->email }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-bold {{ $user->is_admin ? 'text-amber-600' : 'text-slate-500' }}">
                                {{ $user->is_admin ? 'Admin' : 'User' }}
                            </p>
                            <p class="text-xs text-slate-400">{{ $user->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-slate-400 py-4">Belum ada user.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>