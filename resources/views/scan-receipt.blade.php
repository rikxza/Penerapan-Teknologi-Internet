<x-app-layout>
    {{-- Alpine.js: State management untuk layout --}}
    <div x-data="receiptScanner()" x-init="init()">

        <x-slot name="header">Scan Struk</x-slot>
        <x-slot name="subtitle">Upload struk belanja untuk input otomatis</x-slot>

        {{-- 1. HEADER SECTION --}}
        <div class="px-8 py-6">
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white transition-colors duration-500">Scan Struk</h1>
            <p
                class="text-slate-500 dark:text-slate-400 text-xs font-bold tracking-wide uppercase transition-colors duration-500">
                Upload struk belanja untuk input pengeluaran otomatis dengan AI</p>
        </div>

        {{-- 2. MAIN CONTENT --}}
        <div class="mx-4 md:mx-8 mb-8 grid grid-cols-1 lg:grid-cols-2 gap-8">

            {{-- UPLOAD AREA --}}
            <div
                class="bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-slate-800 rounded-[2rem] shadow-xl dark:shadow-2xl transition-all duration-500 overflow-hidden p-8">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-3">
                    <div
                        class="w-10 h-10 money-gradient rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/30">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    Upload Struk
                </h2>

                {{-- Dropzone --}}
                <div @dragover.prevent="isDragging = true" @dragleave.prevent="isDragging = false"
                    @drop.prevent="handleDrop($event)"
                    :class="isDragging ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/20' : 'border-slate-300 dark:border-slate-700'"
                    class="relative border-2 border-dashed rounded-2xl p-12 text-center transition-all duration-300 cursor-pointer hover:border-emerald-400"
                    @click="$refs.fileInput.click()">
                    <input type="file" x-ref="fileInput" @change="handleFileSelect($event)" accept="image/*"
                        class="hidden">

                    <template x-if="!previewUrl">
                        <div>
                            <div
                                class="w-16 h-16 mx-auto mb-4 bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center">
                                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                            </div>
                            <p class="text-slate-600 dark:text-slate-400 font-medium">Drag & drop struk di sini</p>
                            <p class="text-slate-400 dark:text-slate-500 text-sm mt-1">atau klik untuk pilih file</p>
                            <p class="text-slate-400 dark:text-slate-600 text-xs mt-4">PNG, JPG, JPEG (Max 10MB)</p>
                        </div>
                    </template>

                    <template x-if="previewUrl">
                        <div class="relative">
                            <img :src="previewUrl" alt="Preview" class="max-h-64 mx-auto rounded-xl shadow-lg">
                            <button @click.stop="clearImage()"
                                class="absolute top-2 right-2 bg-red-500 text-white p-2 rounded-full shadow-lg hover:bg-red-600 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>

                {{-- Scan Button --}}
                <button @click="analyzeReceipt()" :disabled="!selectedFile || isLoading"
                    :class="selectedFile && !isLoading ? 'money-gradient hover:shadow-xl hover:shadow-emerald-500/30' : 'bg-slate-300 dark:bg-slate-700 cursor-not-allowed'"
                    class="w-full mt-6 py-4 rounded-2xl text-white font-bold text-lg transition-all duration-300 flex items-center justify-center gap-3">
                    <template x-if="!isLoading">
                        <span class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Scan dengan AI
                        </span>
                    </template>
                    <template x-if="isLoading">
                        <span class="flex items-center gap-2">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Menganalisis...
                        </span>
                    </template>
                </button>

                {{-- Error Message --}}
                <template x-if="errorMessage">
                    <div
                        class="mt-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
                        <p class="text-red-600 dark:text-red-400 text-sm" x-text="errorMessage"></p>
                    </div>
                </template>
            </div>

            {{-- RESULT AREA --}}
            <div
                class="bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-slate-800 rounded-[2rem] shadow-xl dark:shadow-2xl transition-all duration-500 overflow-hidden p-8">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-3">
                    <div
                        class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/30">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    Hasil Scan
                </h2>

                <template x-if="!receiptData">
                    <div class="text-center py-16">
                        <div
                            class="w-20 h-20 mx-auto mb-4 bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center">
                            <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <p class="text-slate-500 dark:text-slate-400">Upload struk untuk melihat hasil scan</p>
                    </div>
                </template>

                <template x-if="receiptData">
                    <div class="space-y-5">
                        {{-- Confidence Badge --}}
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Confidence:</span>
                            <span :class="{
                                    'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400': receiptData.confidence === 'high',
                                    'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400': receiptData.confidence === 'medium',
                                    'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400': receiptData.confidence === 'low'
                                }" class="px-3 py-1 rounded-full text-xs font-bold uppercase"
                                x-text="receiptData.confidence"></span>
                        </div>

                        {{-- Merchant Name --}}
                        <div>
                            <label
                                class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">Nama
                                Toko</label>
                            <input type="text" x-model="receiptData.merchant_name"
                                class="w-full px-4 py-3 bg-slate-100 dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-emerald-500 dark:text-white">
                        </div>

                        {{-- Date --}}
                        <div>
                            <label
                                class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">Tanggal</label>
                            <input type="date" x-model="receiptData.transaction_date"
                                class="w-full px-4 py-3 bg-slate-100 dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-emerald-500 dark:text-white">
                        </div>

                        {{-- Amount --}}
                        <div>
                            <label
                                class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">Total</label>
                            <div class="relative">
                                <span
                                    class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 font-bold">Rp</span>
                                <input type="number" x-model="receiptData.total_amount"
                                    class="w-full pl-12 pr-4 py-3 bg-slate-100 dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-emerald-500 dark:text-white">
                            </div>
                        </div>

                        {{-- Category --}}
                        <div>
                            <label
                                class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">Kategori</label>
                            <select x-model="selectedCategory"
                                class="w-full px-4 py-3 bg-slate-100 dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-emerald-500 dark:text-white">
                                <option value="">Pilih Kategori</option>
                                <template x-for="cat in categories" :key="cat.id">
                                    <option :value="cat.id" x-text="cat.name"></option>
                                </template>
                            </select>
                        </div>

                        {{-- Description --}}
                        <div>
                            <label
                                class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">Deskripsi</label>
                            <input type="text" x-model="description"
                                :placeholder="'Belanja di ' + (receiptData.merchant_name || '')"
                                class="w-full px-4 py-3 bg-slate-100 dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-emerald-500 dark:text-white">
                        </div>

                        {{-- Items List (if available) --}}
                        <template x-if="receiptData.items && receiptData.items.length > 0">
                            <div>
                                <label
                                    class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">Items</label>
                                <div class="space-y-2 max-h-40 overflow-y-auto">
                                    <template x-for="(item, index) in receiptData.items" :key="index">
                                        <div
                                            class="flex justify-between items-center p-3 bg-slate-50 dark:bg-slate-800/50 rounded-lg text-sm">
                                            <span class="text-slate-700 dark:text-slate-300" x-text="item.name"></span>
                                            <span class="text-slate-500 dark:text-slate-400"
                                                x-text="'Rp ' + (item.price || 0).toLocaleString('id-ID')"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>

                        {{-- Save Button --}}
                        <button @click="saveTransaction()" :disabled="!selectedCategory || isSaving"
                            :class="selectedCategory && !isSaving ? 'money-gradient hover:shadow-xl hover:shadow-emerald-500/30' : 'bg-slate-300 dark:bg-slate-700 cursor-not-allowed'"
                            class="w-full mt-4 py-4 rounded-2xl text-white font-bold text-lg transition-all duration-300 flex items-center justify-center gap-3">
                            <template x-if="!isSaving">
                                <span class="flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    Simpan Transaksi
                                </span>
                            </template>
                            <template x-if="isSaving">
                                <span class="flex items-center gap-2">
                                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Menyimpan...
                                </span>
                            </template>
                        </button>

                        {{-- Success Message --}}
                        <template x-if="successMessage">
                            <div
                                class="mt-4 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl">
                                <p class="text-emerald-600 dark:text-emerald-400 text-sm flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span x-text="successMessage"></span>
                                </p>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <script>
        function receiptScanner() {
            return {
                isDragging: false,
                selectedFile: null,
                previewUrl: null,
                isLoading: false,
                isSaving: false,
                receiptData: null,
                categories: [],
                selectedCategory: '',
                description: '',
                errorMessage: '',
                successMessage: '',

                init() {
                    // Categories will be loaded after scan
                },

                handleDrop(event) {
                    this.isDragging = false;
                    const files = event.dataTransfer.files;
                    if (files.length > 0) {
                        this.processFile(files[0]);
                    }
                },

                handleFileSelect(event) {
                    const files = event.target.files;
                    if (files.length > 0) {
                        this.processFile(files[0]);
                    }
                },

                processFile(file) {
                    if (!file.type.startsWith('image/')) {
                        this.errorMessage = 'File harus berupa gambar (PNG, JPG, JPEG)';
                        return;
                    }
                    if (file.size > 10 * 1024 * 1024) {
                        this.errorMessage = 'Ukuran file maksimal 10MB';
                        return;
                    }
                    this.errorMessage = '';
                    this.selectedFile = file;
                    this.previewUrl = URL.createObjectURL(file);
                    this.receiptData = null;
                    this.successMessage = '';
                },

                clearImage() {
                    this.selectedFile = null;
                    this.previewUrl = null;
                    this.receiptData = null;
                    this.errorMessage = '';
                    this.successMessage = '';
                },

                async analyzeReceipt() {
                    if (!this.selectedFile) return;

                    this.isLoading = true;
                    this.errorMessage = '';
                    this.receiptData = null;

                    const formData = new FormData();
                    formData.append('receipt_image', this.selectedFile);

                    try {
                        const response = await fetch("{{ route('scan.receipt.analyze') }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: formData
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.receiptData = data.data;
                            this.categories = data.categories;
                            this.description = 'Belanja di ' + (data.data.merchant_name || 'Toko');

                            // Auto-classify: Set suggested category
                            if (data.suggested_category_id) {
                                this.selectedCategory = data.suggested_category_id;
                            }
                        } else {
                            this.errorMessage = data.message || 'Gagal menganalisis struk';
                        }
                    } catch (error) {
                        console.error(error);
                        this.errorMessage = 'Terjadi kesalahan. Silakan coba lagi.';
                    } finally {
                        this.isLoading = false;
                    }
                },

                async saveTransaction() {
                    if (!this.selectedCategory || !this.receiptData) return;

                    this.isSaving = true;
                    this.errorMessage = '';
                    this.successMessage = '';

                    try {
                        const response = await fetch("{{ route('scan.receipt.store') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                amount: this.receiptData.total_amount,
                                category_id: this.selectedCategory,
                                description: this.description || ('Belanja di ' + this.receiptData.merchant_name),
                                transaction_date: this.receiptData.transaction_date || new Date().toISOString().split('T')[0]
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.successMessage = data.message;
                            // Reset form after 2 seconds
                            setTimeout(() => {
                                this.clearImage();
                                this.selectedCategory = '';
                                this.description = '';
                            }, 2000);
                        } else {
                            this.errorMessage = data.message || 'Gagal menyimpan transaksi';
                        }
                    } catch (error) {
                        console.error(error);
                        this.errorMessage = 'Terjadi kesalahan. Silakan coba lagi.';
                    } finally {
                        this.isSaving = false;
                    }
                }
            };
        }
    </script>
</x-app-layout>