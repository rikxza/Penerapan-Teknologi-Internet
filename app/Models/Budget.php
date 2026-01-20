<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;
    
    // Aktifkan mass assignment untuk field yang boleh diisi dari form
    protected $fillable = [
        'user_id',
        'category_id',
        'amount',
        'period',
        'start_date',
        'end_date',
    ];

    // Definisikan relasi ke Kategori
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Definisikan relasi ke User (opsional, tapi bagus untuk konsistensi)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}