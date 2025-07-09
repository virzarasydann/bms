<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengeluaran;
use App\Models\PengeluaranDetail;
use App\Models\PengeluaranDetailSumber;
use App\Models\TutupBuku;
use App\Models\Pemasukan;
use App\Models\KategoriPengeluaran;
use App\Models\KategoriPenerimaan;
use FPDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
class LaporanJurnalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $permissions = HakAksesController::getUserPermissions();
       
        return view('admin.laporan_jurnal.index', compact('permissions'));
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    
     public function store(Request $request)
     {
         $tipe = $request->input('tipe'); // 'Bank' atau 'Kas'
         $bulan = (int) $request->input('bulan');
         $tahun = (int) $request->input('tahun');
     
         if (!in_array($tipe, ['Kas', 'Bank'])) {
             return response()->json([
                 'message' => 'error',
                 'data' => [],
             ]);
         }
     
         // Ambil data tutup buku bulan sebelumnya
         $bulanSebelumnya = $bulan - 1;
         $tahunSebelumnya = $tahun;
     
         if ($bulanSebelumnya < 1) {
             $bulanSebelumnya = 12;
             $tahunSebelumnya -= 1;
         }
     
         // Ambil semua saldo awal berdasarkan tipe dari tutup buku
         $tutupBukuList = TutupBuku::where('tipe', $tipe)
             ->where('bulan', $bulanSebelumnya)
             ->where('tahun', $tahunSebelumnya)
             ->get();
     
         $saldoAwal = $tutupBukuList->sum('saldo');
     
         // Ambil pemasukan
         $pemasukan = Pemasukan::where('tipe', $tipe)
             ->whereMonth('tanggal_pemasukan', $bulan)
             ->whereYear('tanggal_pemasukan', $tahun)
             ->get();
     
         // Ambil pengeluaran detail (berelasi ke pengeluaran)
         $pengeluaranDetail = PengeluaranDetail::whereHas('pengeluaran', function ($query) use ($tipe, $bulan, $tahun) {
             $query->where('tipe', $tipe)
                   ->whereMonth('tanggal_pengeluaran', $bulan)
                   ->whereYear('tanggal_pengeluaran', $tahun);
         })->with('pengeluaran')->get();
     
         return response()->json([
             'message' => 'success',
             'data' => [
                 'pemasukan' => $pemasukan,
                 'pengeluaran' => $pengeluaranDetail,
                 'saldo_awal' => $saldoAwal
             ],
         ]);
     }
     
    

