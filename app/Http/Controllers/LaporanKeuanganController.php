<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengeluaran;
use App\Models\PengeluaranDetail;
use App\Models\Pemasukan;
use App\Models\KategoriPengeluaran;
use App\Models\KategoriPenerimaan;
use FPDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Response;
class LaporanKeuanganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $permissions = HakAksesController::getUserPermissions();
        return view('admin.laporan_keuangan.index', compact('permissions'));
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
        $tipe = $request->input('jenis_kategori');
        
        $data = [];
        if ($tipe == 'Pemasukan') {
            $data = Pemasukan::with('kategori')
            ->where('kategori_penerimaan', $request->kategori)
            ->whereMonth('tanggal_pemasukan', $request->bulan)
            ->whereYear('tanggal_pemasukan', $request->tahun)
            ->get();
        


        } elseif ($tipe == 'Pengeluaran') {
            $data = PengeluaranDetail::with(['kategori', 'pengeluaran'])
            ->where('kategori_pengeluaran_id', $request->kategori)
            ->whereHas('pengeluaran', function ($query) use ($request) {
                $query->whereMonth('tanggal_pengeluaran', $request->bulan)
                      ->whereYear('tanggal_pengeluaran', $request->tahun);
            })
            ->get();
        }

        return response()->json(['message' => 'success', 'data' => $data]);
    }

    public function exportPdf(Request $request)
    {
        $tipe = $request->input('hidden_jenis_kategori');
        $kategori = $request->input('hidden_kategori');
        $bulan = $request->input('hidden_bulan');
        $tahun = $request->input('hidden_tahun');

        $data = [];

        if ($tipe === 'Pemasukan') {
            $data = Pemasukan::with('kategori')
                ->where('kategori_penerimaan', $kategori)
                ->whereMonth('tanggal_pemasukan', $bulan)
                ->whereYear('tanggal_pemasukan', $tahun)
                ->get();
        } elseif ($tipe === 'Pengeluaran') {
            $data = PengeluaranDetail::with(['kategori', 'pengeluaran'])
                ->where('kategori_pengeluaran_id', $kategori)
                ->whereHas('pengeluaran', function ($q) use ($bulan, $tahun) {
                    $q->whereMonth('tanggal_pengeluaran', $bulan)
                        ->whereYear('tanggal_pengeluaran', $tahun);
                })
                ->get();
        }

        $pdf = new \FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, 'Laporan ' . ucfirst($tipe), 0, 1, 'C');
        $pdf->Ln(5);

        $pdf->SetFont('Arial', 'B', 10);

        if ($tipe === 'Pemasukan') {
            // Header
            $pdf->Cell(10, 10, 'No', 1);
            $pdf->Cell(30, 10, 'Tanggal', 1);
            $pdf->Cell(40, 10, 'No Transaksi', 1);
            $pdf->Cell(30, 10, 'Nominal', 1);
            $pdf->Cell(30, 10, 'Tipe', 1);
            $pdf->Cell(50, 10, 'Deskripsi', 1);
            $pdf->Ln();

            // Data
            $pdf->SetFont('Arial', '', 10);
            foreach ($data as $i => $item) {
                $pdf->Cell(10, 10, $i + 1, 1);
                $pdf->Cell(30, 10, $item->tanggal_pemasukan ?? '-', 1);
                $pdf->Cell(40, 10, $item->no_transaksi ?? '-', 1);
                $pdf->Cell(30, 10, number_format($item->nominal, 0, ',', '.'), 1);
                $pdf->Cell(30, 10, $item->tipe ?? '-', 1);
                $pdf->Cell(50, 10, $item->deskripsi ?? '-', 1);
                $pdf->Ln();
            }

        } else {
            // Header
            $pdf->Cell(10, 10, 'No', 1);
            $pdf->Cell(30, 10, 'Tanggal', 1);
            $pdf->Cell(40, 10, 'No Pengeluaran', 1);
            $pdf->Cell(30, 10, 'Jumlah', 1);
            $pdf->Cell(30, 10, 'Tipe', 1);
            $pdf->Cell(50, 10, 'Deskripsi', 1);
            $pdf->Ln();

            // Data
            $pdf->SetFont('Arial', '', 10);
            foreach ($data as $i => $item) {
                $p = $item->pengeluaran;
                $pdf->Cell(10, 10, $i + 1, 1);
                $pdf->Cell(30, 10, $p->tanggal_pengeluaran ?? '-', 1);
                $pdf->Cell(40, 10, $p->no_pengeluaran ?? '-', 1);
                $pdf->Cell(30, 10, number_format($item->nominal, 0, ',', '.'), 1);
                $pdf->Cell(30, 10, $p->tipe ?? '-', 1);
                $pdf->Cell(50, 10, $p->deskripsi ?? '-', 1);
                $pdf->Ln();
            }
        }

        if (count($data) === 0) {
            $pdf->Cell(0, 10, 'Data tidak tersedia untuk periode ini.', 1, 1, 'C');
        }

        return response($pdf->Output('S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="laporan-keuangan.pdf"');
    }



    public function exportExcel(Request $request)
    {
        $jenis = $request->input('excel_jenis_kategori');
        $kategori = $request->input('excel_kategori');
        $bulan = $request->input('excel_bulan');
        $tahun = $request->input('excel_tahun');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        if ($jenis === 'Pemasukan') {
            $sheet->fromArray(['No', 'Tanggal', 'No Transaksi', 'Nominal', 'Tipe', 'Deskripsi'], NULL, 'A1');

            $data = \App\Models\Pemasukan::where('kategori_penerimaan', $kategori)
                ->whereMonth('tanggal_pemasukan', $bulan)
                ->whereYear('tanggal_pemasukan', $tahun)
                ->get();

            foreach ($data as $i => $item) {
                $sheet->fromArray([
                    $i + 1,
                    $item->tanggal_pemasukan,
                    $item->no_transaksi,
                    $item->nominal,
                    $item->tipe,
                    $item->deskripsi
                ], NULL, 'A' . ($i + 2));
            }
        } else {
            $sheet->fromArray(['No', 'Tanggal', 'No Pengeluaran', 'Jumlah', 'Tipe', 'Deskripsi'], NULL, 'A1');

            $data = \App\Models\PengeluaranDetail::with('pengeluaran')
                ->where('kategori_pengeluaran_id', $kategori)
                ->whereHas('pengeluaran', function ($q) use ($bulan, $tahun) {
                    $q->whereMonth('tanggal_pengeluaran', $bulan)
                    ->whereYear('tanggal_pengeluaran', $tahun);
                })
                ->get();

            foreach ($data as $i => $item) {
                $sheet->fromArray([
                    $i + 1,
                    $item->pengeluaran->tanggal_pengeluaran ?? '-',
                    $item->pengeluaran->no_pengeluaran ?? '-',
                    $item->nominal,
                    $item->pengeluaran->tipe ?? '-',
                    $item->pengeluaran->deskripsi ?? '-'
                ], NULL, 'A' . ($i + 2));
            }
        }

        // Output response
        $writer = new Xlsx($spreadsheet);
        $filename = 'laporan-keuangan.xlsx';

        // Simpan ke temporary memory
        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }


    /**
     * Display the specified resource.
     */
    public function showJenis(Request $request)
    {
        $tipe = $request->input('jenis_kategori');
        
        $data = [];
        if ($tipe == 'Pemasukan') {
           $data = KategoriPenerimaan::all();


        } elseif ($tipe == 'Pengeluaran') {
           $data = KategoriPengeluaran::all();
        }

        return response()->json(['message' => 'success', 'data' => $data]);
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
