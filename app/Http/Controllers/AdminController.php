<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Admin Dashboard
     */
    public function index()
    {
        $totalUsers = User::count();
        $newUsersThisMonth = User::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        $totalTransactions = Transaction::count();
        $transactionsThisMonth = Transaction::whereMonth('transaction_date', Carbon::now()->month)
            ->whereYear('transaction_date', Carbon::now()->year)
            ->count();

        $recentUsers = User::orderBy('created_at', 'desc')->take(5)->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'newUsersThisMonth',
            'totalTransactions',
            'transactionsThisMonth',
            'recentUsers'
        ));
    }

    /**
     * List all users
     */
    public function users(Request $request)
    {
        $search = $request->input('search');

        $users = User::when($search, function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        })
            ->withCount('transactions')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.users', compact('users', 'search'));
    }

    /**
     * Toggle user admin status
     */
    public function toggleAdmin(User $user)
    {
        // Prevent self-demotion
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat mengubah status admin diri sendiri.');
        }

        $user->is_admin = !$user->is_admin;
        $user->save();

        $status = $user->is_admin ? 'dijadikan Admin' : 'dicabut status Admin-nya';
        return back()->with('status', "User {$user->name} berhasil {$status}.");
    }

    /**
     * Delete user
     */
    public function destroyUser(User $user)
    {
        // Prevent self-deletion
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $userName = $user->name;
        $user->delete();

        return back()->with('status', "User {$userName} berhasil dihapus.");
    }

    /**
     * System logs (simple version)
     */
    public function logs()
    {
        $logFile = storage_path('logs/laravel.log');
        $logs = [];

        if (file_exists($logFile)) {
            $content = file_get_contents($logFile);
            // Get last 50 lines
            $lines = explode("\n", $content);
            $logs = array_slice($lines, -100);
            $logs = array_reverse($logs);
        }

        return view('admin.logs', compact('logs'));
    }
}
