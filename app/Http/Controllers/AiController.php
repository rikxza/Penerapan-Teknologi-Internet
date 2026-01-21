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
        $totalIncome = Transaction::where('user_id', $userId)->where('type', 'income')->whereMonth('transaction_date', now()->month)->sum('amount');
        $totalExpense = Transaction::where('user_id', $userId)->where('type', 'expense')->whereMonth('transaction_date', now()->month)->sum('amount');
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

        $prompt = "Kamu adalah 'G-ment', asisten keuangan pribadi yang ramah, gaul, dan solutif. Gunakan Bahasa Indonesia yang santai tapi profesional.\n\n" .
            "Data User:\n" .
            "- Nama: {$userName}\n" .
            "- Pemasukan bulan ini: Rp" . number_format($totalIncome, 0, ',', '.') . "\n" .
            "- Pengeluaran bulan ini: Rp" . number_format($totalExpense, 0, ',', '.') . "\n" .
            "- Saldo saat ini: Rp" . number_format($balance, 0, ',', '.') . "\n" .
            "- 10 Transaksi terakhir: " . json_encode($recentTransactions, JSON_UNESCAPED_UNICODE) . "\n\n" .
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
        $totalExpense = Transaction::where('user_id', $userId)->where('type', 'expense')->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])->sum('amount');

        $topExpenseCategory = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->leftJoin('categories', 'transactions.category_id', '=', 'categories.id')
            ->selectRaw("COALESCE(categories.name, 'Tanpa Kategori') as category_name, SUM(transactions.amount) as total_amount")
            ->groupByRaw("COALESCE(categories.name, 'Tanpa Kategori')")
            ->orderByDesc('total_amount')
            ->first();
        
        $data = [
            "total_income" => number_format($totalIncome, 0, ',', '.'),
            "total_expense" => number_format($totalExpense, 0, ',', '.'),
            "top_category" => $topExpenseCategory->category_name ?? 'N/A',
            "top_amount" => number_format($topExpenseCategory->total_amount ?? 0, 0, ',', '.'),
        ];

        $prompt = "Berikan 3 poin insight singkat (bullet point) dalam Bahasa Indonesia untuk data keuangan ini: 
                   Pemasukan: Rp{$data['total_income']}, Pengeluaran: Rp{$data['total_expense']}, 
                   Kategori Terboros: {$data['top_category']} (Rp{$data['top_amount']}). 
                   Fokus pada saran tindakan. Tanpa pembukaan/penutupan.";

        try {
            $insight = $this->callGemini($prompt);

            return response()->json([
                'success' => true,
                'insight' => $insight ?? 'Tetap semangat kelola keuanganmu!',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
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

            return response()->json([
                'success' => true,
                'data' => $receiptData,
                'categories' => $categories,
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
            'transaction_date' => 'required|date',
        ]);

        $transaction = Transaction::create([
            'user_id' => Auth::id(),
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            'type' => 'expense',
            'description' => $request->description ?? 'Dari Scan Struk',
            'transaction_date' => $request->transaction_date,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil disimpan!',
            'transaction' => $transaction,
        ]);
    }
}