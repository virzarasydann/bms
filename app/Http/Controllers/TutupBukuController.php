<?php

namespace App\Http\Controllers;

use App\Models\Pemasukan;
use App\Models\KategoriPenerimaan;
use App\Models\PengeluaranDetail;
use App\Models\PengeluaranDetailSumber;
use Illuminate\Support\Facades\DB;
use App\Models\Pengeluaran;
use App\Models\Mustahik;
use App\Models\TutupBuku;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\HakAksesController;
use App\Models\KategoriPengeluaran;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TutupBukuController extends Controller
{
    public function index(Request $request)
    {
        
        $permissions = HakAksesController::getUserPermissions();
        return view('admin.tutup_buku.index', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2000|max:' . date('Y'),
        ]);
    
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $kategoriList = KategoriPenerimaan::all();
        $tipeList = ['Bank', 'Kas'];
    
        DB::beginTransaction();
    
        try {
            foreach ($tipeList as $tipe) {
                foreach ($kategoriList as $kategori) {
                    // Cek dulu apakah sudah pernah tutup buku untuk kombinasi ini
                    $existing = TutupBuku::where('id_penerimaan', $kategori->id)
                        ->where('tipe', $tipe)
                        ->where('bulan', $bulan)
                        ->where('tahun', $tahun)
                        ->first();
    
                    if ($existing) {
                        return response()->json([
                            'message' => "Tutup buku untuk bulan {$bulan}/{$tahun} sudah dilakukan",
                        ], 409); // 409 Conflict
                    }
    
                    // Hitung total pemasukan
                    $totalPemasukan = Pemasukan::where('kategori_penerimaan', $kategori->id)
                        ->where('tipe', $tipe)
                        ->whereMonth('tanggal_pemasukan', $bulan)
                        ->whereYear('tanggal_pemasukan', $tahun)
                        ->sum('nominal');
    
                    // Hitung total pengeluaran
                    $totalPengeluaran = PengeluaranDetailSumber::where('kategori_pemasukan_id', $kategori->id)
                        ->whereHas('detail.pengeluaran', function ($query) use ($bulan, $tahun, $tipe) {
                            $query->where('tipe', $tipe)
                                ->whereMonth('tanggal_pengeluaran', $bulan)
                                ->whereYear('tanggal_pengeluaran', $tahun);
                        })
                        ->sum('nominal');
    
                    $saldo = $totalPemasukan - $totalPengeluaran;
    
                    // Simpan tutup buku
                    TutupBuku::create([
                        'bulan' => $bulan,
                        'tahun' => $tahun,
                        'id_penerimaan' => $kategori->id,
                        'tipe' => $tipe,
                        'saldo' => $saldo,
                    ]);
                }
            }
    
            DB::commit();
            return response()->json(['message' => 'success'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan tutup buku: ' . $e->getMessage(),
            ], 500);
        }
    }
    

    


    
}
