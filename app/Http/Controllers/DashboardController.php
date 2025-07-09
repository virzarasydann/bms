<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\KategoriPenerimaan;
use App\Models\Pemasukan;
use App\Models\Pengajuan;
use App\Models\TutupBuku;
use App\Models\Survey;
use App\Models\PengeluaranDetail;
use App\Models\PengeluaranDetailSumber;
\Carbon\Carbon::setLocale('id');
use Illuminate\Http\Request;
use App\Models\Pengeluaran;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
{
    if (!Auth::check()) {
        return redirect()->route('login')->withError('Silahkan Login terlebih dahulu');
    }

    $tahun = $request->input('tahun', now()->year);
    $bulan = $request->input('bulan', now()->month);

    $kategoriList = KategoriPenerimaan::all();

    $jumlahPengajuan = Pengajuan::where('stt_pengajuan', 2)->count();
    $jumlahSurvey = Survey::count();

    // ====== Tambahan: Hitung saldoBank, saldoTunai, totalSaldo berdasarkan tipe dan tutup buku ======

    // Ambil saldo awal dari TutupBuku (semua kategori)
    $bulanSebelumnya = $bulan - 1;
    $tahunSebelumnya = $tahun;
    if ($bulanSebelumnya <= 0) {
        $bulanSebelumnya = 12;
        $tahunSebelumnya--;
    }

    $saldoAwal = TutupBuku::where('bulan', $bulanSebelumnya)
        ->where('tahun', $tahunSebelumnya)
        ->sum('saldo');

    // Pemasukan & pengeluaran BANK
    $pemasukanBank = Pemasukan::where('tipe', 'Bank')->whereMonth('tanggal_pemasukan', $bulan)->whereYear('tanggal_pemasukan', $tahun)->sum('nominal');
    $pengeluaranBank = PengeluaranDetail::whereHas('pengeluaran', function ($q) use ($bulan, $tahun) {
        $q->where('tipe', 'Bank')->whereMonth('tanggal_pengeluaran', $bulan)->whereYear('tanggal_pengeluaran', $tahun);
    })->sum('nominal');
    $saldoBank = $saldoAwal + $pemasukanBank - $pengeluaranBank;

    // Pemasukan & pengeluaran KAS
    $pemasukanKas = Pemasukan::where('tipe', 'Kas')->whereMonth('tanggal_pemasukan', $bulan)->whereYear('tanggal_pemasukan', $tahun)->sum('nominal');
    $pengeluaranKas = PengeluaranDetail::whereHas('pengeluaran', function ($q) use ($bulan, $tahun) {
        $q->where('tipe', 'Kas')->whereMonth('tanggal_pengeluaran', $bulan)->whereYear('tanggal_pengeluaran', $tahun);
    })->sum('nominal');
    $saldoTunai = $saldoAwal + $pemasukanKas - $pengeluaranKas;

    // Total saldo = saldoAwal + seluruh pemasukan - seluruh pengeluaran
    $totalPemasukan = Pemasukan::whereMonth('tanggal_pemasukan', $bulan)->whereYear('tanggal_pemasukan', $tahun)->sum('nominal');
    $totalPengeluaran = PengeluaranDetail::whereHas('pengeluaran', function ($q) use ($bulan, $tahun) {
        $q->whereMonth('tanggal_pengeluaran', $bulan)->whereYear('tanggal_pengeluaran', $tahun);
    })->sum('nominal');
    $totalSaldo = $saldoAwal + $totalPemasukan - $totalPengeluaran;

    // ===============================================================================================

    $data = $kategoriList->map(function ($kategori, $index) use ($tahun, $bulan) {
        $saldoAwal = TutupBuku::where('id_penerimaan', $kategori->id)
            ->where(function ($query) use ($bulan, $tahun) {
                $query->where(function ($sub) use ($bulan, $tahun) {
                    $sub->where('tahun', $tahun)->where('bulan', '<', $bulan);
                })->orWhere(function ($sub) use ($tahun) {
                    $sub->where('tahun', '<', $tahun);
                });
            })
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->value('saldo') ?? 0;

        $pemasukanBulanIni = Pemasukan::where('kategori_penerimaan', $kategori->id)
            ->whereYear('tanggal_pemasukan', $tahun)
            ->whereMonth('tanggal_pemasukan', $bulan)
            ->sum('nominal');

        $pengeluaranBulanIni = PengeluaranDetailSumber::where('kategori_pemasukan_id', $kategori->id)
            ->whereHas('detail.pengeluaran', function ($query) use ($tahun, $bulan) {
                $query->whereYear('tanggal_pengeluaran', $tahun)->whereMonth('tanggal_pengeluaran', $bulan);
            })->sum('nominal');

        $saldo = $saldoAwal + $pemasukanBulanIni - $pengeluaranBulanIni;

        return [
            'id' => $kategori->id,
            'no' => $index + 1,
            'jenis_data' => $kategori->nama,
            'pemasukan' => $pemasukanBulanIni,
            'pengeluaran' => $pengeluaranBulanIni,
            'saldo' => $saldo,
            'saldo_awal' => $saldoAwal,
            'transaksi_pemasukan' => Pemasukan::with('donatur')
                ->where('kategori_penerimaan', $kategori->id)
                ->whereYear('tanggal_pemasukan', $tahun)
                ->whereMonth('tanggal_pemasukan', $bulan)
                ->get(),

            'transaksi_pengeluaran' => PengeluaranDetail::with('pengeluaran.mustahik')
                ->whereHas('sumberDana', function ($q) use ($kategori) {
                    $q->where('kategori_pemasukan_id', $kategori->id);
                })
                ->whereHas('pengeluaran', function ($query) use ($tahun, $bulan) {
                    $query->whereYear('tanggal_pengeluaran', $tahun)
                          ->whereMonth('tanggal_pengeluaran', $bulan);
                })
                ->get(),
        ];
    });

    return view('admin.dashboard.dashboard', compact(
        'data',
        'tahun',
        'bulan',
        'jumlahPengajuan',
        'jumlahSurvey',
        'saldoBank',
        'saldoTunai',
        'totalSaldo'
    ));
}

    

}
