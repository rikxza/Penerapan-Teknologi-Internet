<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Budget milik user
            $table->foreignId('category_id')->constrained()->onDelete('cascade'); // Budget untuk kategori apa
            $table->decimal('amount', 10, 2); // Jumlah budget (misal: 5.000.000)
            $table->string('period')->default('monthly');
            $table->date('start_date'); // Tanggal mulai periode budget
            $table->date('end_date'); // Tanggal akhir periode budget (untuk budget bulanan)
            $table->timestamps();

            // Budget hanya boleh unik per user, per kategori, dan per periode
            $table->unique(['user_id', 'category_id', 'start_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
