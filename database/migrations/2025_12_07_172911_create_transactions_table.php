<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Link ke tabel users
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null'); // Link ke tabel categories
            $table->decimal('amount', 10, 2);
            $table->enum('type', ['income', 'expense']); // Jenis transaksi
            $table->string('description')->nullable();
            $table->date('transaction_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};