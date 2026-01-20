<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ApiController extends Controller
{
    public function dashboardData()
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $userId = Auth::id();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // [LINE CHART LOGIC - AMAN]
        $endDate = Carbon::now()->endOfDay();
        $startDate = $endDate->copy()->subDays(6)->startOfDay(); 

        $dailyData = Transaction::where('user_id', $userId)
                                ->whereBetween('transaction_date', [$startDate, $endDate])
                                ->orderBy('transaction_date', 'asc')
                                ->get()
                                ->groupBy(function ($date) {
                                    return Carbon::parse($date->transaction_date)->format('Y-m-d');
                                });

        $dates = [];
        $balances = [];
        $currentBalance = 0; 

        for ($i = 0; $i <= 6; $i++) {
            $date = $startDate->copy()->addDays($i);
            $dateKey = $date->format('Y-m-d');
            $dates[] = $date->format('d M'); 

            $transactionsToday = $dailyData->get($dateKey, collect());
            
            $incomeToday = $transactionsToday->where('type', 'income')->sum('amount');
            $expenseToday = $transactionsToday->where('type', 'expense')->sum('amount');
            
            $currentBalance += ($incomeToday - $expenseToday);
            $balances[] = $currentBalance;
        }

        // ------------------------------------------------
        // 2. DATA PIE CHART (PENGELUARAN BULAN INI)
        // ------------------------------------------------
        $pieData = Transaction::where('transactions.user_id', $userId)
                    ->where('transactions.type', 'expense')
                    ->whereBetween('transactions.transaction_date', [$startOfMonth, $endOfMonth])
                    ->leftJoin('categories', 'transactions.category_id', '=', 'categories.id')
                    ->selectRaw("
                        COALESCE(categories.name, 'No Category') as category_name,
                        SUM(transactions.amount) as total_amount
                    ")
                    ->groupByRaw("COALESCE(categories.name, 'No Category')")
                    ->orderByDesc('total_amount')
                    ->get();

        $pieLabels = $pieData->pluck('category_name')->toArray();
        $pieAmounts = $pieData->pluck('total_amount')->toArray();


        // ------------------------------------------------
        // 3. KIRIM RESPON JSON
        // ------------------------------------------------
        return response()->json([
            'success' => true,
            'chart_line' => [
                'labels' => $dates,
                'data' => $balances,
            ],
            'chart_pie' => [
                'labels' => $pieLabels,
                'data' => $pieAmounts,
            ],
            // Stats (tidak diubah, harusnya aman)
            'stats' => [
                'masuk' => Transaction::where('user_id', $userId)->where('type', 'income')->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])->sum('amount'),
                'keluar' => Transaction::where('user_id', $userId)->where('type', 'expense')->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])->sum('amount'),
            ]
        ]);
    }
}