<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    DashboardController,
    TransactionController,
    CategoryController,
    BudgetController,
    ProfileController,
    AiController,
    ReportController,
    AdminController,
    TicketController,
    AdminTicketController
};

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['id', 'en'])) {
        session(['locale' => $locale]);
    }
    return back();
})->name('lang.switch');

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Notifications
    Route::post('/notifications/mark-read', function () {
        Illuminate\Support\Facades\Auth::user()->unreadNotifications->markAsRead();
        return back();
    })->name('notifications.markRead');

    // FITUR AI (Gue pastiin name('ai.chat') ada di sini)
    Route::get('/ai-chat', function () {
        return view('ai-chat');
    })->name('ai.chat');
    Route::post('/ai-chat/send', [AiController::class, 'chat'])->name('ai.chat.send');
    Route::get('/ai/insight', [AiController::class, 'getInsight'])->name('ai.insight');
    Route::get('/ai/forecast', [AiController::class, 'getForecast'])->name('ai.forecast');

    // SCAN STRUK (Receipt Scanning dengan OpenAI Vision)
    Route::get('/scan-receipt', function () {
        return view('scan-receipt');
    })->name('scan.receipt');
    Route::post('/scan-receipt/analyze', [AiController::class, 'scanReceipt'])->name('scan.receipt.analyze');
    Route::post('/scan-receipt/store', [AiController::class, 'storeReceipt'])->name('scan.receipt.store');

    // EXPORT REPORT (UC-08)
    Route::get('/report/export-csv', [ReportController::class, 'exportCsv'])->name('report.export.csv');
    Route::get('/report/export-excel', [ReportController::class, 'exportExcel'])->name('report.export.excel');
    Route::get('/report/export-pdf', [ReportController::class, 'exportPdf'])->name('report.export.pdf');

    // Transactions
    Route::delete('/transactions/delete-all', [TransactionController::class, 'deleteAll'])->name('transactions.deleteAll');

    // Resources UTAMA
    Route::resources([
        'transactions' => TransactionController::class,
        'categories' => CategoryController::class,
        'budgeting' => BudgetController::class,
        // 'tickets' => TicketController::class,
    ], ['except' => ['show']]);

    // Profile
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
        Route::get('/profile/export', 'exportData')->name('profile.export');
    });
});

// ADMIN ROUTES (UC-13, 14, 15)
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users/{user}/toggle-admin', [AdminController::class, 'toggleAdmin'])->name('users.toggleAdmin');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
    Route::get('/logs', [AdminController::class, 'logs'])->name('logs');

    // Support Tickets (HIDDEN)
    // Route::get('/tickets', [AdminTicketController::class, 'index'])->name('tickets.index');
    // Route::post('/tickets/{ticket}/reply', [AdminTicketController::class, 'reply'])->name('tickets.reply');
    // Route::post('/tickets/{ticket}/close', [AdminTicketController::class, 'close'])->name('tickets.close');
});

Route::get('/fix-names', function () {
    \App\Models\Transaction::where('description', 'LIKE', 'Add Budget:%')
        ->update(['description' => \DB::raw("REPLACE(description, 'Add Budget:', 'Alokasi Budget :')")]);
    return "Names fixed!";
});

require __DIR__ . '/auth.php';