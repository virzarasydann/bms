<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\HakAksesController;
use Carbon\Carbon;
use App\Models\Survey;
use Codedge\Fpdf\Fpdf\Fpdf;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;

class PengajuanController extends Controller
{
    public function index(Request $request)
    {
         Carbon::setLocale('id');
        $permissions = HakAksesController::getUserPermissions();

        if ($request->ajax()) {
            $opd = Pengajuan::orderBy('id', 'asc');


            return DataTables::of($opd)
                ->addIndexColumn()
                  ->addColumn('tgl_pengajuan', function ($row) {
                    return Carbon::parse($row->tgl_pengajuan)->translatedFormat('j F Y');
                })
                ->editColumn('alamat', function ($row) {
                        return $row->alamat . '<br><small class="text-muted" style="font-weight: 600;">Telp: ' . $row->no_telp . '</small>';
                    })
                    ->editColumn('stt_pengajuan', function ($row) {
                    switch ($row->stt_pengajuan) {
                    case 0:
                        return '<span class="badge badge-warning">Pending</span>';
                    case 1:
                        return '<span class="badge badge-danger">Ditolak</span>';
                    case 2:
                        return '<span class="badge badge-success">Disetujui</span>';
                    default:
                        return '<span class="badge badge-secondary">Tidak Diketahui</span>';
                    }
                    })


               ->addColumn('action', function ($row) use ($permissions): string {
                    $editUrl = route('pengajuan.edit', $row->id);
                    $deleteUrl = route('pengajuan.destroy', $row->id);
                    $surveyUrl = route('pengajuan.survey', $row->id);

                    $btn = '<div class="d-flex justify-content-center">';

                    if ($permissions['edit']) {
                    $btn .= '<button class="btn btn-primary btn-xs mx-1" data-id="' . e($row->id) . '"
                         data-url="' . e($editUrl) . '" data-toggle="modal" data-target="#modalForm" id="edit-button">
                         Edit
                     </button>';
                    }

                    $btn .= '<a href="' . e($surveyUrl) . '" class="btn btn-success btn-xs mx-1">
                        Survey
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
                ->rawColumns(['action', 'alamat', 'stt_pengajuan'])
                ->make(true);
        }

        return view('admin.pengajuan.index', compact('permissions'));
    }

 public function survey($id)
{
    $data = Pengajuan::findOrFail($id);
    $survey = Survey::where('id_pengajuan', $id)->first();

    return view('admin.pengajuan.survey', compact('data', 'survey'));
}


public function storeSurvey(Request $request, $id)
{
    $request->validate([
        'tgl_survey' => 'required|date',
        'nama_lengkap' => 'required|string|max:255',
        'nik' => 'required|string|max:255',
        'alamat' => 'required|string',
        'no_hp' => 'nullable|string|max:255',
        'tempat_lahir' => 'required|string|max:255',
        'tgl_lahir' => 'required|date',
        'usia' => 'required|numeric|min:0',
        'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
        'status' => 'required|in:Belum,Kawin,Cerai,Janda/Duda',
        'pekerjaan' => 'required|string|max:255',
        'penghasilan' => 'required|string|max:255',
        'lama_tinggal' => 'required|string|max:255',
        'stt_tempat_tinggal' => 'required|string|max:255',
        'membantu' => 'required|in:Ada,Tidak ada',
        'nama_lembaga_membantu' => 'nullable|string|max:255',
        'orang_terdekat' => 'nullable|string|max:255',
        'masalah' => 'required|string',
        'jumlah_tanggungan' => 'required|integer|min:0',
        'usaha_dilakukan' => 'required|string|max:255',
        'pengeluaran_bulan' => 'required|string|max:255',
        'tabungan' => 'required|string|max:255',
        'hutang' => 'required|in:Ada,Tidak ada',
        'jumlah_hutang' => 'nullable|string|max:255',
        'harapan_bantuan' => 'required|string|max:255',
        'bersedia_kajian_islam' => 'required|in:Bersedia,Tidak',
    ]);

    try {
        $tanggal = Carbon::parse($request->tgl_survey);
        $bulanRomawi = [
            '01'=>'I','02'=>'II','03'=>'III','04'=>'IV','05'=>'V','06'=>'VI',
            '07'=>'VII','08'=>'VIII','09'=>'IX','10'=>'X','11'=>'XI','12'=>'XII'
        ][$tanggal->format('m')];

        $tahun = $tanggal->year;

        $latestKode = Survey::whereYear('tgl_survey', $tahun)
            ->whereNotNull('no_survey')
            ->orderByDesc('id')
            ->value('no_survey');

        if ($latestKode) {
            $lastNumber = (int) substr($latestKode, 0, 4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        $nomorUrut = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        $kodeMenu = "{$nomorUrut}/S-LAZIS/{$bulanRomawi}/{$tahun}";

        Survey::updateOrCreate(
            ['id_pengajuan' => $id],
            [
                'tgl_survey' => $request->tgl_survey,
                'nama_lengkap' => $request->nama_lengkap,
                'nik' => $request->nik,
                'alamat' => $request->alamat,
                'no_hp' => $request->no_hp,
                'tempat_lahir' => $request->tempat_lahir,
                'tgl_lahir' => $request->tgl_lahir,
                'usia' => $request->usia,
                'jenis_kelamin' => $request->jenis_kelamin,
                'status' => $request->status,
                'pekerjaan' => $request->pekerjaan,
                'penghasilan' => $request->penghasilan,
                'lama_tinggal' => $request->lama_tinggal,
                'stt_tempat_tinggal' => $request->stt_tempat_tinggal,
                'membantu' => $request->membantu,
                'nama_lembaga_membantu' => $request->nama_lembaga_membantu,
                'orang_terdekat' => $request->orang_terdekat,
                'masalah' => $request->masalah,
                'jumlah_tanggungan' => $request->jumlah_tanggungan,
                'usaha_dilakukan' => $request->usaha_dilakukan,
                'pengeluaran_bulan' => $request->pengeluaran_bulan,
                'tabungan' => $request->tabungan,
                'hutang' => $request->hutang,
                'jumlah_hutang' => $request->jumlah_hutang,
                'harapan_bantuan' => $request->harapan_bantuan,
                'bersedia_kajian_islam' => $request->bersedia_kajian_islam,
                'no_survey' => $kodeMenu
            ]
        );

        return redirect()->route('pengajuan.index')->with('success', 'Data survey berhasil disimpan atau diperbarui.');

    } catch (\Exception $e) {

        Log::error('Gagal menyimpan data survey', [
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
            'input' => $request->all()
        ]);

        return back()->with('error', 'Terjadi kesalahan saat menyimpan data survey. Silakan cek log aplikasi.');
    }
}



  public function store(Request $request)
    {
        $request->validate([
            'tgl_pengajuan' => 'required',
            'nama_lengkap' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'no_telp' => 'required|string|max:50',
            'permasalahan' => 'required|string|max:255',
            'penyelesaian' => 'required|string|max:255',
            'nama_perekomendasi' => 'required|string|max:255',
        ], [
            'tgl_pengajuan.required' => 'Tanggal wajib diisi.',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'alamat.required' => 'Alamat wajib diisi.',
            'no_telp.required' => 'No. Telp wajib diisi.',
            'permasalahan.required' => 'Permasalahan wajib diisi.',
            'penyelesaian.required' => 'Penyelesaian wajib diisi.',
            'nama_perekomendasi.required' => 'Nama perekomendasi wajib diisi.',
            ]);

        $tanggal = Carbon::parse($request->tgl_pengajuan);
        $bulanRomawi = [
            '01'=>'I','02'=>'II','03'=>'III','04'=>'IV','05'=>'V','06'=>'VI',
            '07'=>'VII','08'=>'VIII','09'=>'IX','10'=>'X','11'=>'XI','12'=>'XII'
        ][$tanggal->format('m')];

        $tahun = $tanggal->year;

        // Ambil kode terakhir
        $latestKode = Pengajuan::whereYear('tgl_pengajuan', $tahun)
            ->whereNotNull('no_pengajuan')
            ->orderByDesc('id')
            ->value('no_pengajuan');

        if ($latestKode) {
            $lastNumber = (int) substr($latestKode, 0, 4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        $nomorUrut = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        $kodeMenu = "{$nomorUrut}/P-LAZIS/{$bulanRomawi}/{$tahun}";


        $pengajuan = Pengajuan::create([
            'tgl_pengajuan' => $request->tgl_pengajuan,
            'nama_lengkap' => $request->nama_lengkap,
            'alamat' => $request->alamat,
            'no_telp' => $request->no_telp,
            'permasalahan' => $request->permasalahan,
            'penyelesaian' => $request->penyelesaian,
            'nama_perekomendasi' => $request->nama_perekomendasi,
            'stt_pengajuan' => 0,
            'no_pengajuan' => $kodeMenu
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data pengajuan berhasil disimpan.',
            'data' => $pengajuan
        ]);
    }



    public function showNoPengajuan(Request $request)
    {

        $tanggal = $request->tgl_pengajuan
            ? Carbon::parse($request->tgl_pengajuan)
            : Carbon::now();


        $bulanRomawi = [
            '01' => 'I', '02' => 'II', '03' => 'III', '04' => 'IV', '05' => 'V', '06' => 'VI',
            '07' => 'VII', '08' => 'VIII', '09' => 'IX', '10' => 'X', '11' => 'XI', '12' => 'XII'
        ][$tanggal->format('m')];

        $tahun = $tanggal->year;

        $latestKode = Pengajuan::whereYear('tgl_pengajuan', $tahun)
            ->whereNotNull('no_pengajuan')
            ->orderByDesc('id')
            ->value('no_pengajuan');

        if ($latestKode) {
            $lastNumber = (int) substr($latestKode, 0, 4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        $nomorUrut = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        $kodeMenu = "{$nomorUrut}/P-LAZIS/{$bulanRomawi}/{$tahun}";

        return response()->json(['status' => 'success', 'data' => $kodeMenu]);
    }


    public function showNoSurvey(Request $request)
    {

        $tanggal = $request->tgl_survey
            ? Carbon::parse($request->tgl_survey)
            : Carbon::now();


        $bulanRomawi = [
            '01' => 'I', '02' => 'II', '03' => 'III', '04' => 'IV', '05' => 'V', '06' => 'VI',
            '07' => 'VII', '08' => 'VIII', '09' => 'IX', '10' => 'X', '11' => 'XI', '12' => 'XII'
        ][$tanggal->format('m')];

        $tahun = $tanggal->year;

        $latestKode = Survey::whereYear('tgl_survey', $tahun)
            ->whereNotNull('no_survey')
            ->orderByDesc('id')
            ->value('no_survey');

        if ($latestKode) {
            $lastNumber = (int) substr($latestKode, 0, 4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        $nomorUrut = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        $kodeMenu = "{$nomorUrut}/S-LAZIS/{$bulanRomawi}/{$tahun}";

        return response()->json(['status' => 'success', 'data' => $kodeMenu]);
    }


    public function edit($id)
    {
        $data = Pengajuan::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

 public function update(Request $request, $id)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'no_telp' => 'required|string|max:50',
            'permasalahan' => 'required|string|max:255',
            'penyelesaian' => 'required|string|max:255',
            'nama_perekomendasi' => 'required|string|max:255',
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'alamat.required' => 'Alamat wajib diisi.',
            'no_telp.required' => 'No. Telp wajib diisi.',
            'permasalahan.required' => 'Permasalahan wajib diisi.',
            'penyelesaian.required' => 'Penyelesaian wajib diisi.',
            'nama_perekomendasi.required' => 'Nama perekomendasi wajib diisi.',
        ]);

        $pengajuan = Pengajuan::findOrFail($id);

        $pengajuan->update([
            'nama_lengkap' => $request->nama_lengkap,
            'alamat' => $request->alamat,
            'no_telp' => $request->no_telp,
            'permasalahan' => $request->permasalahan,
            'penyelesaian' => $request->penyelesaian,
            'nama_perekomendasi' => $request->nama_perekomendasi,
            'stt_pengajuan' => $request->stt_pengajuan,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data pengajuan berhasil diperbarui.',
            'data' => $pengajuan
        ]);
    }


    public function cetak($id)
    {
        $data = Survey::findOrFail($id);
         if (!$data) {
        return Redirect::back()->with('error', 'Data survey tidak ditemukan. Silakan lengkapi form survey terlebih dahulu.');
    }
        $pdf = new \FPDF();
        $pdf->AddPage();
        $pdf->SetAutoPageBreak(true, 15);

        // Header
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetXY(50, 10);
        $pdf->Cell(0, 6, 'LEMBAGA AAMIL ZAKAT, INFAQ & SHADAQAH', 0, 1, 'L');
        $pdf->SetXY(50, 10);
        $pdf->SetFont('Arial', '', 7);
        $pdf->SetXY(50, 17);
        $pdf->MultiCell(0, 4, "MASJID AL IMAM AN - NASA'I\nJL. SYAFRUDDIN YOS RT.41 KOMPLEK MASJID AL IMAM AN-NASA'I KELURAHAN GN. BAHAGIA\n0811511252 - 08115096123");

        $pdf->Ln(3);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'LEMBAR VERIFIKASI PENGAJUAN BANTUAN', 0, 1, 'C');
        $pdf->Ln(3);
        $pdf->SetFont('Arial', '', 11);

          $logo = public_path('images/logo.png');
        if (file_exists($logo)) {
            $pdf->Image($logo, 12, 12, 30);
        }

        $pdf->SetLineWidth(0.3);
        $pdf->Line(10, 33, 200, 33);

        // Helper
        function row($pdf, $no, $label, $value) {
            $pdf->Cell(10, 6, "$no.", 0, 0);
            $pdf->Cell(100, 6, $label, 0, 0);
            $pdf->Cell(8, 6, ":", 0, 0);
            $pdf->MultiCell(0, 6, $value);
        }

        $pdf->SetFont('Arial', '', 8);

        row($pdf, 1,  "NAMA", $data->nama_lengkap);
        row($pdf, 2,  "NIK", $data->nik);
        row($pdf, 3,  "ALAMAT", $data->alamat);
        row($pdf, 4,  "TEMPAT TANGGAL LAHIR", $data->tempat_lahir . ', ' . $data->tgl_lahir);
        row($pdf, 5,  "USIA", $data->usia . ' tahun');
        row($pdf, 6,  "JENIS KELAMIN", $data->jenis_kelamin);
        row($pdf, 7,  "STATUS PERNIKAHAN", $data->status);
        row($pdf, 8,  "PEKERJAAN", $data->pekerjaan);
        row($pdf, 9,  "PENGHASILAN", 'Rp ' . number_format($data->penghasilan, 0, ',', '.'));
        row($pdf, 10, "NO HP", $data->no_hp);
        row($pdf, 11, "LAMA TINGGAL DI TEMPAT SEKARANG", $data->lama_tinggal);
        row($pdf, 12, "STATUS TEMPAT TINGGAL", $data->stt_tempat_tinggal);
        row($pdf, 13, "LEMBAGA/PERORANGAN\nYANG PERNAH MEMBANTU", $data->membantu);
        row($pdf, 14, "NAMA LEMBAGA", $data->nama_lembaga_membantu);
        row($pdf, 15, "ORANG TERDEKAT YANG BISA DIHUBUNGI", $data->orang_terdekat);
        row($pdf, 16, "MASALAH YANG DIHADAPI", $data->masalah);
        row($pdf, 17, "JUMLAH TANGGUNGAN", $data->jumlah_tanggungan);
        row($pdf, 18, "UPAYA/USAHA YANG TELAH DILAKUKAN", $data->usaha_dilakukan);

        $pengeluaranList = explode(',', $data->pengeluaran_bulan);
        $pengeluaranFormatted = '';

        foreach ($pengeluaranList as $i => $item) {
        $pengeluaranFormatted .= ($i + 1) . '. ' . trim($item) . "\n";
        }

        row($pdf, 19, "PENGELUARAN SETIAP BULAN", trim($pengeluaranFormatted));


        row($pdf, 20, "TABUNGAN/HARTA YANG DIMILIKI", $data->tabungan);
        row($pdf, 21, "APAKAH MEMPUNYAI HUTANG", $data->hutang);
        row($pdf, 22, "JUMLAH HUTANG", 'Rp ' . number_format($data->jumlah_hutang, 0, ',', '.'));
        row($pdf, 23, "BANTUAN YANG DIHARAPKAN", $data->harapan_bantuan);
        row($pdf, 24, "BERSEDIA MENGIKUTI KAJIAN ISLAM", $data->bersedia_kajian_islam);

        // Pernyataan
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 8);
        $pdf->MultiCell(0, 5, "DEMIKIAN PERMOHONAN/PENGAJUAN BANTUAN SAYA AJUKAN, SEMUA KETERANGAN YANG DIATAS SAYA BUAT DENGAN SEBENAR-BENARNYA. JIKA KEMUDIAN HARI ADA KETERANGAN YANG TIDAK SESUAI DENGAN SEBENARNYA SAYA BERSEDIA DIBERI SANKSI SESUAI ATURAN DARI LAZIS AN NASA'I.");

        // Tanda tangan
        $pdf->Ln(10);
        $pdf->Cell(100, 8, 'VERIFIKATOR', 0, 0, 'C');
        $pdf->Cell(0, 8, 'BALIKPAPAN, ...................', 0, 1, 'C');
        $pdf->Ln(15);
        $pdf->Cell(100, 8, '(............................)', 0, 0, 'C');
        $pdf->Cell(0, 8, '(............................)', 0, 1, 'C');

        $pdf->Output('I', 'lembar_verifikasi_pengajuan.pdf');
        exit;
    }




    public function destroy($id)
    {
        $data = Pengajuan::findOrFail($id);
        $data->delete();

        return response()->json(['status' => 'success']);
    }
}
