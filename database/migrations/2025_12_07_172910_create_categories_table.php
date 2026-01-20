<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            // Kolom wajib untuk kategori:
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); // Kategori opsional bisa global
            $table->string('name')->unique(); // Nama kategori (harus unik)
            $table->string('type')->default('expense'); // Tipe: 'income' atau 'expense'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};