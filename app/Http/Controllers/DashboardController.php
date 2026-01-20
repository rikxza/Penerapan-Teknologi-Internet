<?php

namespace App\Http\Controllers;

use App\Models\{Transaction, Budget, Category};
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $now = Carbon::now();

        // 1. PEMASUKAN
        $totalIncome = Transaction::where('user_id', $user->id)
            ->where('type', 'income')
            ->whereMonth('transaction_date', $now->month)
            ->whereYear('transaction_date', $now->year)
            ->sum('amount');

        // 2. PENGELUARAN TRANSAKSI (Belanja Riil)
        $totalExpense = Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->where('description', 'NOT LIKE', '%Budget:%')
            ->whereMonth('transaction_date', $now->month)
            ->whereYear('transaction_date', $now->year)
            ->sum('amount');

        // 3. PENGELUARAN BUDGET (Alokasi/Jatah)
        $totalBudgetAllocation = Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->where('description', 'LIKE', '%Budget:%')
            ->whereMonth('transaction_date', $now->month)
            ->whereYear('transaction_date', $now->year)
            ->sum('amount');

        // 4. SALDO BERSIH (Pemasukan - (Belanja + Alokasi))
        $netSavings = $totalIncome - ($totalExpense + $totalBudgetAllocation);

        // 5. BUDGET PROGRESS & CHART
        $activeBudgets = Budget::where('user_id', $user->id)
            ->with('category')
            ->get();
        
        foreach ($activeBudgets as $budget) {
            $realizedAmount = Transaction::where('user_id', $user->id)
                ->where('category_id', $budget->category_id)
                ->where('type', 'expense')
                ->where('description', 'NOT LIKE', '%Budget:%') 
                ->whereBetween('transaction_date', [
                    $budget->start_date . ' 00:00:00', 
                    $budget->end_date . ' 23:59:59'
                ])
                ->sum('amount');
            
            $budget->realized = $realizedAmount;
            $budget->percentage = ($budget->amount > 0) ? round(($realizedAmount / $budget->amount) * 100, 1) : 0;
        }

        $transactions = Transaction::where('user_id', $user->id)
            ->with('category') 
            ->orderBy('transaction_date', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', [
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'totalBudgetAllocation' => $totalBudgetAllocation,
            'netSavings' => $netSavings,
            'activeBudgets' => $activeBudgets,
            'transactions' => $transactions, 
            'currentMonth' => $now->translatedFormat('F Y'),
        ]);
    }
}