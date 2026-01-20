<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Fungsi global format_money
        \Illuminate\Support\Facades\Blade::directive('currency', function ($expression) {
            return "<?php 
                echo (auth()->user()->currency ?? 'IDR') == 'USD' 
                    ? '$' . number_format($expression, 2, '.', ',') 
                    : 'Rp ' . number_format($expression, 0, ',', '.'); 
            ?>";
        });
    }
}
