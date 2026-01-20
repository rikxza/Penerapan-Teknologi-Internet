<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\AiController;
use Illuminate\Http\Request;

// ✅ TANPA AUTH (dashboard data)
Route::get('/dashboard/data', [ApiController::class, 'dashboardData'])
    ->name('api.dashboard.data');

Route::middleware('auth:sanctum')->get('/ai/insight', [AiController::class, 'getInsight']);

// ✅ AUTH (AI & user)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/ai/analyze', [AiController::class, 'analyze'])
        ->name('api.ai.analyze');

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});