<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Export transactions to CSV
     */
    public function exportCsv(Request $request)
    {
        $user = Auth::user();
        $period = $request->input('period');

        // Parse period or use current month
        if ($period && preg_match('/^(\d{4})-(\d{2})$/', $period, $matches)) {
            $year = (int) $matches[1];
            $month = (int) $matches[2];
        } else {
            $month = Carbon::now()->month;
            $year = Carbon::now()->year;
        }

        $transactions = Transaction::where('user_id', $user->id)
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year)
            ->with('category')
            ->orderBy('transaction_date', 'desc')
            ->get();

        $filename = "laporan_keuangan_{$year}_{$month}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($transactions, $user, $month, $year) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header info
            fputcsv($file, ['LAPORAN KEUANGAN - ' . $user->name]);
            fputcsv($file, ['Periode: ' . Carbon::create($year, $month)->translatedFormat('F Y')]);
            fputcsv($file, ['Diekspor pada: ' . Carbon::now()->format('d/m/Y H:i')]);
            fputcsv($file, []);

            // Column headers
            fputcsv($file, ['Tanggal', 'Tipe', 'Kategori', 'Deskripsi', 'Jumlah']);

            $totalIncome = 0;
            $totalExpense = 0;

            foreach ($transactions as $t) {
                $amount = $t->amount;
                if ($t->type === 'income') {
                    $totalIncome += $amount;
                } else {
                    $totalExpense += $amount;
                }

                fputcsv($file, [
                    Carbon::parse($t->transaction_date)->format('d/m/Y'),
                    $t->type === 'income' ? 'Pemasukan' : 'Pengeluaran',
                    $t->category->name ?? 'Tanpa Kategori',
                    $t->description,
                    $t->type === 'income' ? $amount : -$amount,
                ]);
            }

            // Summary
            fputcsv($file, []);
            fputcsv($file, ['RINGKASAN']);
            fputcsv($file, ['Total Pemasukan', '', '', '', $totalIncome]);
            fputcsv($file, ['Total Pengeluaran', '', '', '', $totalExpense]);
            fputcsv($file, ['Saldo Bersih', '', '', '', $totalIncome - $totalExpense]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export transactions to PDF
     */
    public function exportPdf(Request $request)
    {
        $user = Auth::user();
        $period = $request->input('period');

        // Parse period or use current month
        if ($period && preg_match('/^(\d{4})-(\d{2})$/', $period, $matches)) {
            $year = (int) $matches[1];
            $month = (int) $matches[2];
        } else {
            $month = Carbon::now()->month;
            $year = Carbon::now()->year;
        }

        $transactions = Transaction::where('user_id', $user->id)
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year)
            ->with('category')
            ->orderBy('transaction_date', 'desc')
            ->get();

        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');

        $data = [
            'user' => $user,
            'transactions' => $transactions,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'netBalance' => $totalIncome - $totalExpense,
            'periodLabel' => Carbon::create($year, $month)->translatedFormat('F Y'),
            'exportDate' => Carbon::now()->format('d/m/Y H:i'),
        ];

        $pdf = Pdf::loadView('reports.pdf', $data);
        $pdf->setPaper('A4', 'portrait');

        $filename = "laporan_keuangan_{$year}_{$month}.pdf";

        return $pdf->download($filename);
    }
    /**
     * Export transactions to Excel (HTML Table method)
     */
    public function exportExcel(Request $request)
    {
        $user = Auth::user();
        $period = $request->input('period');

        // Parse period or use current month
        if ($period && preg_match('/^(\d{4})-(\d{2})$/', $period, $matches)) {
            $year = (int) $matches[1];
            $month = (int) $matches[2];
        } else {
            $month = Carbon::now()->month;
            $year = Carbon::now()->year;
        }

        $transactions = Transaction::where('user_id', $user->id)
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year)
            ->with('category')
            ->orderBy('transaction_date', 'desc')
            ->get();

        $filename = "laporan_keuangan_{$year}_{$month}.xls";

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        // Hitung total
        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');

        $periodLabel = Carbon::create($year, $month)->translatedFormat('F Y');
        $exportDate = Carbon::now()->format('d/m/Y H:i');

        $callback = function () use ($transactions, $user, $periodLabel, $exportDate, $totalIncome, $totalExpense) {
            echo "<html>";
            echo "<head><meta http-equiv='Content-Type' content='text/html; charset=utf-8' /></head>";
            echo "<body>";

            // JUDUL
            echo "<h2>LAPORAN KEUANGAN - " . strtoupper($user->name) . "</h2>";
            echo "<p>Periode: <strong>$periodLabel</strong><br>Diekspor pada: $exportDate</p>";
            echo "<br>";

            // TABEL DATA
            echo "<table border='1' cellpadding='5' cellspacing='0'>";
            echo "<tr style='background-color: #f2f2f2; font-weight: bold;'>
                    <th>Tanggal</th>
                    <th>Tipe</th>
                    <th>Kategori</th>
                    <th>Deskripsi</th>
                    <th>Jumlah</th>
                  </tr>";

            foreach ($transactions as $t) {
                $color = $t->type === 'income' ? 'green' : 'red';
                $amount = $t->amount;
                // Format amount output
                $amountStr = $t->type === 'income' ? $amount : -$amount;

                echo "<tr>";
                echo "<td>" . Carbon::parse($t->transaction_date)->format('d/m/Y') . "</td>";
                echo "<td>" . ($t->type === 'income' ? 'Pemasukan' : 'Pengeluaran') . "</td>";
                echo "<td>" . ($t->category->name ?? 'Tanpa Kategori') . "</td>";
                echo "<td>" . $t->description . "</td>";
                echo "<td style='color: $color;'>" . $amountStr . "</td>";
                echo "</tr>";
            }

            // SUMMARY ROW
            echo "<tr><td colspan='5'></td></tr>";
            echo "<tr style='background-color: #e6f7ff; font-weight: bold;'> <td colspan='4'>Total Pemasukan</td> <td>$totalIncome</td> </tr>";
            echo "<tr style='background-color: #fff1f0; font-weight: bold;'> <td colspan='4'>Total Pengeluaran</td> <td>$totalExpense</td> </tr>";
            echo "<tr style='background-color: #f6ffed; font-weight: bold;'> <td colspan='4'>Saldo Bersih</td> <td>" . ($totalIncome - $totalExpense) . "</td> </tr>";

            echo "</table>";
            echo "</body>";
            echo "</html>";
        };

        return response()->stream($callback, 200, $headers);
    }
}
