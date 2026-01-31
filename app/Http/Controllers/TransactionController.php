<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use App\Models\Budget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TransactionController extends Controller
{
    /**
     * Halaman utama transaksi
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // 1. FILTER: DATE RANGE & PERIOD
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $period = $request->input('period');

        // Defaults
        $selectedMonth = Carbon::now()->month;
        $selectedYear = Carbon::now()->year;

        // Logic: Date Range takes precedence over Period
        if ($startDate && $endDate) {
            // Using custom date range
            $filterDate = Carbon::parse($startDate); // Just for view compatibility if needed
        } elseif ($period && preg_match('/^(\d{4})-(\d{2})$/', $period, $matches)) {
            // Using Monthly Period
            $selectedYear = (int) $matches[1];
            $selectedMonth = (int) $matches[2];
            $filterDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1);

            // Set start and end for query
            $startDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->endOfMonth()->format('Y-m-d');
        } else {
            // Default: Current Month
            $filterDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1);
            $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        }

        // 2. SORTING
        $sortDir = $request->input('sort_dir', 'desc'); // Default latest first
        if (!in_array(strtolower($sortDir), ['asc', 'desc'])) {
            $sortDir = 'desc';
        }

        // Ambil kategori milik user atau kategori default (null)
        $categories = Category::where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->orWhereNull('user_id');
        })
            ->get()
            ->groupBy('type');

        // QUERY TRANSACTIONS
        $query = Transaction::where('user_id', $user->id)
            ->with('category');

        // Apply Date Filter
        if ($startDate && $endDate) {
            $query->whereBetween('transaction_date', [
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59'
            ]);
        }

        // Apply Sorting
        $transactions = $query->orderBy('transaction_date', $sortDir)
            ->paginate(10)
            ->appends([
                'period' => $period,
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'sort_dir' => $sortDir
            ]);

        // STATS CALCULATION (Respecting the same filter)
        $statsQuery = Transaction::where('user_id', $user->id);
        if ($startDate && $endDate) {
            $statsQuery->whereBetween('transaction_date', [
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59'
            ]);
        }

        // Clone query for efficiency
        $monthlyIncome = (clone $statsQuery)->where('type', 'income')->sum('amount');
        $monthlyExpense = (clone $statsQuery)->where('type', 'expense')->sum('amount');
        $monthlyTransactionCount = (clone $statsQuery)->count();
        $totalTransactions = Transaction::where('user_id', $user->id)->count(); // All time count

        // Active active budgets
        $activeBudgets = Budget::where('user_id', $user->id)
            ->where('end_date', '>=', now())
            ->count();

        // Generate list of available months (last 12 months)
        $availableMonths = collect();
        for ($i = 0; $i < 12; $i++) {
            $date = Carbon::now()->subMonths($i);
            $availableMonths->push([
                'month' => $date->month,
                'year' => $date->year,
                'label' => $date->translatedFormat('F Y'),
            ]);
        }

        return view('transactions.index', [
            'categories' => $categories,
            'transactions' => $transactions,
            'totalTransactions' => $totalTransactions,
            'activeBudgets' => $activeBudgets,
            'monthlyIncome' => $monthlyIncome,
            'monthlyExpense' => $monthlyExpense,
            'monthlyTransactionCount' => $monthlyTransactionCount,
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
            'filterDate' => $filterDate,
            'availableMonths' => $availableMonths,
            // Pass back new filter vars
            'startDate' => $startDate,
            'endDate' => $endDate,
            'sortDir' => $sortDir,
            'isCustomFilter' => $request->has('start_date') // Flag to show we are in custom filter mode
        ]);
    }

    /**
     * Simpan transaksi baru
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'type' => ['required', 'string', 'in:income,expense'],
            'amount' => ['required', 'numeric', 'min:1'],
            'category_id' => ['required'], // Bisa ID atau string 'new'
            'new_category' => ['required_if:category_id,new', 'nullable', 'string', 'max:255'],
            'transaction_date' => ['required', 'date', 'before_or_equal:today'],
            'description' => ['required', 'string', 'min:3', 'max:255'],
            'receipt_image' => ['nullable', 'image', 'max:5120'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('openForm', true);
        }

        $validated = $validator->validated();

        // 2. Olah data setelah validasi sukses
        $validated['user_id'] = Auth::id();

        // 2b. Handle Custom Category
        if ($request->input('category_id') === 'new') {
            $newCategoryName = $validated['new_category'];

            // Cek apakah kategori dengan nama sama sudah ada (baik global atau milik user)
            $existingCategory = Category::where('name', $newCategoryName)
                ->where(function ($q) {
                    $q->where('user_id', Auth::id())
                        ->orWhereNull('user_id');
                })
                ->where('type', $validated['type'])
                ->first();

            if ($existingCategory) {
                $validated['category_id'] = $existingCategory->id;
            } else {
                // Buat baru
                $newCat = Category::create([
                    'name' => $newCategoryName,
                    'type' => $validated['type'],
                    'user_id' => Auth::id()
                ]);
                $validated['category_id'] = $newCat->id;
            }
            // Hapus field dummy
            unset($validated['new_category']);
        }

        /** * FIX JAM: Gabungkan tanggal dari input dengan jam menit detik saat ini 
         * agar jamnya tidak 00:00.
         */
        $validated['transaction_date'] = Carbon::parse($request->transaction_date)
            ->setTimeFrom(now());

        if ($request->hasFile('receipt_image')) {
            $path = $request->file('receipt_image')->store('receipts', 'public');
            $validated['receipt_image'] = $path;
        }

        // 3. Eksekusi simpan ke database
        Transaction::create($validated);

        // CHECK BUDGET ALERT (Fitur Notification System)
        if ($validated['type'] === 'expense') {
            $budget = Budget::where('user_id', Auth::id())
                ->where('category_id', $validated['category_id'])
                ->where('end_date', '>=', now())
                ->first();

            if ($budget) {
                // Re-calculate spent for this month (or budget period)
                // Assuming budget is monthly based on Dashboard logic
                $startOfMonth = Carbon::now()->startOfMonth();
                $endOfMonth = Carbon::now()->endOfMonth();

                $spent = Transaction::where('user_id', Auth::id())
                    ->where('category_id', $budget->category_id)
                    ->where('type', 'expense')
                    ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
                    ->sum('amount');

                $percentage = ($spent / $budget->amount) * 100;

                if ($percentage >= 100) {
                    Auth::user()->notify(new \App\Notifications\BudgetAlertNotification("Budget {$budget->category->name} sudah tembus " . round($percentage) . "%!", 'danger'));
                } elseif ($percentage >= 80) {
                    Auth::user()->notify(new \App\Notifications\BudgetAlertNotification("Budget {$budget->category->name} sudah mencapai " . round($percentage) . "%!", 'warning'));
                }
            }
        }

        // 4. Response dengan format mata uang user
        $formattedAmount = Auth::user()->formatCurrency($validated['amount']);
        $typeLabel = ($validated['type'] === 'income' ? 'Pemasukan' : 'Pengeluaran');

        $message = "Transaksi $typeLabel sebesar $formattedAmount berhasil dicatat!";

        return redirect()
            ->route('transactions.index')
            ->with('status', $message);
    }

    /**
     * Simpan pembaruan transaksi
     */
    public function update(Request $request, Transaction $transaction)
    {
        // Cek keamanan kepemilikan
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'type' => ['required', 'string', 'in:income,expense'],
            'amount' => ['required', 'numeric', 'min:1'],
            'category_id' => ['required', 'exists:categories,id'],
            'transaction_date' => ['required', 'date', 'before_or_equal:today'],
            'description' => ['required', 'string', 'min:3', 'max:255'],
            'receipt_image' => ['nullable', 'image', 'max:5120'],
        ]);

        // Tetap pertahankan jam lama atau update ke jam sekarang jika tanggal berubah
        // Di sini kita update jamnya mengikuti waktu edit jika ingin presisi
        $validated['transaction_date'] = Carbon::parse($request->transaction_date)
            ->setTimeFrom(now());

        if ($request->hasFile('receipt_image')) {
            if ($transaction->receipt_image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($transaction->receipt_image);
            }
            $path = $request->file('receipt_image')->store('receipts', 'public');
            $validated['receipt_image'] = $path;
        }

        $transaction->update($validated);

        return redirect()->route('transactions.index')->with('status', 'Transaksi berhasil diperbarui!');
    }

    /**
     * Hapus satu transaksi
     */
    public function destroy(Transaction $transaction)
    {
        if ($transaction->user_id === Auth::id()) {
            if ($transaction->receipt_image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($transaction->receipt_image);
            }
            $transaction->delete();
        }

        return redirect()->back()->with('status', 'Transaksi berhasil dihapus!');
    }

    /**
     * Hapus SEMUA transaksi user
     */
    public function deleteAll()
    {
        $user = Auth::user();
        $transactions = Transaction::where('user_id', $user->id)->get();

        foreach ($transactions as $t) {
            if ($t->receipt_image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($t->receipt_image);
            }
            $t->delete();
        }

        return redirect()
            ->back()
            ->with('status', 'Semua data riwayat transaksi kamu telah berhasil dihapus!');
    }

    // Methods untuk view (form)
    // Form create sudah ada di index.blade.php, jadi redirect saja
    public function create()
    {
        return redirect()->route('transactions.index')->with('openForm', true);
    }
    public function edit(Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }
        return view('transactions.edit', compact('transaction'));
    }
}