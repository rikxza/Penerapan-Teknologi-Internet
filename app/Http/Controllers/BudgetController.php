<?php

namespace App\Http\Controllers;

use App\Models\{Budget, Category, Transaction};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BudgetController extends Controller
{
    /**
     * =====================================
     * INDEX – HALAMAN BUDGET
     * =====================================
     */
    public function index()
    {
        $user = Auth::user();

        // Kategori expense (global + user)
        $categories = Category::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
                ->orWhereNull('user_id');
        })
            ->where('type', 'expense')
            ->orderBy('name')
            ->get();

        // Budget user
        $budgets = Budget::where('user_id', $user->id)
            ->with('category')
            ->orderBy('created_at', 'desc')
            ->get();

        /**
         * HITUNG PROGRESS (SPENT MURNI)
         * Abaikan transaksi "Budget:"
         */
        foreach ($budgets as $budget) {
            $realized = Transaction::where('user_id', $user->id)
                ->where('category_id', $budget->category_id)
                ->where('type', 'expense')
                ->where('description', 'NOT LIKE', '%Budget:%')
                ->whereBetween('transaction_date', [
                    $budget->start_date . ' 00:00:00',
                    $budget->end_date . ' 23:59:59',
                ])
                ->sum('amount');

            $budget->realized_amount = $realized;
            $budget->remaining = $budget->amount - $realized;
            $budget->percentage = $budget->amount > 0
                ? round(($realized / $budget->amount) * 100, 1)
                : 0;
        }

        return view('budgeting.index', compact('categories', 'budgets'));
    }

    /**
     * =====================================
     * STORE – ADD / UPDATE BUDGET
     * =====================================
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'new_category_name' => 'nullable|string|max:100',
            'amount' => 'required|numeric|min:1000',
            'period' => 'required|in:monthly,weekly',
        ]);

        /**
         * HANDLE CATEGORY
         */
        if ($request->filled('new_category_name')) {
            $category = Category::where('user_id', $user->id)
                ->whereRaw('LOWER(name) = ?', [strtolower($request->new_category_name)])
                ->where('type', 'expense')
                ->first();

            if (!$category) {
                $category = Category::create([
                    'user_id' => $user->id,
                    'name' => ucfirst($request->new_category_name),
                    'type' => 'expense',
                ]);
            }

            $categoryId = $category->id;
        } else {
            $categoryId = $validated['category_id'];
        }

        /**
         * PERIOD
         */
        $start = $validated['period'] === 'weekly'
            ? now()->startOfWeek()
            : now()->startOfMonth();

        $end = $validated['period'] === 'weekly'
            ? now()->endOfWeek()
            : now()->endOfMonth();

        /**
         * CEK BUDGET LAMA
         */
        $existingBudget = Budget::where('user_id', $user->id)
            ->where('category_id', $categoryId)
            ->where('start_date', $start->format('Y-m-d'))
            ->first();

        /**
         * FIX: CEGAH OVERWRITE / MERGE SILENT
         * Jika budget sudah ada, tolak dan minta user pakai fitur Edit (CRUD Options)
         */
        if ($existingBudget) {
            return redirect()->back()->with('error_budget', 'Budget untuk kategori ini sudah ada! Silakan gunakan opsi Edit (Ikon Pensil) atau Hapus pada kartu budget.');
        }

        $newAmount = $validated['amount'];
        // $oldAmount tidak lagi relevan di store karena selalu create baru
        $diff = $newAmount; // Karena baru, diff adalah full amount

        /**
         * SIMPAN BUDGET
         */
        $budget = Budget::updateOrCreate(
            [
                'user_id' => $user->id,
                'category_id' => $categoryId,
                'start_date' => $start->format('Y-m-d'),
            ],
            [
                'amount' => $newAmount,
                'period' => $validated['period'],
                'end_date' => $end->format('Y-m-d'),
            ]
        );

        /**
         * CATAT ALOKASI → TRANSACTION
         * Logika: Jika budget baru (oldAmount == 0), deskripsi "Add Budget:"
         * Jika update (oldAmount > 0), deskripsi "Top Up / Pengurangan"
         */
        /**
         * CATAT ALOKASI → TRANSACTION
         * Karena ini create baru, deskripsi pasti "Alokasi Budget"
         */
        if ($newAmount > 0) {
            $categoryName = Category::find($categoryId)->name ?? 'Kategori';

            Transaction::create([
                'user_id' => $user->id,
                'category_id' => $categoryId,
                'type' => 'expense',
                'amount' => $newAmount,
                'transaction_date' => now(),
                'description' => 'Alokasi Budget: ' . $categoryName,
            ]);
        }

        return redirect()->back()->with('status', 'Budget berhasil dibuat!');
    }

    /**
     * =====================================
     * UPDATE – EDIT VIA ICON PENSIL
     * =====================================
     */
    public function update(Request $request, $id)
    {
        $budget = Budget::findOrFail($id);

        if ($budget->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1000',
        ]);

        $oldAmount = $budget->amount;
        $newAmount = $validated['amount'];
        $diff = $newAmount - $oldAmount;

        if ($diff != 0) {
            Transaction::create([
                'user_id' => auth()->id(),
                'category_id' => $budget->category_id,
                'type' => 'expense',
                'amount' => $diff,
                'transaction_date' => now(),
                'description' => ($diff > 0 ? 'Top Up Budget: ' : 'Pengurangan Budget: ')
                    . ($budget->category->name ?? 'Kategori'),
            ]);
        }

        $budget->update(['amount' => $newAmount]);

        return redirect()->back()->with('status', 'Budget diperbarui!');
    }

    /**
     * =====================================
     * DELETE
     * =====================================
     */
    public function destroy($id)
    {
        $budget = Budget::findOrFail($id);

        if ($budget->user_id === auth()->id()) {
            // Reverse the budget allocation
            Transaction::create([
                'user_id' => auth()->id(),
                'category_id' => $budget->category_id,
                'type' => 'expense',
                'amount' => -$budget->amount, // Negative amount to subtract
                'transaction_date' => now(),
                'description' => 'Hapus Budget: ' . ($budget->category->name ?? 'Kategori'),
            ]);

            $budget->delete();
        }

        return redirect()->back()->with('status', 'Budget dihapus!');
    }
}