<?php

namespace App\Http\Controllers;

use App\Models\Pemasukan;
use App\Models\Donatur;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\HakAksesController;
use App\Models\KategoriPenerimaan;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Codedge\Fpdf\Fpdf\Fpdf;
use App\Models\TutupBuku;


class PenerimaanController extends Controller
{
    public function index(Request $request)
    {
         Carbon::setLocale('id');
        $permissions = HakAksesController::getUserPermissions();

        if ($request->ajax()) {
            $opd = Pemasukan::with(['donatur', 'kategori'])->orderBy('id', 'asc');


            return DataTables::of($opd)
                ->addIndexColumn()
                  ->addColumn('tanggal_pemasukan', function ($row) {
                    return Carbon::parse($row->tanggal_pemasukan)->translatedFormat('j F Y');
                })

                ->addColumn('id_donatur', function ($row) {
                    return $row->donatur->nama_lengkap ?? '-';
                })

                ->addColumn('kategori_penerimaan', function ($row) {
                    if ($row->kategori) {
                        return $row->kategori->nama . ' - ' . $row->kategori->jenis_kategori;
                    }
                    return '-';
                })

               ->addColumn('lampiran', function ($row) {
                    if ($row->lampiran) {
                        $url = asset('penerimaan/' . $row->lampiran);
                        return '<button class="btn btn-success btn-sm btn-lampiran" data-url="' . $url . '">Lampiran</button>';
                    }
                    return '-';
                })



                 ->editColumn('nominal', function ($row) {
                      return '
                    <div class="d-flex justify-content-between harga-format w-100">
                        <span>Rp.</span>
                        <span>' . number_format($row->nominal, 0, ',', '.') . '</span>
                    </div>';
                })


                 ->addColumn('action', function ($row) use ($permissions): string {
                    $editUrl = route('penerimaan.edit', $row->id);
                    $deleteUrl = route('penerimaan.destroy', $row->id);
                    $cetakUrl = route('cetak-kwitansi-masuk', $row->id);

                    $btn = '<div class="d-flex justify-content-center">';
                      if ($permissions['edit']) {
                            $btn .= '<button class="btn btn-primary btn-xs mx-1" data-id="' . e($row->id) . '"
                                data-url="' . e($editUrl) . '" data-toggle="modal" data-target="#modalForm" id="edit-button">
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
                ->rawColumns(['action', 'nominal', 'lampiran'])
                ->make(true);
        }
        return view('admin.pemasukan.index', compact('permissions'));
    }

    public function show($id)
{
    $data = Pemasukan::with(['donatur', 'kategori'])->findOrFail($id);

    return response()->json([
        'status' => 'success',
        'data' => $data
    ]);
}



      public function getDonatur(Request $request)
    {
        $search = $request->get('q');
        $donatur = Donatur::where('nama_lengkap', 'like', "%$search%")
            ->select('id', 'nama_lengkap')
            ->get();

        return response()->json($donatur);
    }

    public function getKategoriPenerimaan()
    {
        $data = KategoriPenerimaan::select('id', 'nama', 'jenis_kategori')->get();

        return response()->json($data);
    }


   public function store(Request $request)
    {
        $request->validate([
            'tanggal_pemasukan' => 'required|date',
            'nama_lengkap' => 'required',
            'nominal' => 'required',
            'file_upload' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'kategori_penerimaan_id' => 'required',
            'tipe_saldo' => 'required',
            'deskripsi' => 'required'
        ], [
            'tanggal_pemasukan.required' => 'Tanggal pemasukan wajib diisi.',
            'tanggal_pemasukan.date' => 'Tanggal pemasukan harus berupa format tanggal yang valid.',

            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',

            'nominal.required' => 'Nominal wajib diisi.',

            'file_upload.required' => 'Lampiran wajib diisi.',
            'file_upload.file' => 'Lampiran harus berupa file.',
            'file_upload.mimes' => 'Lampiran harus berformat jpg, jpeg, png, atau pdf.',
            'file_upload.max' => 'Ukuran lampiran maksimal 2MB.',

            'kategori_penerimaan_id.required' => 'Kategori penerimaan wajib dipilih.',

            'tipe_saldo.required' => 'Tipe saldo wajib diisi.',
            'deskripsi.required' => 'Deskripsi wajib diisi.'
        ]);


        $tanggal = Carbon::parse($request->tanggal_pemasukan);
        $bulan = $tanggal->month;
        $tahun = $tanggal->year;
        $kategoriId = $request->kategori_penerimaan_id;

        $sudahDitutup = TutupBuku::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->where('id_penerimaan', $kategoriId)
            ->exists();

        if ($sudahDitutup) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data pemasukan tidak dapat disimpan karena bulan tersebut sudah ditutup buku.'
            ], 403);
        }

        $nominal = (int) str_replace('.', '', $request->nominal);
        $filename = null;

        if ($request->hasFile('file_upload')) {
            $file = $request->file('file_upload');
            $filename = 'lampiran_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('penerimaan'), $filename);
        }


        $bulanRomawi = strtoupper(Carbon::parse($request->tanggal_pemasukan)->format('m'));
        $bulanRomawi = [
            '01'=>'I','02'=>'II','03'=>'III','04'=>'IV','05'=>'V','06'=>'VI',
            '07'=>'VII','08'=>'VIII','09'=>'IX','10'=>'X','11'=>'XI','12'=>'XII'
        ][Carbon::parse($request->tanggal_pemasukan)->format('m')];

        $tahun = Carbon::parse($request->tanggal_pemasukan)->year;
        $latestKode = Pemasukan::whereYear('tanggal_pemasukan', $tahun)
        ->whereNotNull('no_transaksi')
        ->orderByDesc('id')
        ->value('no_transaksi');

        if ($latestKode) {
            $lastNumber = (int) substr($latestKode, 0, 4); // Ambil 0001 dari "0001/K-EULAZ/VI/2025"
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        $nomorUrut = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);


        $kodeMenu = "{$nomorUrut}/K-KEULAZ/{$bulanRomawi}/{$tahun}";


        Pemasukan::create([
            'tanggal_pemasukan'   => $request->tanggal_pemasukan,
            'id_donatur'          => $request->nama_lengkap,
            'nominal'             => $nominal,
            'lampiran'            => $filename,
            'kategori_penerimaan' => $kategoriId,
            'no_transaksi'           => $kodeMenu,
            'tipe' => $request->tipe_saldo,
            'deskripsi' => $request->deskripsi
        ]);

        return response()->json(['status' => 'success']);
    }


