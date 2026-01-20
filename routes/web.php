<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    DashboardController, TransactionController, CategoryController, 
    BudgetController, ProfileController, AiController
};

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // FITUR AI (Gue pastiin name('ai.chat') ada di sini)
    Route::get('/ai-chat', function () { return view('ai-chat'); })->name('ai.chat'); 
    Route::post('/ai-chat/send', [AiController::class, 'chat'])->name('ai.chat.send');
    Route::get('/ai/insight', [AiController::class, 'getInsight'])->name('ai.insight');

    // Transactions
    Route::delete('/transactions/delete-all', [TransactionController::class, 'deleteAll'])->name('transactions.deleteAll');
    
    // Resources UTAMA
    Route::resources([
        'transactions' => TransactionController::class,
        'categories'   => CategoryController::class,
        'budgeting'    => BudgetController::class, 
    ], ['except' => ['show']]);

    // Profile
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });
});

require __DIR__.'/auth.php';