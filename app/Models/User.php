<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'currency',
        'avatar',      // Tambahkan ini biar bisa upload foto
        'avatar_type', // Tambahkan ini juga
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // --- RELASI ---

    /**
     * Hubungkan user ke transaksi miliknya.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Hubungkan user ke budget miliknya.
     */
    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }

    // --- ACCESSORS (Logika Otomatis) ---

    /**
     * MENGHITUNG SALDO SECARA REAL-TIME
     * Panggil di Blade dengan: {{ Auth::user()->balance }}
     */
    public function getBalanceAttribute(): float
    {
        $income = $this->transactions()->where('type', 'income')->sum('amount');
        $expense = $this->transactions()->where('type', 'expense')->sum('amount');
        
        return (float) ($income - $expense);
    }

    /**
     * Mendapatkan simbol mata uang.
     */
    public function getSymbolAttribute(): string
    {
        return $this->currency === 'USD' ? '$' : 'Rp';
    }

    // --- HELPERS ---

    /**
     * Format angka ke mata uang yang dipilih.
     */
    public function formatCurrency($value): string
    {
        $currency = $this->currency ?: 'IDR';

        if ($currency === 'USD') {
            return '$ ' . number_format($value, 2, '.', ',');
        }

        return 'Rp ' . number_format($value, 0, ',', '.');
    }
}