    public function showNoTransaksi(Request $request)
    {

        $tanggal = $request->tanggal_pemasukan
            ? Carbon::parse($request->tanggal_pemasukan)
            : Carbon::now();


        $bulanRomawi = [
            '01' => 'I', '02' => 'II', '03' => 'III', '04' => 'IV', '05' => 'V', '06' => 'VI',
            '07' => 'VII', '08' => 'VIII', '09' => 'IX', '10' => 'X', '11' => 'XI', '12' => 'XII'
        ][$tanggal->format('m')];

        $tahun = $tanggal->year;

        $latestKode = Pemasukan::whereYear('tanggal_pemasukan', $tahun)
            ->whereNotNull('no_transaksi')
            ->orderByDesc('id')
            ->value('no_transaksi');

        if ($latestKode) {
            $lastNumber = (int) substr($latestKode, 0, 4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        $nomorUrut = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        $kodeMenu = "{$nomorUrut}/K-KEULAZ/{$bulanRomawi}/{$tahun}";

        return response()->json(['status' => 'success', 'data' => $kodeMenu]);
    }

    public function edit($id)
    {
        $data = Pemasukan::with(['donatur', 'kategori'])
            ->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

   public function update(Request $request, $id)
    {
       $request->validate([
            'tanggal_pemasukan' => 'required|date',
            'nama_lengkap' => 'required',
            'nominal' => 'required',
            'file_upload' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'kategori_penerimaan_id' => 'required',
            'tipe_saldo' => 'required',
            'deskripsi' => 'required'
        ], [
            'tanggal_pemasukan.required' => 'Tanggal pemasukan wajib diisi.',
            'tanggal_pemasukan.date' => 'Tanggal pemasukan harus berupa format tanggal yang valid.',

            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',

            'nominal.required' => 'Nominal wajib diisi.',

            'file_upload.file' => 'Lampiran harus berupa file.',
            'file_upload.mimes' => 'Lampiran harus berformat jpg, jpeg, png, atau pdf.',
            'file_upload.max' => 'Ukuran lampiran maksimal 2MB.',

            'kategori_penerimaan_id.required' => 'Kategori penerimaan wajib dipilih.',
            'tipe_saldo.required' => 'Tipe saldo wajib diisi.',
            'deskripsi.required' => 'Deskripsi wajib diisi.'
        ]);


        $data = Pemasukan::findOrFail($id);

    $tanggalBaru = Carbon::parse($request->tanggal_pemasukan);
    $bulan = $tanggalBaru->month;
    $tahun = $tanggalBaru->year;
    $kategoriId = $request->kategori_penerimaan_id;

    $sudahDitutup = TutupBuku::where('bulan', $bulan)
        ->where('tahun', $tahun)
        ->where('id_penerimaan', $kategoriId)
        ->exists();

    if ($sudahDitutup) {
        return response()->json([
            'status' => 'error',
            'message' => 'Data pemasukan tidak dapat diubah karena bulan tersebut sudah ditutup buku.'
        ], 403);
    }
    $filename = $data->lampiran;

    if ($request->hasFile('file_upload')) {
        $oldPath = public_path('penerimaan/' . $filename);
        if ($filename && file_exists($oldPath)) {
            unlink($oldPath);
        }

        $file = $request->file('file_upload');
        $filename = 'lampiran_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('penerimaan'), $filename);
    }

    $nominal = (int) str_replace('.', '', $request->nominal);

    $data->update([
        'tanggal_pemasukan'   => $request->tanggal_pemasukan,
        'id_donatur'          => $request->nama_lengkap,
        'nominal'             => $nominal,
        'lampiran'            => $filename,
        'kategori_penerimaan' => $kategoriId,
        'tipe' => $request->tipe_saldo,
        'deskripsi' => $request->deskripsi
    ]);

    return response()->json(['status' => 'success']);
    }

    public function cetakKwitansi($id)
    {
        $data = Pemasukan::with(['donatur', 'kategori'])->findOrFail($id);
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
        $pdf->Cell(60, 6, ($data->no_transaksi ?? '-'), 0, 1);

        $pdf->SetTextColor(0, 0, 0);

        $pdf->SetFont('Times', '', 12);
        $pdf->SetXY(5, 43);
        $pdf->Cell(55, 5, '', 0, 0, 'L');
        $pdf->Cell(130, 6, $data->donatur->nama_lengkap ?? '-', 0, 1);

        $pdf->SetXY(60, 50.5);
        $terbilang = ucwords($this->terbilang($data->nominal)) . ' Rupiah';
        $pdf->MultiCell(140, 6, $terbilang, 0, 'L');

        $kategoriLabel = '-';
        if ($data->kategori) {
        $kategoriLabel = $data->kategori->nama . ' - ' . $data->kategori->jenis_kategori;
        }
        $pdf->SetXY(60, 58);

        $pdf->Cell(150, 6, $kategoriLabel, 0, 1);

        $pdf->SetXY(60, 64.5);
        $pdf->MultiCell(120, 6, $data->deskripsi ?? '-');

        $pdf->SetXY(25, 82);
        $pdf->Cell(80, 6, 'Rp. ' . number_format($data->nominal, 0, ',', '.'), 0, 1);

        $pdf->SetXY(20, 125);
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(100, 6, 'Rincian Kategori Pengeluaran:', 0, 1);


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
            $hasil = " Seratus " . $this->terbilang($angka - 100);
        } elseif ($angka < 1000) {
            $hasil = $this->terbilang(floor($angka / 100)) . " Ratus" . $this->terbilang($angka % 100);
        } elseif ($angka < 2000) {
            $hasil = " Seribu " . $this->terbilang($angka - 1000);
        } elseif ($angka < 1000000) {
            $hasil = $this->terbilang(floor($angka / 1000)) . " Ribu" . $this->terbilang($angka % 1000);
        } elseif ($angka < 1000000000) {
            $hasil = $this->terbilang(floor($angka / 1000000)) . " Juta" . $this->terbilang($angka % 1000000);
        }

        return trim($hasil);
    }


    public function cetak()
    {
        $data = Pemasukan::with(['donatur', 'kategori'])->orderBy('id', 'asc')->get();

        $pdf = new \FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Times','B',14);
        $pdf->Cell(0,15,'Laporan Penerimaan',0,1,'C');

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
        $pdf->Cell(50,10,'Nama Donatur',1,0,'C', true);
        $pdf->Cell(50,10,'Kategori',1,0,'C', true);
        $pdf->Cell(50,10,'Nominal',1,0,'C', true);

        $pdf->Ln();

        $pdf->SetFont('Arial','',10);
        $no = 1;
        foreach ($data as $row) {
            $pdf->Cell(10,8, $no++,1);
            $pdf->Cell(30,8, Carbon::parse($row->tanggal_pemasukan)->translatedFormat('d-m-Y'),1);
            $pdf->Cell(50,8, $row->donatur->nama_lengkap ?? '-',1);
            $kategori = $row->kategori ? $row->kategori->nama . ' - ' . $row->kategori->jenis_kategori : '-';
            $pdf->Cell(50,8, $kategori,1);
            $pdf->Cell(50,8, 'Rp.' . str_pad(number_format($row->nominal, 0, ',', '.'), 33, ' ', STR_PAD_LEFT), 1);
            $pdf->Ln();
        }

        $pdf->Output('I', 'laporan_penerimaan.pdf');
        exit;
    }

    public function destroy($id)
    {
        $data = pemasukan::findOrFail($id);
        $data->delete();

        return response()->json(['status' => 'success']);
    }
}
