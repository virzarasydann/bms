<?php

namespace App\Http\Controllers;

use App\Models\Pengeluaran;
use App\Models\PengeluaranDetail;
use App\Models\PengeluaranDetailSumber;
use App\Models\Mustahik;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\HakAksesController;
use App\Models\KategoriPengeluaran;
use App\Models\KategoriPenerimaan;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Codedge\Fpdf\Fpdf\Fpdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PengeluaranController extends Controller
{
    public function index(Request $request)
{
    Carbon::setLocale('id');
    $permissions = HakAksesController::getUserPermissions();

    if ($request->ajax()) {
        $opd = Pengeluaran::with(['mustahik', 'detail.kategori'])->orderBy('id', 'asc');

        return DataTables::of($opd)
            ->addIndexColumn()

            ->addColumn('tanggal_pengeluaran', function ($row) {
                return Carbon::parse($row->tanggal_pengeluaran)->translatedFormat('j F Y');
            })

            ->addColumn('id_mustahik', function ($row) {
                return $row->mustahik->nama_lengkap ?? '-';
            })

            ->addColumn('kategori_pengeluaran', function ($row) {
                if ($row->detail->isEmpty()) {
                    return '-';
                }

                $list = '<ul>';
                foreach ($row->detail as $detail) {
                    $list .= '<li>' . ($detail->kategori->nama ?? '-') . ' - Rp. ' . number_format($detail->nominal, 0, ',', '.') . '</li>';
                }
                $list .= '</ul>';

                return $list;
            })

            ->addColumn('lampiran', function ($row) {
                if ($row->lampiran) {
                    $url = asset('pengeluaran/' . $row->lampiran);
                    return '<button class="btn btn-success btn-sm btn-lampiran" data-url="' . $url . '">Lampiran</button>';
                }
                return '-';
            })

            ->editColumn('jumlah', function ($row) {
                return '
                    <div class="d-flex justify-content-between harga-format w-100">
                        <span>Rp.</span>
                        <span>' . number_format($row->jumlah, 0, ',', '.') . '</span>
                    </div>';
            })

            ->addColumn('action', function ($row) use ($permissions): string {
                $editUrl = route('pengeluaran.edit', $row->id);
                $deleteUrl = route('pengeluaran.destroy', $row->id);
                $cetakUrl = route('cetak-kwitansi', $row->id);

                $btn = '<div class="d-flex justify-content-center">';
                if ($permissions['edit']) {
                    $btn .= '<button class="btn btn-primary btn-xs mx-1 edit-btn" data-id="' . e($row->id) . '"
                        data-url="' . e($editUrl) . '" data-toggle="modal" data-target="#modalForm" id="edit">
                        Edit
                    </button>';
                }

                 $btn .= '<a href="' . e($cetakUrl) . '" class="btn btn-success btn-xs mx-1" target="_blank">
                    Cetak
                </a>';

                if ($permissions['hapus']) {
                    $btn .= '<form action="' . e($deleteUrl) . '" method="POST" style="display:inline;">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="delete-button btn btn-danger btn-xs mx-1">
                                Hapus
                            </button>
                        </form>';
                }
                $btn .= '</div>';
                return $btn;
            })

            ->rawColumns(['action', 'jumlah', 'lampiran', 'kategori_pengeluaran'])
            ->make(true);
    }

    return view('admin.pengeluaran.index', compact('permissions'));
}

    public function show($id)
    {
        $data = Pengeluaran::with(['mustahik', 'kategori'])->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }


      public function getMustahik(Request $request)
    {
        $search = $request->get('q');
        $mustahik = Mustahik::where('nama_lengkap', 'like', "%$search%")
            ->select('id', 'nama_lengkap')
            ->get();

        return response()->json($mustahik);
    }

    public function getKategoriPengeluaran()
    {
        $data = KategoriPengeluaran::select('id', 'nama', 'jenis_kategori')->get();

        return response()->json($data);
    }


    public function getKategoriPemasukan()
    {
        $data = KategoriPenerimaan::select('id', 'nama', 'jenis_kategori')->get();

        return response()->json($data);
    }


    public function store(Request $request)
{
    $request->validate([
        'tanggal_pengeluaran' => 'required|date',
        'nama_lengkap' => 'required',
        'nominal' => ['required', 'array', 'min:1'],
        'nominal.*' => ['required', 'regex:/^[\d.]+$/'],
        'kategori_pengeluaran_id' => ['required', 'array', 'min:1'],
        'kategori_pengeluaran_id.*' => ['required', 'integer'],
        'file_upload' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        'tipe_saldo' => 'required',
        'deskripsi' => 'required'
    ]);

    $nominal = array_map(fn($value) => (int) str_replace('.', '', $value), $request->nominal);
    $jumlah = array_sum($nominal);

    $filename = null;
    if ($request->hasFile('file_upload')) {
        $file = $request->file('file_upload');
        $filename = 'lampiran_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('pengeluaran'), $filename);
    }

    $bulanRomawi = [
        '01'=>'I','02'=>'II','03'=>'III','04'=>'IV','05'=>'V','06'=>'VI',
        '07'=>'VII','08'=>'VIII','09'=>'IX','10'=>'X','11'=>'XI','12'=>'XII'
    ][Carbon::parse($request->tanggal_pengeluaran)->format('m')];

    $tahun = Carbon::parse($request->tanggal_pengeluaran)->year;
    $latestKode = Pengeluaran::whereYear('tanggal_pengeluaran', $tahun)
        ->whereNotNull('no_pengeluaran')
        ->orderByDesc('id')
        ->value('no_pengeluaran');

    $nextNumber = $latestKode ? (int) substr($latestKode, 0, 4) + 1 : 1;
    $nomorUrut = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    $kodeMenu = "{$nomorUrut}/D-KEULAZ/{$bulanRomawi}/{$tahun}";

    $pengeluaran = Pengeluaran::create([
        'no_pengeluaran' => $kodeMenu,
        'tanggal_pengeluaran' => $request->tanggal_pengeluaran,
        'id_mustahik' => $request->nama_lengkap,
        'jumlah' => $jumlah,
        'lampiran' => $filename,
        'tipe' => $request->tipe_saldo,
        'deskripsi' => $request->deskripsi
    ]);

    foreach ($request->kategori_pengeluaran_id as $index => $kategoriId) {
        $detail = PengeluaranDetail::create([
            'id_pengeluaran' => $pengeluaran->id,
            'kategori_pengeluaran_id' => $kategoriId,
            'nominal' => $nominal[$index] ?? 0,
        ]);

        // ✔️ Tambah sumber dana per detail
        if (!empty($request->sumber_dana_jenis[$index])) {
            foreach ($request->sumber_dana_jenis[$index] as $i => $sumberId) {
                $sumberNominal = (int) str_replace('.', '', $request->sumber_nominal[$index][$i] ?? 0);
                PengeluaranDetailSumber::create([
                    'pengeluaran_detail_id' => $detail->id,
                    'kategori_pemasukan_id' => $sumberId,
                    'nominal' => $sumberNominal,
                ]);
            }
        }
    }

    return response()->json(['status' => 'success', 'message' => 'Data pengeluaran berhasil disimpan.']);
}


public function showNoPengeluaran(Request $request)
{

    $tanggal = $request->tanggal_pengeluaran
        ? Carbon::parse($request->tanggal_pengeluaran)
        : Carbon::now();


    $bulanRomawi = [
        '01' => 'I', '02' => 'II', '03' => 'III', '04' => 'IV', '05' => 'V', '06' => 'VI',
        '07' => 'VII', '08' => 'VIII', '09' => 'IX', '10' => 'X', '11' => 'XI', '12' => 'XII'
    ][$tanggal->format('m')];

    $tahun = $tanggal->year;

    $latestKode = Pengeluaran::whereYear('tanggal_pengeluaran', $tahun)
        ->whereNotNull('no_pengeluaran')
        ->orderByDesc('id')
        ->value('no_pengeluaran');

    if ($latestKode) {
        $lastNumber = (int) substr($latestKode, 0, 4);
        $nextNumber = $lastNumber + 1;
    } else {
        $nextNumber = 1;
    }

    $nomorUrut = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    $kodeMenu = "{$nomorUrut}/D-KEULAZ/{$bulanRomawi}/{$tahun}";

    return response()->json(['status' => 'success', 'data' => $kodeMenu]);
}


public function edit($id)
{
    $data = Pengeluaran::with([
        'mustahik',
        'detail.kategori',
        'detail.sumberDana.kategoriPemasukan'
    ])->findOrFail($id);

    // Format nominal untuk ditampilkan di input
    $data->detail->transform(function ($item) {
        $item->nominal = number_format($item->nominal, 0, ',', '.');

        // Format sumber dana
        $item->sumber_dana = $item->sumberDana->map(function ($sumber) {
            return [
                'id' => $sumber->id,
                'sumber_dana_id' => $sumber->kategori_pemasukan_id,
                'nama' => $sumber->kategoriPemasukan->nama ?? '-',
                'jenis_kategori' => $sumber->kategoriPemasukan->jenis_kategori ?? '',
                'nominal' => number_format($sumber->nominal, 0, ',', '.'),
            ];
        });

        unset($item->sumberDana); // optional: hapus relasi mentah untuk ringkas

        return $item;
    });

    return response()->json([
        'status' => 'success',
        'data' => $data
    ]);
}


public function update(Request $request, $id)
{
    

    // ==== 3. SUBMIT FORM PENUH ====
    $request->validate([
        'tanggal_pengeluaran' => 'required|date',
        'nama_lengkap' => 'required',
        'file_upload' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        'kategori_pengeluaran_id' => 'required|array',
        'kategori_pengeluaran_id.*' => 'required|integer|exists:kategori_pengeluaran,id',
        'nominal' => 'required|array',
        'nominal.*' => 'required',
        'tipe_saldo' => 'required',
        'deskripsi' => 'required',
        'detail_id' => 'nullable|array',
        'sumber_dana_jenis' => 'nullable|array',
        'sumber_dana_jenis.*' => 'nullable|array',
        'sumber_nominal' => 'nullable|array',
        'sumber_nominal.*' => 'nullable|array',
    ]);

    $data = Pengeluaran::findOrFail($id);
    $filename = $data->lampiran;

    // === Handle file upload ===
    if ($request->hasFile('file_upload')) {
        $oldPath = public_path('pengeluaran/' . $filename);
        if ($filename && file_exists($oldPath)) {
            unlink($oldPath);
        }

        $file = $request->file('file_upload');
        $filename = 'lampiran_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('pengeluaran'), $filename);
    }

    $kategoriIds = $request->kategori_pengeluaran_id;
    $nominals = $request->nominal;
    $detailIds = $request->detail_id ?? [];

    $total = 0;
    $usedDetailIds = [];

    foreach ($kategoriIds as $i => $kategoriId) {
        $nominal = (int) str_replace('.', '', $nominals[$i] ?? 0);
        $total += $nominal;

        if (!empty($detailIds[$i])) {
            PengeluaranDetail::where('id', $detailIds[$i])->update([
                'kategori_pengeluaran_id' => $kategoriId,
                'nominal' => $nominal
            ]);
            $usedDetailIds[] = $detailIds[$i];
        } else {
            $detail = PengeluaranDetail::create([
                'id_pengeluaran' => $data->id,
                'kategori_pengeluaran_id' => $kategoriId,
                'nominal' => $nominal
            ]);
            $usedDetailIds[] = $detail->id;
        }
    }

    // === Update sumber dana berdasarkan detail ID ===
    if ($request->has('sumber_dana_jenis')) {
        foreach ($request->sumber_dana_jenis as $barisIndex => $sumberDanaBaris) {
            $detailId = $usedDetailIds[$barisIndex] ?? null;
            if (!$detailId) continue;

            // Hapus sumber dana lama dulu
            PengeluaranDetailSumber::where('pengeluaran_detail_id', $detailId)->delete();

            foreach ($sumberDanaBaris as $sumberIndex => $kategoriPemasukanId) {
                $nominal = (int) str_replace('.', '', $request->sumber_nominal[$barisIndex][$sumberIndex] ?? 0);

                PengeluaranDetailSumber::create([
                    'pengeluaran_detail_id' => $detailId,
                    'kategori_pemasukan_id' => $kategoriPemasukanId,
                    'nominal' => $nominal
                ]);
            }
        }
    }

    // === Update data utama ===
    $updated= $data->update([
        'tanggal_pengeluaran' => $request->tanggal_pengeluaran,
        'id_mustahik' => $request->nama_lengkap,
        'lampiran' => $filename,
        'jumlah' => $total,
        'tipe' => $request->tipe_saldo,
        'deskripsi' => $request->deskripsi
    ]);

    Log::info("Apakah update berhasil?", ['result' => $updated]);

    return response()->json(['status' => 'success']);
}

public function updateDetail(Request $request)
{
    $request->validate([
        'detail_id' => 'required|integer|exists:pengeluaran_detail,id',
        'kategori_pengeluaran_id' => 'required|integer|exists:kategori_pengeluaran,id',
        'nominal' => 'required|numeric|min:0',
    ]);

    $nominal = (int) str_replace('.', '', $request->nominal);

    // Update detail
    PengeluaranDetail::where('id', $request->detail_id)->update([
        'kategori_pengeluaran_id' => $request->kategori_pengeluaran_id,
        'nominal' => $nominal
    ]);

    // Ambil id_pengeluaran dari detail
    $detail = PengeluaranDetail::find($request->detail_id);
    $idPengeluaran = $detail->id_pengeluaran;

    // Hitung ulang total
    $total = PengeluaranDetail::where('id_pengeluaran', $idPengeluaran)->sum('nominal');

    // Update total jumlah di pengeluaran
    Pengeluaran::where('id', $idPengeluaran)->update([
        'jumlah' => $total
    ]);

    return response()->json(['status' => 'success']);
}



public function updateSumber(Request $request)
{
    $request->validate([
        'sumber_dana_id' => 'required|integer|exists:pengeluaran_detail_sumber,id',
        'sumber_dana_jenis' => 'required|integer|exists:kategori_penerimaan,id',
        'sumber_nominal' => 'required|numeric|min:0',
    ]);

    $nominal = (int) str_replace('.', '', $request->sumber_nominal);

    PengeluaranDetailSumber::where('id', $request->sumber_dana_id)->update([
        'kategori_pemasukan_id' => $request->sumber_dana_jenis,
        'nominal' => $nominal
    ]);

    return response()->json(['status' => 'success']);
}





public function cetakKwitansi($id)
{
    $data = Pengeluaran::with(['mustahik', 'kategori', 'detail.kategori'])->findOrFail($id);
    $width = 210;
    $height = 120;
    $pdf = new \FPDF('L', 'mm', [$width, $height]);
    $pdf->AddPage();
    $pdf->SetAutoPageBreak(false);

    $backgroundPath = public_path('images/Lazis_kwitansi.png');
    if (file_exists($backgroundPath)) {
        $pdf->Image($backgroundPath, 0, 0, $width, $height);
    }

    $pdf->SetFont('Arial','',11);

    $pdf->SetXY(25, 40);
    $pdf->SetFont('Times', 'B', 9);
    $pdf->SetTextColor(255, 0, 0);
    $pdf->SetXY(17, 38);
    $pdf->Cell(60, 6, ($data->no_pengeluaran ?? '-'), 0, 1);

     $pdf->SetTextColor(0, 0, 0);

    $pdf->SetFont('Times', '', 12);
    $pdf->SetXY(5, 43);
    $pdf->Cell(55, 5, '', 0, 0, 'L');
    $pdf->Cell(130, 6, $data->mustahik->nama_lengkap ?? '-', 0, 1);

    $pdf->SetXY(60, 50.5);
    $terbilang = ucwords($this->terbilang($data->jumlah)) . ' Rupiah';
    $pdf->MultiCell(140, 6, $terbilang, 0, 'L');

    $pdf->SetXY(60, 57.5);
    foreach ($data->detail as $detail) {
        $kategori = $detail->kategori;
        $namaKategori = $kategori->nama ?? 'Tidak Diketahui';
        $jenisKategori = $kategori->jenis_kategori ?? '-';

        $pdf->Cell(150, 6, $namaKategori . ' - ' . $jenisKategori , 0, 1);
        $pdf->SetX(20);
    }

    $pdf->SetXY(60, 64.5);
    $pdf->MultiCell(120, 6, $data->deskripsi ?? '-');

    $pdf->SetXY(25, 82);
    $pdf->Cell(80, 6, 'Rp. ' . number_format($data->jumlah, 0, ',', '.'), 0, 1);

    $pdf->SetXY(20, 125);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(100, 6, 'Rincian Kategori Pengeluaran:', 0, 1);

    $pdf->SetFont('Arial','',10);
    $pdf->SetX(20);
    foreach ($data->detail as $detail) {
        $pdf->Cell(100, 6, '- ' . ($detail->kategori->nama ?? '-') . ': Rp. ' . number_format($detail->nominal, 0, ',', '.'), 0, 1);
        $pdf->SetX(20);
    }

    $tipePembayaran = strtoupper($data->tipe ?? '-');

    $checkIcon = public_path('images/check-solid.png');
    $checkSize = 5;

    if (file_exists($checkIcon)) {
       switch (strtolower($data->tipe ?? '-')) {
        case 'bank':
            $pdf->Image($checkIcon, 28, 90, $checkSize);
            break;

        case 'kas':
            $pdf->Image($checkIcon, 11, 90, $checkSize);
            break;
    }

    }


    $pdf->Output();
    exit;
}

private function terbilang($angka)
{
    $angka = abs($angka);
    $baca = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];
    $hasil = "";

    if ($angka < 12) {
        $hasil = " " . $baca[$angka];
    } elseif ($angka < 20) {
        $hasil = $this->terbilang($angka - 10) . " Belas";
    } elseif ($angka < 100) {
        $hasil = $this->terbilang(floor($angka / 10)) . " Puluh" . $this->terbilang($angka % 10);
    } elseif ($angka < 200) {
        $hasil = " Seratus" . $this->terbilang($angka - 100);
    } elseif ($angka < 1000) {
        $hasil = $this->terbilang(floor($angka / 100)) . " Ratus" . $this->terbilang($angka % 100);
    } elseif ($angka < 2000) {
        $hasil = " Seribu" . $this->terbilang($angka - 1000);
    } elseif ($angka < 1000000) {
        $hasil = $this->terbilang(floor($angka / 1000)) . " Ribu" . $this->terbilang($angka % 1000);
    } elseif ($angka < 1000000000) {
        $hasil = $this->terbilang(floor($angka / 1000000)) . " Juta" . $this->terbilang($angka % 1000000);
    }

    return trim($hasil);
}



   public function cetak()
{
    $data = Pengeluaran::with(['mustahik', 'detail.kategori'])->orderBy('id', 'asc')->get();

    $pdf = new \FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Times','B',14);
    $pdf->Cell(0,15,'Laporan Pengeluaran',0,1,'C');

    $pdf->SetDrawColor(0, 0, 0);
    $pdf->SetLineWidth(0.7);
    $pdf->Line(10, 10, 200, 10);
    $pdf->SetLineWidth(0.3);
    $pdf->Line(10, 11, 200, 11);

    $pdf->SetXY(10, 30);
    $pdf->Ln(10);
    $pdf->SetFont('Arial','B',10);
    $pdf->SetFillColor(211, 236, 230);
    $pdf->Cell(10,10,'No',1,0,'C', true);
    $pdf->Cell(30,10,'Tanggal',1,0,'C', true);
    $pdf->Cell(50,10,'Nama Mustahik',1,0,'C', true);
    $pdf->Cell(50,10,'Kategori',1,0,'C', true);
    $pdf->Cell(50,10,'Nominal',1,0,'C', true);

    $pdf->Ln();

    $pdf->SetFont('Arial','',10);
    $no = 1;

    foreach ($data as $row) {
        foreach ($row->detail as $detail) {
            $pdf->Cell(10,8, $no++,1);
            $pdf->Cell(30,8, Carbon::parse($row->tanggal_pemasukan)->translatedFormat('d-m-Y'),1);
            $pdf->Cell(50,8, $row->mustahik->nama_lengkap ?? '-',1);

            $kategori = $detail->kategori
                ? $detail->kategori->nama . ' - ' . $detail->kategori->jenis_kategori
                : '-';
            $pdf->Cell(50,8, $kategori,1);

            $pdf->Cell(50,8, 'Rp.' . str_pad(number_format($detail->nominal, 0, ',', '.'), 33, ' ', STR_PAD_LEFT), 1);
            $pdf->Ln();
        }
    }

    $pdf->Output('I', 'laporan_pengeluaran.pdf');
    exit;
}


    public function destroy($id)
    {
        $data = Pengeluaran::findOrFail($id);

        // Ambil semua ID pengeluaran_detail
        $detailIds = PengeluaranDetail::where('id_pengeluaran', $data->id)->pluck('id');

        // Hapus semua sumber dana terkait
        PengeluaranDetailSumber::whereIn('pengeluaran_detail_id', $detailIds)->delete();

        // Hapus semua detail pengeluaran
        PengeluaranDetail::where('id_pengeluaran', $data->id)->delete();

        // Hapus pengeluaran utamanya
        $data->delete();

        return response()->json(['status' => 'success']);
    }


}