public function exportPdf(Request $request)
{
    $tipe = $request->input('hidden_tipe');
    $bulan = $request->input('hidden_bulan');
    $tahun = $request->input('hidden_tahun');

    $pemasukan = Pemasukan::where('tipe', $tipe)
        ->whereMonth('tanggal_pemasukan', $bulan)
        ->whereYear('tanggal_pemasukan', $tahun)
        ->get();

    $pengeluaran = PengeluaranDetail::whereHas('pengeluaran', function ($query) use ($tipe, $bulan, $tahun) {
        $query->where('tipe', $tipe)
            ->whereMonth('tanggal_pengeluaran', $bulan)
            ->whereYear('tanggal_pengeluaran', $tahun);
    })->with('pengeluaran')->get();

    $pdf = new \FPDF();
    $pdf->AddPage('L'); // Landscape
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Laporan Jurnal - ' . $tipe . " (" . $bulan . "/" . $tahun . ")", 0, 1, 'C');

    // Pemasukan
    $pdf->Ln(4);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 8, 'Pemasukan', 0, 1);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(200, 220, 255);
    $pdf->Cell(10, 8, 'No', 1, 0, 'C', true);
    $pdf->Cell(35, 8, 'Tanggal', 1, 0, 'C', true);
    $pdf->Cell(60, 8, 'No Transaksi', 1, 0, 'C', true);
    $pdf->Cell(30, 8, 'Nominal', 1, 0, 'C', true);
    $pdf->Cell(30, 8, 'Tipe', 1, 0, 'C', true);
    $pdf->Cell(80, 8, 'Deskripsi', 1, 1, 'C', true);

    $pdf->SetFont('Arial', '', 10);
    foreach ($pemasukan as $i => $item) {
        $pdf->Cell(10, 8, $i + 1, 1);
        $pdf->Cell(35, 8, $item->tanggal_pemasukan, 1);
        $pdf->Cell(60, 8, $item->no_transaksi, 1);
        $pdf->Cell(30, 8, number_format($item->nominal), 1, 0, 'R');
        $pdf->Cell(30, 8, $item->tipe, 1);
        $pdf->Cell(80, 8, Str::limit($item->deskripsi, 50), 1);
        $pdf->Ln();
    }

    // Pengeluaran
    $pdf->Ln(4);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 8, 'Pengeluaran', 0, 1);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(255, 230, 200);
    $pdf->Cell(10, 8, 'No', 1, 0, 'C', true);
    $pdf->Cell(35, 8, 'Tanggal', 1, 0, 'C', true);
    $pdf->Cell(60, 8, 'No Pengeluaran', 1, 0, 'C', true);
    $pdf->Cell(30, 8, 'Nominal', 1, 0, 'C', true);
    $pdf->Cell(30, 8, 'Tipe', 1, 0, 'C', true);
    $pdf->Cell(80, 8, 'Deskripsi', 1, 1, 'C', true);

    $pdf->SetFont('Arial', '', 10);
    foreach ($pengeluaran as $i => $item) {
        $pdf->Cell(10, 8, $i + 1, 1);
        $pdf->Cell(35, 8, $item->pengeluaran->tanggal_pengeluaran ?? '-', 1);
        $pdf->Cell(60, 8, $item->pengeluaran->no_pengeluaran ?? '-', 1);
        $pdf->Cell(30, 8, number_format($item->nominal), 1, 0, 'R');
        $pdf->Cell(30, 8, $item->pengeluaran->tipe ?? '-', 1);
        $pdf->Cell(80, 8, Str::limit($item->pengeluaran->deskripsi ?? '-', 50), 1);
        $pdf->Ln();
    }

    $pdf->Output();
    exit;
}


    public function exportExcel(Request $request)
    {
        $tipe = $request->input('excel_tipe');
        $bulan = $request->input('excel_bulan');
        $tahun = $request->input('excel_tahun');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Jurnal');

        $sheet->setCellValue('A1', 'Jenis');
        $sheet->setCellValue('B1', 'Tanggal');
        $sheet->setCellValue('C1', 'Nomor');
        $sheet->setCellValue('D1', 'Nominal');

        $row = 2;

        $pemasukan = Pemasukan::where('tipe', $tipe)
            ->whereMonth('tanggal_pemasukan', $bulan)
            ->whereYear('tanggal_pemasukan', $tahun)
            ->get();

        foreach ($pemasukan as $item) {
            $sheet->setCellValue('A' . $row, 'Pemasukan');
            $sheet->setCellValue('B' . $row, $item->tanggal_pemasukan);
            $sheet->setCellValue('C' . $row, $item->no_transaksi);
            $sheet->setCellValue('D' . $row, $item->nominal);
            $row++;
        }

        $pengeluaran = PengeluaranDetail::whereHas('pengeluaran', function ($query) use ($tipe, $bulan, $tahun) {
            $query->where('tipe', $tipe)
                ->whereMonth('tanggal_pengeluaran', $bulan)
                ->whereYear('tanggal_pengeluaran', $tahun);
        })->with('pengeluaran')->get();

        foreach ($pengeluaran as $item) {
            $sheet->setCellValue('A' . $row, 'Pengeluaran');
            $sheet->setCellValue('B' . $row, $item->pengeluaran->tanggal_pengeluaran);
            $sheet->setCellValue('C' . $row, $item->pengeluaran->no_pengeluaran);
            $sheet->setCellValue('D' . $row, $item->nominal);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'laporan_jurnal_' . $tipe . '_' . $bulan . '_' . $tahun . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
