<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Transaction; 
use App\Models\Budget;      
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Carbon\Carbon;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman pengaturan profil.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        // Hitung Pemasukan (Income)
        $totalIncome = Transaction::where('user_id', $user->id)
                        ->where('type', 'income') // Pastikan di database lo nama kolomnya 'type' dan isinya 'income'
                        ->count();

        // Hitung Pengeluaran (Expense)
        $totalExpense = Transaction::where('user_id', $user->id)
                        ->where('type', 'expense')
                        ->count();

        $activeBudgets = Budget::where('user_id', $user->id)
                                ->where('end_date', '>=', now())
                                ->count();

        $activeDays = Carbon::parse($user->created_at)->diffInDays(now());

        return view('profile.edit', compact('user', 'totalIncome', 'totalExpense', 'activeBudgets', 'activeDays'));
    }

    /**
     * Memperbarui informasi profil (Hanya Nama).
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        // 1. Update Nama
        $user->name = $request->input('name');
        
        // 2. Update Avatar Logic
        $user->avatar_type = $request->input('avatar_type');
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        // 3. Update Currency (Ini yang tadi gagal karena di luar form)
        if ($request->has('currency')) {
            $user->currency = $request->input('currency');
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Hapus akun user.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}