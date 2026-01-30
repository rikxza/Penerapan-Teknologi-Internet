<x-app-layout>
    <x-slot name="header">Buat Tiket Baru</x-slot>
    <div class="px-4 md:px-8 py-6">
        <div
            class="max-w-2xl mx-auto bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
            <h2 class="text-xl font-bold text-slate-800 dark:text-white mb-6">Sampaikan Kendala</h2>

            <form action="{{ route('tickets.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Subjek</label>
                    <input type="text" name="subject" required
                        class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Pesan</label>
                    <textarea name="message" rows="5" required
                        class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 focus:ring-emerald-500"></textarea>
                </div>
                <div class="flex gap-4">
                    <button type="submit"
                        class="bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-2 rounded-xl font-bold transition-all">Kirim
                        Tiket</button>
                    <a href="{{ route('tickets.index') }}"
                        class="px-6 py-2 rounded-xl font-bold text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>