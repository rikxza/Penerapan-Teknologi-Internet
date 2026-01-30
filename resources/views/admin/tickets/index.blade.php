<x-app-layout>
    <x-slot name="header">Admin Support Tickets</x-slot>

    <div class="px-4 md:px-8 py-6">
        <div
            class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead
                        class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 font-bold uppercase text-xs">
                        <tr>
                            <th class="p-4">User</th>
                            <th class="p-4">Subject</th>
                            <th class="p-4">Status</th>
                            <th class="p-4">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($tickets as $ticket)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                                <td class="p-4 font-bold text-slate-700 dark:text-slate-300">{{ $ticket->user->name }}</td>
                                <td class="p-4">
                                    <p class="font-bold text-slate-800 dark:text-white">{{ $ticket->subject }}</p>
                                    <p class="text-xs text-slate-500 truncate max-w-xs">{{ $ticket->message }}</p>
                                </td>
                                <td class="p-4">
                                    <span
                                        class="px-2 py-1 rounded text-xs font-bold uppercase {{ $ticket->status == 'open' ? 'bg-amber-100 text-amber-600' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $ticket->status }}
                                    </span>
                                </td>
                                <td class="p-4">
                                    <div x-data="{ openWait: false }">
                                        @if($ticket->status != 'closed')
                                            <form action="{{ route('admin.tickets.reply', $ticket) }}" method="POST"
                                                class="mb-2">
                                                @csrf
                                                <div class="flex gap-2">
                                                    <input type="text" name="reply" placeholder="Balas..."
                                                        class="text-xs rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 w-full"
                                                        required>
                                                    <button type="submit"
                                                        class="bg-blue-500 text-white px-3 py-1 rounded-lg text-xs hover:bg-blue-600">Reply</button>
                                                </div>
                                            </form>
                                            <form action="{{ route('admin.tickets.close', $ticket) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="text-rose-500 text-xs font-bold hover:underline">Tutup Tiket</button>
                                            </form>
                                        @else
                                            <span class="text-slate-400 text-xs italic">Closed</span>
                                        @endif

                                        @if($ticket->admin_reply)
                                            <p class="text-[10px] text-slate-400 mt-1">Replied: {{ $ticket->admin_reply }}</p>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4">
                {{ $tickets->links() }}
            </div>
        </div>
    </div>
</x-app-layout>