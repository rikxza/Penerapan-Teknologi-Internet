<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan - {{ $user->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 2px solid #10b981;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 20px;
            color: #10b981;
            margin-bottom: 5px;
        }

        .header .subtitle {
            color: #64748b;
            font-size: 12px;
        }

        .meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 10px;
            background: #f1f5f9;
            border-radius: 5px;
        }

        .meta-item {
            text-align: center;
        }

        .meta-label {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
        }

        .meta-value {
            font-size: 12px;
            font-weight: bold;
            color: #1e293b;
        }

        .summary-cards {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .summary-card {
            display: table-cell;
            width: 33.33%;
            padding: 10px;
            text-align: center;
            border: 1px solid #e2e8f0;
        }

        .summary-card.income {
            border-top: 3px solid #10b981;
        }

        .summary-card.expense {
            border-top: 3px solid #ef4444;
        }

        .summary-card.balance {
            border-top: 3px solid #3b82f6;
        }

        .summary-label {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .summary-value {
            font-size: 14px;
            font-weight: bold;
        }

        .summary-value.income {
            color: #10b981;
        }

        .summary-value.expense {
            color: #ef4444;
        }

        .summary-value.balance {
            color: #3b82f6;
        }

        table.transactions {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table.transactions th {
            background: #10b981;
            color: white;
            padding: 8px 5px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
        }

        table.transactions td {
            padding: 8px 5px;
            border-bottom: 1px solid #e2e8f0;
        }

        table.transactions tr:nth-child(even) {
            background: #f8fafc;
        }

        .type-income {
            color: #10b981;
            font-weight: bold;
        }

        .type-expense {
            color: #ef4444;
            font-weight: bold;
        }

        .amount {
            text-align: right;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            color: #94a3b8;
            font-size: 9px;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>💰 LAPORAN KEUANGAN</h1>
        <div class="subtitle">{{ $user->name }} • {{ $periodLabel }}</div>
    </div>

    <div class="meta">
        <div class="meta-item">
            <div class="meta-label">Nama</div>
            <div class="meta-value">{{ $user->name }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-label">Periode</div>
            <div class="meta-value">{{ $periodLabel }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-label">Diekspor</div>
            <div class="meta-value">{{ $exportDate }}</div>
        </div>
    </div>

    <div class="summary-cards">
        <div class="summary-card income">
            <div class="summary-label">Total Pemasukan</div>
            <div class="summary-value income">Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
        </div>
        <div class="summary-card expense">
            <div class="summary-label">Total Pengeluaran</div>
            <div class="summary-value expense">Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
        </div>
        <div class="summary-card balance">
            <div class="summary-label">Saldo Bersih</div>
            <div class="summary-value balance">Rp {{ number_format($netBalance, 0, ',', '.') }}</div>
        </div>
    </div>

    <h3 style="margin-bottom: 10px; color: #475569;">📋 Rincian Transaksi ({{ $transactions->count() }} transaksi)</h3>

    @if($transactions->count() > 0)
        <table class="transactions">
            <thead>
                <tr>
                    <th style="width: 15%">Tanggal</th>
                    <th style="width: 12%">Tipe</th>
                    <th style="width: 18%">Kategori</th>
                    <th style="width: 35%">Deskripsi</th>
                    <th style="width: 20%; text-align: right;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $t)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($t->transaction_date)->format('d/m/Y') }}</td>
                        <td class="type-{{ $t->type }}">
                            {{ $t->type === 'income' ? '↑ Masuk' : '↓ Keluar' }}
                        </td>
                        <td>{{ $t->category->name ?? 'Tanpa Kategori' }}</td>
                        <td>{{ $t->description }}</td>
                        <td class="amount type-{{ $t->type }}">
                            {{ $t->type === 'income' ? '+' : '-' }}Rp {{ number_format($t->amount, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="text-align: center; color: #94a3b8; padding: 30px;">Tidak ada transaksi pada periode ini.</p>
    @endif

    <div class="footer">
        <p>Dokumen ini digenerate otomatis oleh Moneygement</p>
        <p>© {{ date('Y') }} Moneygement - Kelola Keuanganmu dengan Cerdas</p>
    </div>
</body>

</html>