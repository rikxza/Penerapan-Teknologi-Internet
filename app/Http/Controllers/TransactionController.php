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

        // Get selected period from request or use current month
        $period = $request->input('period');
        if ($period && preg_match('/^(\d{4})-(\d{2})$/', $period, $matches)) {
            $selectedYear = (int) $matches[1];
            $selectedMonth = (int) $matches[2];
        } else {
            $selectedMonth = Carbon::now()->month;
            $selectedYear = Carbon::now()->year;
        }
        $filterDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1);

        // Ambil kategori milik user atau kategori default (null)
        $categories = Category::where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->orWhereNull('user_id');
        })
            ->get()
            ->groupBy('type');

        // Ambil transaksi user (pagination 10 data) - filtered by selected month
        // Diurutkan berdasarkan transaction_date DESC agar yang baru (berdasarkan jam) di atas
        $transactions = Transaction::where('user_id', $user->id)
            ->whereMonth('transaction_date', $selectedMonth)
            ->whereYear('transaction_date', $selectedYear)
            ->with('category')
            ->orderBy('transaction_date', 'desc')
            ->paginate(10)
            ->appends(['period' => sprintf('%d-%02d', $selectedYear, $selectedMonth)]);

        // Statistik singkat
        $totalTransactions = Transaction::where('user_id', $user->id)->count();

        // Monthly stats for statistics bar (filtered by selected month)
        $monthlyIncome = Transaction::where('user_id', $user->id)
            ->where('type', 'income')
            ->whereMonth('transaction_date', $selectedMonth)
            ->whereYear('transaction_date', $selectedYear)
            ->sum('amount');

        $monthlyExpense = Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereMonth('transaction_date', $selectedMonth)
            ->whereYear('transaction_date', $selectedYear)
            ->sum('amount');

        $monthlyTransactionCount = Transaction::where('user_id', $user->id)
            ->whereMonth('transaction_date', $selectedMonth)
            ->whereYear('transaction_date', $selectedYear)
            ->count();

        // Budget aktif (yang belum expired)
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
        ]);
    }

    /**
     * Simpan transaksi baru
     */
    public function store(Request $request)
    {
        // 1. Validasi Input (Hanya aturan validasi di dalam sini)
        $validated = $request->validate([
            'type' => ['required', 'string', 'in:income,expense'],
            'amount' => ['required', 'numeric', 'min:1'],
            'category_id' => ['required', 'exists:categories,id'],
            'transaction_date' => ['required', 'date', 'before_or_equal:today'],
            'description' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        // 2. Olah data setelah validasi sukses
        $validated['user_id'] = Auth::id();

        /** * FIX JAM: Gabungkan tanggal dari input dengan jam menit detik saat ini 
         * agar jamnya tidak 00:00.
         */
        $validated['transaction_date'] = Carbon::parse($request->transaction_date)
            ->setTimeFrom(now());

        // 3. Eksekusi simpan ke database
        Transaction::create($validated);

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
        ]);

        // Tetap pertahankan jam lama atau update ke jam sekarang jika tanggal berubah
        // Di sini kita update jamnya mengikuti waktu edit jika ingin presisi
        $validated['transaction_date'] = Carbon::parse($request->transaction_date)
            ->setTimeFrom(now());

        $transaction->update($validated);

        return redirect()->route('transactions.index')->with('status', 'Transaksi berhasil diperbarui!');
    }

    /**
     * Hapus satu transaksi
     */
    public function destroy(Transaction $transaction)
    {
        if ($transaction->user_id === Auth::id()) {
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
        Transaction::where('user_id', $user->id)->delete();

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