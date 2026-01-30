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
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl font-black text-emerald-800 dark:text-white">
                    👥 Kelola User
                </h1>
                <p class="text-emerald-600/70 dark:text-emerald-400/70 text-sm font-medium mt-1">
                    Total {{ $users->total() }} user terdaftar
                </p>
            </div>
            <a href="{{ route('admin.dashboard') }}"
                class="bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 px-4 py-2 rounded-xl text-sm font-bold transition-all">
                ← Kembali
            </a>
        </div>

        {{-- Flash Messages --}}
        @if(session('status'))
            <div
                class="mb-6 p-4 rounded-xl bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 font-medium">
                ✅ {{ session('status') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 p-4 rounded-xl bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300 font-medium">
                ❌ {{ session('error') }}
            </div>
        @endif

        {{-- Search --}}
        <form method="GET" class="mb-6">
            <div class="flex gap-3">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau email..."
                    class="flex-1 bg-white/80 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent dark:text-white">
                <button type="submit"
                    class="bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-3 rounded-xl font-bold text-sm transition-all">
                    🔍 Cari
                </button>
            </div>
        </form>

        {{-- Users Table --}}
        <div class="glass-card rounded-2xl overflow-hidden">
            <table class="w-full">
                <thead>
                    <tr class="bg-emerald-500 text-white">
                        <th class="text-left py-4 px-6 font-bold text-xs uppercase">User</th>
                        <th class="text-left py-4 px-6 font-bold text-xs uppercase">Email</th>
                        <th class="text-center py-4 px-6 font-bold text-xs uppercase">Transaksi</th>
                        <th class="text-center py-4 px-6 font-bold text-xs uppercase">Status</th>
                        <th class="text-center py-4 px-6 font-bold text-xs uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($users as $user)
                        <tr class="hover:bg-white/50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-full flex items-center justify-center text-white font-bold">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800 dark:text-white">{{ $user->name }}</p>
                                        <p class="text-xs text-slate-400">Bergabung {{ $user->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-sm text-slate-600 dark:text-slate-300">{{ $user->email }}</td>
                            <td class="py-4 px-6 text-center">
                                <span
                                    class="bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 px-3 py-1 rounded-full text-xs font-bold">
                                    {{ $user->transactions_count }} TXN
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span
                                    class="{{ $user->is_admin ? 'bg-amber-100 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }} px-3 py-1 rounded-full text-xs font-bold">
                                    {{ $user->is_admin ? '👑 Admin' : 'User' }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    @if($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.toggleAdmin', $user) }}"
                                            class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="bg-amber-100 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400 hover:bg-amber-200 px-3 py-1.5 rounded-lg text-xs font-bold transition-colors">
                                                {{ $user->is_admin ? '↓ Demote' : '↑ Promote' }}
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline"
                                            onsubmit="return confirm('Yakin hapus user {{ $user->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="bg-rose-100 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400 hover:bg-rose-200 px-3 py-1.5 rounded-lg text-xs font-bold transition-colors">
                                                🗑️
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-slate-400 italic">Akun Anda</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400">Tidak ada user ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $users->links() }}
        </div>
    </div>
</x-app-layout>