<x-app-layout>
    {{-- Alpine.js: State management untuk layout chat --}}
    <div x-data="{ showForm: false }">
        
        <x-slot name="header">AI Assistant</x-slot>
        <x-slot name="subtitle">Tanya G-ment tentang kondisi keuanganmu</x-slot>

        {{-- 1. HEADER SECTION --}}
        <div class="px-8 py-6">
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white transition-colors duration-500">AI Assistant</h1>
            <p class="text-slate-500 dark:text-slate-400 text-xs font-bold tracking-wide uppercase transition-colors duration-500">Tanya G-ment tentang kondisi keuanganmu</p>
        </div>

        {{-- 2. WADAH UTAMA CHAT --}}
        <div class="mx-4 md:mx-8 mb-8 bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-slate-800 rounded-[2.5rem] md:rounded-[3.5rem] shadow-xl dark:shadow-2xl transition-all duration-500 overflow-hidden flex flex-col h-[calc(100vh-220px)]">
            
            <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-900/20">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 money-gradient rounded-xl rotate-12 flex items-center justify-center shadow-xl shadow-emerald-500/40 shrink-0 transition-transform hover:rotate-0 duration-300">
                        <span class="font-black text-white text-xl -rotate-12 group-hover:rotate-0 transition-transform">$</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900 dark:text-white leading-tight">G-ment AI Assistant</h3>
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                            <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">Online</span>
                        </div>
                    </div>
                </div>
            </div>

            <div id="chat-container" class="flex-1 overflow-y-auto p-6 md:p-10 space-y-8 custom-scrollbar bg-slate-50/30 dark:bg-transparent">
                
                {{-- Welcome Message --}}
                <div class="flex flex-col items-start gap-3 max-w-[85%] md:max-w-[70%]">
                    <div class="bg-white dark:bg-slate-800/60 p-6 rounded-[2rem] rounded-tl-none shadow-sm border border-slate-100 dark:border-slate-700">
                        <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed">
                            Halo <strong>{{ Auth::user()->name }}</strong>! Saya <strong>G-ment</strong>. Saya sudah menganalisa data transaksi Anda bulan ini. Ada yang ingin Anda tanyakan atau konsultasikan?
                        </p>
                    </div>
                </div>

            </div>

            {{-- Input Area --}}
            <div class="p-6 md:p-8 bg-white dark:bg-slate-900/40 border-t border-slate-100 dark:border-slate-800">
                <div class="relative group max-w-4xl mx-auto">
                    <input type="text" id="user-input" 
                           onkeypress="if(event.key === 'Enter') sendMessage()"
                           placeholder="Ketik pesan: 'Berapa sisa saldo gue?' atau 'Analisa pengeluaran dong'..." 
                           class="w-full pl-6 pr-16 py-4 bg-slate-100 dark:bg-slate-800 border-none rounded-2xl focus:ring-2 focus:ring-emerald-500 transition-all dark:text-white placeholder:text-slate-400">
                    
                    <button onclick="sendMessage()" id="send-btn" 
                            class="absolute right-2 top-2 bottom-2 px-5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl shadow-lg shadow-emerald-500/30 transition-all flex items-center justify-center disabled:opacity-50">
                        <svg id="send-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        <svg id="loading-icon" class="hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(16, 185, 129, 0.2); border-radius: 20px; }
        
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up { animation: fadeInUp 0.3s ease-out forwards; }
    </style>

    <script>
    async function sendMessage() {
        const input = document.getElementById('user-input');
        const container = document.getElementById('chat-container');
        const btn = document.getElementById('send-btn');
        const sendIcon = document.getElementById('send-icon');
        const loadingIcon = document.getElementById('loading-icon');
        const message = input.value.trim();

        if (!message) return;

        // 1. Tampilkan pesan user ke layar
        const userHtml = `
            <div class="flex flex-col items-end gap-3 self-end ml-auto max-w-[85%] md:max-w-[70%] animate-fade-in-up">
                <div class="bg-emerald-500 text-white p-5 rounded-[2rem] rounded-tr-none shadow-lg shadow-emerald-500/20">
                    <p class="text-sm leading-relaxed">${message}</p>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', userHtml);
        
        // UI Reset
        input.value = '';
        btn.disabled = true;
        sendIcon.classList.add('hidden');
        loadingIcon.classList.remove('hidden');
        container.scrollTop = container.scrollHeight;

        try {
            const response = await fetch("{{ route('ai.chat.send') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ message: message })
            });

            const data = await response.json();

            // 2. Tampilkan jawaban G-ment ke layar
            const gmentHtml = `
                <div class="flex flex-col items-start gap-3 max-w-[85%] md:max-w-[70%] animate-fade-in-up">
                    <div class="bg-white dark:bg-slate-800/60 p-6 rounded-[2rem] rounded-tl-none shadow-sm border border-slate-100 dark:border-slate-700">
                        <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed font-black uppercase tracking-widest text-[10px] mb-1">G-ment Assistant</p>
                        <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">${data.reply.replace(/\n/g, '<br>')}</p>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', gmentHtml);

        } catch (error) {
            console.error(error);
            container.insertAdjacentHTML('beforeend', `<p class="text-red-500 text-[10px] text-center font-bold">Gagal terhubung ke G-ment AI.</p>`);
        } finally {
            btn.disabled = false;
            sendIcon.classList.remove('hidden');
            loadingIcon.classList.add('hidden');
            container.scrollTop = container.scrollHeight;
        }
    }
    </script>
</x-app-layout>