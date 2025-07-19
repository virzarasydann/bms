<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use App\Models\Bank;
use Yajra\DataTables\Facades\DataTables;
use FPDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LaporanArusKasController extends Controller
{
    public function index(Request $request)
    {
        $dataBank = Bank::all();
        $permissions = HakAksesController::getUserPermissions();
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $rekening = $request->id_bank;

        if ($request->ajax()) {
            // Ambil data pemasukan
            $pemasukan = Pemasukan::with('kategoriTransaksi')
                ->when($bulan, function ($query) use ($bulan) {
                    return $query->whereMonth('tanggal', $bulan);
                })
                ->when($tahun, function ($query) use ($tahun) {
                    return $query->whereYear('tanggal', $tahun);
                })
                ->when($rekening, function ($query) use ($rekening) {
                    return $query->where('id_bank', $rekening);
                })
                ->get()
                ->map(function ($item) {
                    return [
                        'tanggal' => $item->tanggal,
                        'kategori' => $item->kategoriTransaksi->nama_kategori ?? '-',
                        'jenis' => 'Pemasukan',
                        'nominal' => $item->nominal
                    ];
                });

            // Ambil data pengeluaran
            $pengeluaran = Pengeluaran::with('kategoriTransaksi')
                ->when($bulan, function ($query) use ($bulan) {
                    return $query->whereMonth('tanggal', $bulan);
                })
                ->when($tahun, function ($query) use ($tahun) {
                    return $query->whereYear('tanggal', $tahun);
                })
                ->when($rekening, function ($query) use ($rekening) {
                    return $query->where('id_bank', $rekening);
                })
                ->get()
                ->map(function ($item) {
                    return [
                        'tanggal' => $item->tanggal,
                        'kategori' => $item->kategoriTransaksi->nama_kategori ?? '-',
                        'jenis' => 'Pengeluaran',
                        'nominal' => $item->nominal
                    ];
                });

            // Gabungkan dan urutkan berdasarkan tanggal
            $dataGabungan = $pemasukan->merge($pengeluaran)->sortBy('tanggal')->values();

            return DataTables::of($dataGabungan)
                ->addIndexColumn()
                ->make(true);
        }

        $rekeningList = Bank::all();
        $monthList = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $yearList = range(now()->year, now()->year - 3);

        return view('admin.keuangan.laporan_arus_kas.index', compact(
            'permissions',
            'rekeningList',
            'monthList',
            'dataBank',
            'yearList'
        ));
    }

    public function exportPdf(Request $request)
{
    $bulan = $request->bulan;
    $tahun = $request->tahun;
    $rekening = $request->id_bank;

    $pemasukan = Pemasukan::with('kategoriTransaksi')
        ->when($bulan, fn($q) => $q->whereMonth('tanggal', $bulan))
        ->when($tahun, fn($q) => $q->whereYear('tanggal', $tahun))
        ->when($rekening, fn($q) => $q->where('id_bank', $rekening))
        ->get()
        ->map(fn($i) => [
            'tanggal' => $i->tanggal,
            'kategori' => $i->kategoriTransaksi->nama_kategori ?? '-',
            'jenis' => 'Pemasukan',
            'nominal' => $i->nominal
        ]);

    $pengeluaran = Pengeluaran::with('kategoriTransaksi')
        ->when($bulan, fn($q) => $q->whereMonth('tanggal', $bulan))
        ->when($tahun, fn($q) => $q->whereYear('tanggal', $tahun))
        ->when($rekening, fn($q) => $q->where('id_bank', $rekening))
        ->get()
        ->map(fn($i) => [
            'tanggal' => $i->tanggal,
            'kategori' => $i->kategoriTransaksi->nama_kategori ?? '-',
            'jenis' => 'Pengeluaran',
            'nominal' => $i->nominal
        ]);

    $dataGabungan = $pemasukan->merge($pengeluaran)->sortBy('tanggal')->values();

    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Laporan Arus Kas', 0, 1, 'C');

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(40, 8, 'Tanggal', 1);
    $pdf->Cell(50, 8, 'Kategori', 1);
    $pdf->Cell(40, 8, 'Jenis', 1);
    $pdf->Cell(50, 8, 'Nominal', 1);
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 10);
    foreach ($dataGabungan as $row) {
        $pdf->Cell(40, 8, $row['tanggal'], 1);
        $pdf->Cell(50, 8, $row['kategori'], 1);
        $pdf->Cell(40, 8, $row['jenis'], 1);
        $pdf->Cell(50, 8, number_format($row['nominal'], 0, ',', '.'), 1);
        $pdf->Ln();
    }

    $pdf->Output('I', 'Laporan_Arus_Kas.pdf');
    exit;
}


public function exportExcel(Request $request)
{
    $bulan = $request->query('bulan');
    $tahun = $request->query('tahun');
    $id_bank = $request->query('id_bank');

    if (!$id_bank) {
        return redirect()->back()->with('error', 'Bank wajib dipilih.');
    }

    $pemasukan = Pemasukan::with('kategoriTransaksi')->whereMonth('tanggal', $bulan)
        ->whereYear('tanggal', $tahun)
        ->where('id_bank', $id_bank)
        ->get();

    $pengeluaran = Pengeluaran::with('kategoriTransaksi')->whereMonth('tanggal', $bulan)
        ->whereYear('tanggal', $tahun)
        ->where('id_bank', $id_bank)
        ->get();

    // Buat spreadsheet baru
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Header
    $sheet->setCellValue('A1', 'No');
    $sheet->setCellValue('B1', 'Tanggal');
    $sheet->setCellValue('C1', 'Jenis');
    $sheet->setCellValue('D1', 'Kategori');
    $sheet->setCellValue('E1', 'Nominal');

    $rowIndex = 2;
    $no = 1;

    foreach ($pemasukan as $item) {
        $sheet->setCellValue("A{$rowIndex}", $no++);
        $sheet->setCellValue("B{$rowIndex}", $item->tanggal);
        $sheet->setCellValue("C{$rowIndex}", 'Pemasukan');
        $sheet->setCellValue("D{$rowIndex}", $item->kategori->nama_kategori ?? '-');
        $sheet->setCellValue("E{$rowIndex}", $item->nominal);
        $rowIndex++;
    }

    foreach ($pengeluaran as $item) {
        $sheet->setCellValue("A{$rowIndex}", $no++);
        $sheet->setCellValue("B{$rowIndex}", $item->tanggal);
        $sheet->setCellValue("C{$rowIndex}", 'Pengeluaran');
        $sheet->setCellValue("D{$rowIndex}", $item->kategori->nama_kategori ?? '-');
        $sheet->setCellValue("E{$rowIndex}", -$item->nominal); // minus
        $rowIndex++;
    }

    // Download response
    $filename = "Laporan_Arus_Kas_{$bulan}_{$tahun}.xlsx";
    $writer = new Xlsx($spreadsheet);

    // Output ke browser
    return response()->streamDownload(function () use ($writer) {
        $writer->save('php://output');
    }, $filename, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ]);
}


}
