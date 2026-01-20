<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class AiController extends Controller
{
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

        // Ambil context keuangan singkat agar AI "kenal" kondisi user
        $totalIncome = Transaction::where('user_id', $userId)->where('type', 'income')->whereMonth('transaction_date', now()->month)->sum('amount');
        $totalExpense = Transaction::where('user_id', $userId)->where('type', 'expense')->whereMonth('transaction_date', now()->month)->sum('amount');
        $balance = $totalIncome - $totalExpense;

        $context = "Context: Nama user adalah {$userName}. Bulan ini pemasukan Rp" . number_format($totalIncome, 0, ',', '.') . 
                   ", pengeluaran Rp" . number_format($totalExpense, 0, ',', '.') . 
                   ", dan saldo saat ini Rp" . number_format($balance, 0, ',', '.') . ".";

        try {
            $apiKey = env('GEMINI_API_KEY');

            if (!$apiKey) {
                return response()->json(['success' => false, 'message' => 'API Key tidak ditemukan di .env'], 500);
            }

            $response = Http::timeout(30)->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $context . "\n\nInstruksi: Jawablah sebagai Fina, asisten keuangan pribadi yang ramah, gaul, dan solutif. Gunakan Bahasa Indonesia.\n\nUser bertanya: " . $userMessage]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 800,
                ]
            ]);

            $result = $response->json();
            $reply = $result['candidates'][0]['content']['parts'][0]['text'] ?? 'Aduh bro, otaknya G-ment lagi nge-blank. Coba tanya lagi ya!';

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
        $netBalance = $totalIncome - $totalExpense;

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
            "net_balance" => number_format($netBalance, 0, ',', '.'),
            "top_category" => $topExpenseCategory->category_name ?? 'N/A',
            "top_amount" => number_format($topExpenseCategory->total_amount ?? 0, 0, ',', '.'),
        ];

        $prompt = "Berikan 3 poin insight singkat (bullet point) dalam Bahasa Indonesia untuk data keuangan ini: 
                   Pemasukan: Rp{$data['total_income']}, Pengeluaran: Rp{$data['total_expense']}, 
                   Kategori Terboros: {$data['top_category']} (Rp{$data['top_amount']}). 
                   Fokus pada saran tindakan. Tanpa pembukaan/penutupan.";

        try {
            $apiKey = env('GEMINI_API_KEY');
            $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}", [
                'contents' => [['parts' => [['text' => $prompt]]]]
            ]);

            $aiResult = $response->json();
            $insight = $aiResult['candidates'][0]['content']['parts'][0]['text'] ?? 'Tetap semangat kelola keuanganmu!';

            return response()->json([
                'success' => true,
                'insight' => $insight,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}