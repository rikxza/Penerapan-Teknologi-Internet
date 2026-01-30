<x-app-layout>
    <x-slot name="header">Dukungan Pengguna</x-slot>
    <x-slot name="subtitle">Hubungi admin jika ada kendala</x-slot>

    <div class="px-4 md:px-8 py-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-slate-800 dark:text-white">Tiket Saya</h2>
            <a href="{{ route('tickets.create') }}"
                class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded-xl text-sm font-bold shadow-lg shadow-emerald-500/30 transition-all">
                + Buat Tiket
            </a>
        </div>

        <div class="space-y-4">
            @forelse($tickets as $ticket)
                    <div
                        class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 class="font-bold text-lg text-slate-900 dark:text-white">{{ $ticket->subject }}</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                    {{ $ticket->created_at->translatedFormat('d F Y, H:i') }}</p>
                            </div>
                            <span
                                class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                                    {{ $ticket->status == 'open' ? 'bg-amber-100 text-amber-600' :
                ($ticket->status == 'answered' ? 'bg-blue-100 text-blue-600' : 'bg-slate-100 text-slate-600') }}">
                                {{ $ticket->status }}
                            </span>
                        </div>

                        <p
                            class="text-slate-700 dark:text-slate-300 mb-4 bg-slate-50 dark:bg-slate-800/50 p-3 rounded-xl border border-slate-100 dark:border-slate-800">
                            {{ $ticket->message }}
                        </p>

                        @if($ticket->admin_reply)
                            <div class="mt-4 pl-4 border-l-4 border-emerald-500">
                                <p class="text-xs font-bold text-emerald-600 dark:text-emerald-400 mb-1">Balasan Admin:</p>
                                <p class="text-slate-600 dark:text-slate-300 text-sm">{{ $ticket->admin_reply }}</p>
                            </div>
                        @else
                            <p class="text-xs text-slate-400 italic">Menunggu balasan admin...</p>
                        @endif
                    </div>
            @empty
                <div class="text-center py-10">
                    <p class="text-slate-500 dark:text-slate-400">Belum ada tiket.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>