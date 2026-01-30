<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class AiController extends Controller
{
    private function getGeminiApiKey()
    {
        return env('GEMINI_API_KEY');
    }

    private function callGemini(string $prompt, ?string $imageBase64 = null, ?string $mimeType = null)
    {
        $apiKey = $this->getGeminiApiKey();

        // Jika ada gambar, gunakan format multimodal
        if ($imageBase64 && $mimeType) {
            $contents = [
                [
                    'parts' => [
                        [
                            'inline_data' => [
                                'mime_type' => $mimeType,
                                'data' => $imageBase64,
                            ]
                        ],
                        [
                            'text' => $prompt
                        ]
                    ]
                ]
            ];
        } else {
            $contents = [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ];
        }

        $response = Http::timeout(60)->post(
            "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}",
            [
                'contents' => $contents,
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 4096,
                ]
            ]
        );

        $result = $response->json();

        // Debug: Log full response from Gemini
        \Log::info('Gemini API Full Response: ' . json_encode($result));

        return $result['candidates'][0]['content']['parts'][0]['text'] ?? null;
    }

    /**
     * Fitur 1: Chat Interaktif (Untuk Halaman AI Chat)
     */
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string'
        ]);

        $userMessage = $request->input('message');
        $userId = Auth::id();
        $userName = Auth::user()->name;

        // Ambil context keuangan
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $totalIncome = Transaction::where('user_id', $userId)->where('type', 'income')->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])->sum('amount');
        $totalExpense = Transaction::where('user_id', $userId)->where('type', 'expense')->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])->sum('amount');
        $balance = $totalIncome - $totalExpense;

        // Get recent transactions
        $recentTransactions = Transaction::where('user_id', $userId)
            ->with('category')
            ->orderBy('transaction_date', 'desc')
            ->take(10)
            ->get()
            ->map(function ($t) {
                return [
                    'type' => $t->type,
                    'amount' => 'Rp' . number_format($t->amount, 0, ',', '.'),
                    'category' => $t->category->name ?? 'Tanpa Kategori',
                    'description' => $t->description,
                    'date' => $t->transaction_date->format('d M Y'),
                ];
            });

        // Get budget data with spending info
        $budgets = \App\Models\Budget::where('user_id', $userId)
            ->where('end_date', '>=', now())
            ->with('category')
            ->get()
            ->map(function ($budget) use ($userId, $startOfMonth, $endOfMonth) {
                $spent = Transaction::where('user_id', $userId)
                    ->where('category_id', $budget->category_id)
                    ->where('type', 'expense')
                    ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
                    ->sum('amount');

                $percentage = $budget->amount > 0 ? round(($spent / $budget->amount) * 100) : 0;
                $status = $spent > $budget->amount ? 'OVER BUDGET!' : ($percentage >= 80 ? 'Hampir habis' : 'Aman');

                return [
                    'kategori' => $budget->category->name ?? 'Unknown',
                    'limit' => 'Rp' . number_format($budget->amount, 0, ',', '.'),
                    'terpakai' => 'Rp' . number_format($spent, 0, ',', '.'),
                    'sisa' => 'Rp' . number_format(max(0, $budget->amount - $spent), 0, ',', '.'),
                    'persentase' => $percentage . '%',
                    'status' => $status,
                ];
            });

        // Get top expense category for context
        $topExpenseCategory = Transaction::where('transactions.user_id', $userId)
            ->where('transactions.type', 'expense')
            ->whereBetween('transactions.transaction_date', [$startOfMonth, $endOfMonth])
            ->leftJoin('categories', 'transactions.category_id', '=', 'categories.id')
            ->selectRaw("COALESCE(categories.name, 'Tanpa Kategori') as category_name, SUM(transactions.amount) as total_amount")
            ->groupByRaw("COALESCE(categories.name, 'Tanpa Kategori')")
            ->orderByDesc('total_amount')
            ->first();

        // ---------------------------------------------------------
        // ANOMALY DETECTION (Disamakan dengan getInsight)
        // ---------------------------------------------------------
        $anomalies = collect();

        // 1. Detect unusually large transactions (> 2x average)
        $avgTransaction = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->avg('amount') ?? 0;

        $largeTransactions = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->where('amount', '>', $avgTransaction * 2)
            ->with('category')
            ->get();

        foreach ($largeTransactions as $t) {
            $anomalies->push("🔴 Transaksi besar: {$t->description} sebesar Rp" . number_format($t->amount, 0, ',', '.') .
                " (" . ($t->category->name ?? 'Tanpa Kategori') . ")");
        }

        // 2. Detect category spending spikes vs last month
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        $thisMonthByCategory = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->pluck('total', 'category_id');

        $lastMonthByCategory = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$lastMonthStart, $lastMonthEnd])
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->pluck('total', 'category_id');

        foreach ($thisMonthByCategory as $catId => $thisMonthTotal) {
            $lastMonthTotal = $lastMonthByCategory[$catId] ?? 0;
            if ($lastMonthTotal > 0 && $thisMonthTotal > $lastMonthTotal * 1.5) {
                $category = \App\Models\Category::find($catId);
                $increase = round((($thisMonthTotal - $lastMonthTotal) / $lastMonthTotal) * 100);
                $categoryName = $category->name ?? 'Unknown';
                $anomalies->push("📈 Kategori {$categoryName} melonjak {$increase}% dibanding bulan lalu");
            }
        }

        $anomalyText = "";
        if ($anomalies->count() > 0) {
            $anomalyText = "\n- ANOMALI TERDETEKSI:\n" . $anomalies->take(5)->join("\n");
        }
        // ---------------------------------------------------------

        $prompt = "Kamu adalah 'G-ment', asisten keuangan pribadi yang ramah, gaul, dan solutif. Gunakan Bahasa Indonesia yang santai tapi profesional.\n\n" .
            "Data User:\n" .
            "- Nama: {$userName}\n" .
            "- Pemasukan bulan ini: Rp" . number_format($totalIncome, 0, ',', '.') . "\n" .
            "- Pengeluaran bulan ini: Rp" . number_format($totalExpense, 0, ',', '.') . "\n" .
            "- Kategori Terboros: " . ($topExpenseCategory->category_name ?? 'N/A') . " (Rp" . number_format($topExpenseCategory->total_amount ?? 0, 0, ',', '.') . ")\n" .
            "- Saldo saat ini: Rp" . number_format($balance, 0, ',', '.') . "\n\n" .
            "- Budget yang diset user:\n" . json_encode($budgets, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n\n" .
            $anomalyText . "\n\n" .
            "- 10 Transaksi terakhir: " . json_encode($recentTransactions, JSON_UNESCAPED_UNICODE) . "\n\n" .
            "PENTING: Jika ada budget dengan status 'OVER BUDGET!' atau 'Hampir habis', beri peringatan kepada user!\n\n" .
            "User bertanya: " . $userMessage;

        try {
            $reply = $this->callGemini($prompt);

            if (!$reply) {
                return response()->json(['success' => false, 'message' => 'AI tidak merespon'], 500);
            }

            return response()->json([
                'success' => true,
                'reply' => $reply
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal kontak AI: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Fitur 2: Get Insight (Untuk Dashboard Ringkasan)
     */
    public function getInsight()
    {
        $userId = Auth::id();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $totalIncome = Transaction::where('user_id', $userId)->where('type', 'income')->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])->sum('amount');

        $totalIncome = Transaction::where('user_id', $userId)->where('type', 'income')->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])->sum('amount');
        $totalExpense = Transaction::where('user_id', $userId)->where('type', 'expense')->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])->sum('amount');

        $topExpenseCategory = Transaction::where('transactions.user_id', $userId)
            ->where('transactions.type', 'expense')
            ->whereBetween('transactions.transaction_date', [$startOfMonth, $endOfMonth])
            ->leftJoin('categories', 'transactions.category_id', '=', 'categories.id')
            ->selectRaw("COALESCE(categories.name, 'Tanpa Kategori') as category_name, SUM(transactions.amount) as total_amount")
            ->groupByRaw("COALESCE(categories.name, 'Tanpa Kategori')")
            ->orderByDesc('total_amount')
            ->first();

        // Get budget data with spending info
        $budgets = \App\Models\Budget::where('user_id', $userId)
            ->where('end_date', '>=', now())
            ->with('category')
            ->get()
            ->map(function ($budget) use ($userId, $startOfMonth, $endOfMonth) {
                $spent = Transaction::where('user_id', $userId)
                    ->where('category_id', $budget->category_id)
                    ->where('type', 'expense')
                    ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
                    ->sum('amount');

                return [
                    'category' => $budget->category->name ?? 'Unknown',
                    'limit' => $budget->amount,
                    'spent' => $spent,
                    'percentage' => $budget->amount > 0 ? round(($spent / $budget->amount) * 100) : 0,
                    'over_budget' => $spent > $budget->amount,
                    'over_amount' => max(0, $spent - $budget->amount),
                ];
            });

        $overBudgets = $budgets->filter(fn($b) => $b['over_budget']);

        $data = [
            "total_income" => number_format($totalIncome, 0, ',', '.'),
            "total_expense" => number_format($totalExpense, 0, ',', '.'),
            "top_category" => $topExpenseCategory->category_name ?? 'N/A',
            "top_amount" => number_format($topExpenseCategory->total_amount ?? 0, 0, ',', '.'),
        ];

        // Build budget warning text
        $budgetWarning = '';
        if ($overBudgets->count() > 0) {
            $warnings = $overBudgets->map(function ($b) {
                return "{$b['category']} (limit Rp" . number_format($b['limit'], 0, ',', '.') .
                    ", terpakai Rp" . number_format($b['spent'], 0, ',', '.') .
                    ", OVER Rp" . number_format($b['over_amount'], 0, ',', '.') . ")";
            })->join(', ');
            $budgetWarning = "\n⚠️ PERINGATAN BUDGET TERLAMPAUI: {$warnings}";
        }

        // ANOMALY DETECTION
        $anomalies = collect();

        // 1. Detect unusually large transactions (> 2x average)
        $avgTransaction = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->avg('amount') ?? 0;

        $largeTransactions = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->where('amount', '>', $avgTransaction * 2)
            ->with('category')
            ->get();

        foreach ($largeTransactions as $t) {
            $anomalies->push("🔴 Transaksi besar: {$t->description} sebesar Rp" . number_format($t->amount, 0, ',', '.') .
                " (" . ($t->category->name ?? 'Tanpa Kategori') . ") - " .
                round($t->amount / max($avgTransaction, 1), 1) . "x dari rata-rata");
        }

        // 2. Detect category spending spikes vs last month
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        $thisMonthByCategory = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->where('description', 'NOT LIKE', '%Budget%')
            ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->pluck('total', 'category_id');

        $lastMonthByCategory = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->where('description', 'NOT LIKE', '%Budget%')
            ->whereBetween('transaction_date', [$lastMonthStart, $lastMonthEnd])
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->pluck('total', 'category_id');

        foreach ($thisMonthByCategory as $catId => $thisMonthTotal) {
            $lastMonthTotal = $lastMonthByCategory[$catId] ?? 0;
            if ($lastMonthTotal > 0 && $thisMonthTotal > $lastMonthTotal * 1.5) {
                $category = \App\Models\Category::find($catId);
                $increase = round((($thisMonthTotal - $lastMonthTotal) / $lastMonthTotal) * 100);
                $categoryName = $category->name ?? 'Unknown';
                $anomalies->push("📈 Kategori {$categoryName} melonjak {$increase}% dibanding bulan lalu");
            }
        }

        // Build anomaly warning text
        $anomalyWarning = '';
        if ($anomalies->count() > 0) {
            $anomalyWarning = "\n🚨 ANOMALI TERDETEKSI:\n" . $anomalies->take(3)->join("\n");
        }

        $prompt = "Berikan 3 poin insight SANGAT SINGKAT (maksimal 15 kata per poin) dengan gaya 'Warning' yang tegas.
                   Data: 
                   - Pemasukan: Rp{$data['total_income']}
                   - Pengeluaran: Rp{$data['total_expense']}
                   - Kategori Terboros: {$data['top_category']} (Rp{$data['top_amount']})
                   {$budgetWarning}
                   {$anomalyWarning}
                   
                   Format Output WAJIB (Gunakan Emoji):
                   ⚠️ [Peringatan Budget/Anomali Paling Kritis]
                   💡 [Saran Penghematan Spesifik]
                   📈 [Status Keuangan Singkat]
                   
                   CONTOH OUTPUT YANG DIINGINKAN:
                   ⚠️ Budget Hiburan OVER 102%! Stop jajan kopi sekarang.
                   💡 Masak sendiri bisa hemat Rp500rb minggu depan.
                   📈 Cashflow positif, tapi boros di gaya hidup.
                   
                   PENTING: JANGAN BERTELE-TELE. LANGSUNG KE POIN PENYAKIT KEUANGANNYA.";

        try {
            $insight = $this->callGemini($prompt);

            if ($insight) {
                return response()->json([
                    'success' => true,
                    'insight' => $insight,
                ]);
            }
        } catch (\Exception $e) {
            // Log error but continue to fallback
            \Log::warning('Gemini API failed for insight: ' . $e->getMessage());
        }

        // Fallback: Generate local insight if AI fails
        $fallbackInsight = [];

        // Budget warnings
        if ($overBudgets->count() > 0) {
            foreach ($overBudgets->take(2) as $b) {
                $fallbackInsight[] = "⚠️ Budget " . $b['category'] . " terlampaui! (Rp" . number_format($b['over_amount'], 0, ',', '.') . " over)";
            }
        }

        // Anomaly warnings
        if ($anomalies->count() > 0) {
            foreach ($anomalies->take(2) as $anomaly) {
                $fallbackInsight[] = $anomaly;
            }
        }

        // General stats
        if ($totalExpense > $totalIncome) {
            $fallbackInsight[] = "📊 Pengeluaran (Rp" . number_format($totalExpense, 0, ',', '.') . ") melebihi pemasukan. Kurangi belanja non-esensial!";
        } else {
            $savings = $totalIncome - $totalExpense;
            $fallbackInsight[] = "✨ Kamu sudah menabung Rp" . number_format($savings, 0, ',', '.') . " bulan ini. Pertahankan!";
        }

        return response()->json([
            'success' => true,
            'insight' => count($fallbackInsight) > 0
                ? implode("\n", $fallbackInsight)
                : 'Tetap semangat kelola keuanganmu! 💪',
        ]);
    }

    /**
     * Fitur 3: Scan Receipt (Analisis struk belanja dengan Gemini Vision)
     */
    public function scanReceipt(Request $request)
    {
        $request->validate([
            'receipt_image' => 'required|image|max:10240',
        ]);

        $image = $request->file('receipt_image');
        $base64Image = base64_encode(file_get_contents($image->getRealPath()));
        $mimeType = $image->getMimeType();

        $prompt = "Kamu adalah asisten yang ahli membaca struk/nota belanja. Ekstrak informasi berikut dari gambar struk dan kembalikan dalam format JSON SAJA (tanpa markdown, tanpa penjelasan tambahan):
{
  \"merchant_name\": \"Nama Toko/Merchant\",
  \"transaction_date\": \"YYYY-MM-DD\",
  \"total_amount\": 12345,
  \"items\": [
    {\"name\": \"Nama Item\", \"price\": 1000, \"qty\": 1}
  ],
  \"confidence\": \"high/medium/low\"
}

Jika ada informasi yang tidak terbaca, beri nilai null. Total amount harus berupa angka tanpa format. Tolong baca struk ini dan ekstrak informasinya.";

        try {
            $content = $this->callGemini($prompt, $base64Image, $mimeType);

            \Log::info('Gemini Response: ' . ($content ?? 'NULL'));

            if (!$content) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membaca struk.'
                ], 422);
            }
            // Parse JSON from response
            $jsonContent = preg_replace('/```json\s*|\s*```/', '', $content);
            $receiptData = json_decode($jsonContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membaca struk. Format tidak dikenali.',
                    'raw_response' => $content
                ], 422);
            }

            // Get expense categories
            $categories = Category::where('type', 'expense')->get(['id', 'name']);

            // UC-06: Auto-classify - Suggest category based on merchant name and items
            $suggestedCategoryId = null;
            $merchantName = strtolower($receiptData['merchant_name'] ?? '');
            $items = collect($receiptData['items'] ?? [])->pluck('name')->implode(' ');
            $searchText = strtolower($merchantName . ' ' . $items);

            // Category mapping based on keywords
            $categoryMappings = [
                'Makanan' => ['resto', 'restaurant', 'cafe', 'coffee', 'kopi', 'makan', 'food', 'warung', 'warteg', 'bakso', 'mie', 'nasi', 'ayam', 'pizza', 'burger', 'kfc', 'mcd', 'starbucks', 'dunkin', 'janji jiwa', 'kopi kenangan', 'gofood', 'grabfood'],
                'Transportasi' => ['grab', 'gojek', 'uber', 'taxi', 'taksi', 'bensin', 'pertamina', 'shell', 'spbu', 'parkir', 'tol', 'transjakarta', 'mrt', 'lrt', 'kereta', 'train', 'bus', 'angkot'],
                'Belanja' => ['indomaret', 'alfamart', 'supermarket', 'hypermart', 'carrefour', 'giant', 'lottemart', 'tokopedia', 'shopee', 'lazada', 'bukalapak', 'blibli', 'marketplace'],
                'Tagihan' => ['pln', 'listrik', 'pdam', 'air', 'telkom', 'indihome', 'wifi', 'internet', 'pulsa', 'token', 'bpjs', 'asuransi'],
                'Hiburan' => ['cinema', 'bioskop', 'xxi', 'cgv', 'cinemaxx', 'netflix', 'spotify', 'youtube', 'game', 'steam', 'playstation', 'xbox', 'karaoke', 'happy puppy'],
                'Kesehatan' => ['apotek', 'pharmacy', 'kimia farma', 'century', 'k24', 'dokter', 'klinik', 'rumah sakit', 'hospital', 'lab', 'halodoc', 'alodokter'],
                'Pendidikan' => ['buku', 'book', 'gramedia', 'togamas', 'kursus', 'les', 'course', 'udemy', 'skill', 'belajar'],
            ];

            foreach ($categoryMappings as $categoryName => $keywords) {
                foreach ($keywords as $keyword) {
                    if (str_contains($searchText, $keyword)) {
                        // Find matching category
                        $matchedCategory = $categories->first(function ($cat) use ($categoryName) {
                            return str_contains(strtolower($cat->name), strtolower($categoryName));
                        });
                        if ($matchedCategory) {
                            $suggestedCategoryId = $matchedCategory->id;
                            break 2;
                        }
                    }
                }
            }

            // Fallback to first category if no match found
            if (!$suggestedCategoryId && $categories->count() > 0) {
                // Try to find "Lain-lain" or similar
                $fallbackCategory = $categories->first(function ($cat) {
                    return str_contains(strtolower($cat->name), 'lain');
                }) ?? $categories->first();
                $suggestedCategoryId = $fallbackCategory->id;
            }

            return response()->json([
                'success' => true,
                'data' => $receiptData,
                'categories' => $categories,
                'suggested_category_id' => $suggestedCategoryId,
            ]);

        } catch (\Exception $e) {
            \Log::error('Gemini Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menganalisis struk: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Fitur 4: Store Receipt Transaction
     */
    public function storeReceipt(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string|max:255',
            'transaction_date' => 'required|date|before_or_equal:today',
        ]);

        // Parse and validate transaction date - if AI misread and date is too old, use today
        $parsedDate = Carbon::parse($request->transaction_date);
        $oneYearAgo = Carbon::now()->subYear();

        // If the date is more than 1 year old, AI probably misread - use today instead
        if ($parsedDate->lt($oneYearAgo)) {
            $parsedDate = Carbon::now();
        }

        $transaction = Transaction::create([
            'user_id' => Auth::id(),
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            'type' => 'expense',
            'description' => $request->description ?? 'Dari Scan Struk',
            'transaction_date' => $parsedDate->setTimeFrom(now()),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil disimpan!',
            'transaction' => $transaction,
        ]);
    }

    /**
     * Fitur 5: Financial Forecasting
     */
    public function getForecast()
    {
        $userId = Auth::id();

        // Gather last 3 months data
        $last3Months = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->where('transaction_date', '>=', Carbon::now()->subMonths(3))
            ->orderBy('transaction_date', 'asc')
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->transaction_date)->format('F Y');
            })
            ->map(function ($row) {
                return $row->sum('amount');
            });

        $prompt = "Kamu adalah analis keuangan. Prediksi total pengeluaran bulan depan berdasarkan riwayat 3 bulan terakhir user ini:
" . json_encode($last3Months) . "

Jawab dalam format JSON saja:
{
  \"predicted_amount\": 1500000,
  \"analysis\": \"Satu kalimat singkat analisis tren.\",
  \"suggestion\": \"Satu kalimat saran.\"
}
Jika data kosong, berikan estimasi 0.";

        try {
            $response = $this->callGemini($prompt);
            $jsonContent = preg_replace('/```json\s*|\s*```/', '', $response);
            $data = json_decode($jsonContent, true);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}