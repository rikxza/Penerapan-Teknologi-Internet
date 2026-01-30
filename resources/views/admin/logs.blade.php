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

        .log-line {
            font-family: 'Fira Code', 'Courier New', monospace;
            font-size: 11px;
        }

        .log-error {
            color: #ef4444;
        }

        .log-warning {
            color: #f59e0b;
        }

        .log-info {
            color: #3b82f6;
        }
    </style>

    <div class="min-h-screen p-6 md:p-8">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl font-black text-emerald-800 dark:text-white">
                    📋 System Logs
                </h1>
                <p class="text-emerald-600/70 dark:text-emerald-400/70 text-sm font-medium mt-1">
                    Log 100 baris terakhir dari laravel.log
                </p>
            </div>
            <a href="{{ route('admin.dashboard') }}"
                class="bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 px-4 py-2 rounded-xl text-sm font-bold transition-all">
                ← Kembali
            </a>
        </div>

        {{-- Log Viewer --}}
        <div class="glass-card rounded-2xl p-6 overflow-hidden">
            <div class="overflow-x-auto max-h-[600px] overflow-y-auto bg-slate-900 rounded-xl p-4">
                @forelse($logs as $log)
                    @php
                        $class = 'text-slate-300';
                        if (str_contains($log, 'ERROR') || str_contains($log, 'error'))
                            $class = 'log-error';
                        elseif (str_contains($log, 'WARNING') || str_contains($log, 'warning'))
                            $class = 'log-warning';
                        elseif (str_contains($log, 'INFO') || str_contains($log, 'info'))
                            $class = 'log-info';
                    @endphp
                    <p class="log-line {{ $class }} py-0.5 border-b border-slate-800 break-all">{{ $log }}</p>
                @empty
                    <p class="text-slate-400 text-center py-8">Log file kosong atau tidak ditemukan.</p>
                @endforelse
            </div>
        </div>

        {{-- Legend --}}
        <div class="flex gap-4 mt-4 justify-center text-xs">
            <span class="flex items-center gap-1"><span class="w-3 h-3 bg-red-500 rounded"></span> Error</span>
            <span class="flex items-center gap-1"><span class="w-3 h-3 bg-amber-500 rounded"></span> Warning</span>
            <span class="flex items-center gap-1"><span class="w-3 h-3 bg-blue-500 rounded"></span> Info</span>
            <span class="flex items-center gap-1"><span class="w-3 h-3 bg-slate-400 rounded"></span> Other</span>
        </div>
    </div>
</x-app-layout>