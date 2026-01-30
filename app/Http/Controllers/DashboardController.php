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
        // FIX: Rename history 'Add Budget' jadi 'Alokasi Budget' (Run once then remove)
        Transaction::where('user_id', $user->id)
            ->where('description', 'LIKE', 'Add Budget:%')
            ->update(['description' => \DB::raw("REPLACE(description, 'Add Budget:', 'Alokasi Budget :')")]);

        $totalIncome = Transaction::where('user_id', $user->id)
            ->where('type', 'income')
            ->whereMonth('transaction_date', $now->month)
            ->whereYear('transaction_date', $now->year)
            ->sum('amount');

        // 2. PENGELUARAN RIIL (Belanja, tidak termasuk alokasi budget)
        $totalRealExpense = Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->where('description', 'NOT LIKE', '%Budget:%')
            ->whereMonth('transaction_date', $now->month)
            ->whereYear('transaction_date', $now->year)
            ->sum('amount');

        // 3. BUDGET PROGRESS & CHART (Move up for calculation)
        $activeBudgets = Budget::where('user_id', $user->id)
            ->where('start_date', '<=', $now->format('Y-m-d'))
            ->where('end_date', '>=', $now->format('Y-m-d'))
            ->with('category')
            ->get();

        // 4. TOTAL BUDGET ALLOCATION (Sum of active budgets)
        $totalBudgetAllocation = $activeBudgets->sum('amount');

        // 5. TOTAL PENGELUARAN (semua expense untuk display)
        // FIX: Gunakan logic yang sama dengan TransactionController (Sum semua transaksi expense bulan ini)
        $totalExpense = Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereMonth('transaction_date', $now->month)
            ->whereYear('transaction_date', $now->year)
            ->sum('amount');

        // 6. SALDO BERSIH
        $netSavings = $totalIncome - $totalExpense;

        // 7. CALCULATE BUDGET REALIZED
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

        // 8. TOTAL REALIZED PERCENTAGE
        $totalBudgetRealized = $activeBudgets->sum('realized');
        $totalBudgetPercentage = ($totalBudgetAllocation > 0) ? round(($totalBudgetRealized / $totalBudgetAllocation) * 100, 1) : 0;

        // 9. RECENT TRANSACTIONS
        $transactions = Transaction::where('user_id', $user->id)
            ->with('category')
            ->orderBy('transaction_date', 'desc')
            ->limit(5)
            ->get();

        // 8. FINANCIAL HEALTH SCORE (0-100)
        $savingsRatio = $totalIncome > 0 ? (($totalIncome - $totalExpense) / $totalIncome) * 100 : 0;
        $budgetScore = 100;
        $budgetCount = $activeBudgets->count();
        if ($budgetCount > 0) {
            $overBudgetCount = $activeBudgets->filter(fn($b) => $b->percentage > 100)->count();
            $budgetScore = (($budgetCount - $overBudgetCount) / $budgetCount) * 100;
        }
        $healthScore = min(100, max(0, round(($savingsRatio * 0.5) + ($budgetScore * 0.5))));
        $healthStatus = match (true) {
            $healthScore >= 80 => 'Excellent! Keep it up.',
            $healthScore >= 60 => 'Good. Room to improve.',
            $healthScore >= 40 => 'Fair. Watch your spending.',
            default => 'Needs attention.'
        };

        // 9. EXPENSE BY CATEGORY FOR PIE CHART
        $expenseByCategory = Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereMonth('transaction_date', $now->month)
            ->whereYear('transaction_date', $now->year)
            ->with('category')
            ->get()
            ->groupBy(fn($t) => $t->category->name ?? 'Lainnya')
            ->map(fn($group) => $group->sum('amount'))
            ->sortDesc()
            ->take(6); // Top 6 categories

        return view('dashboard', [
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'totalBudgetAllocation' => $totalBudgetAllocation,
            'netSavings' => $netSavings,
            'activeBudgets' => $activeBudgets,
            'transactions' => $transactions,
            'currentMonth' => $now->translatedFormat('F Y'),
            'healthScore' => $healthScore,
            'healthStatus' => $healthStatus,
            'expenseByCategory' => $expenseByCategory,
            'totalBudgetRealized' => $totalBudgetRealized,
            'totalBudgetPercentage' => $totalBudgetPercentage,
        ]);
    }
}