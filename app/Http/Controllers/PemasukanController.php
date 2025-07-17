<?php

namespace App\Http\Controllers;

use App\Models\Pemasukan;
use App\Models\Bank;
use App\Models\KategoriTransaksi;
use App\Models\Piutang;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;

class PemasukanController extends Controller
{
    public function index(Request $request)
    {
        $permissions = HakAksesController::getUserPermissions();
        $dataBank = Bank::all();
        $dataKategori = KategoriTransaksi::where('jenis_transaksi', 'pemasukan')
        ->whereIn('nama_kategori', [
            'Hutang External',
            'Piutang Reseller',
            'Pembayaran Piutang'
        ])
        ->get();
    
        $dataPiutang = Piutang::with('project')->get();

        if ($request->ajax()) {
            $query = Pemasukan::with(['bank', 'kategoriTransaksi'])->orderBy('tanggal', 'desc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('bank', fn ($row) => $row->bank->nama_bank ?? '-')
                ->addColumn('kategori', fn ($row) => $row->kategoriTransaksi->nama_kategori ?? '-')
                ->addColumn('lampiran', function ($row) {
                    if ($row->lampiran) {
                        return '<a href="#" class="btn-preview-lampiran text-primary" data-url="' . asset('storage/' . $row->lampiran) . '">Lihat</a>';
                    }
                    return '-';
                })
                ->addColumn('action', function ($row) use ($permissions) {
                    $editUrl = route('pemasukan.edit', $row->id);
                    $deleteUrl = route('pemasukan.destroy', $row->id);
                    $btn = '<div class="text-center">';
                    if ($permissions['edit']) {
                        $btn .= '<button class="btn btn-primary btn-xs mx-1" data-id="' . $row->id . '" data-url="' . $editUrl . '" data-toggle="modal" data-target="#modalForm" id="edit-button">Detail</button>';
                    }
                    if ($permissions['hapus']) {
                        $btn .= '<form action="' . $deleteUrl . '" method="POST" style="display:inline;">' .
                                csrf_field() . method_field('DELETE') .
                                '<button type="submit" class="delete-button btn btn-danger btn-xs mx-1">Hapus</button>' .
                                '</form>';
                    }
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['action','lampiran'])
                ->make(true);
        }

        return view('admin.keuangan.pemasukan.index', compact('permissions', 'dataBank', 'dataKategori','dataPiutang'));
    }

    public function store(Request $request)
{
    
    $request->merge([
        'nominal' => str_replace('.', '', $request->nominal)
    ]);

    
    $request->validate([
        'tanggal' => 'required|date',
        'id_bank' => 'required',
        'nominal' => 'required|numeric|min:0',
        'id_kategori_transaksi' => 'required',
        'lampiran' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
    ], [
        'tanggal.required' => 'Tanggal wajib diisi',
        'id_bank.required' => 'Bank wajib dipilih',
        'nominal.required' => 'Nominal wajib diisi',
        'id_kategori_transaksi.required' => 'Kategori wajib dipilih',
        'lampiran.mimes' => 'Lampiran harus berupa file JPG, PNG, atau PDF',
        'lampiran.max' => 'Lampiran maksimal 2MB',
    ]);

    $data = $request->all();
    if ($request->hasFile('lampiran')) {
        $file = $request->file('lampiran');
        $lampiranPath = $file->store('asset/pemasukan', 'public');
        $data['lampiran'] = $lampiranPath;
    }

    
    $pemasukan = Pemasukan::create($data);

    
    $kategori = KategoriTransaksi::find($request->id_kategori_transaksi);

    if ($kategori && $kategori->nama_kategori === 'Pembayaran Piutang') {
        
        $request->validate([
            'id_piutang' => 'required|exists:piutang,id',
        ], [
            'id_piutang.required' => 'Piutang harus dipilih',
            'id_piutang.exists' => 'Piutang tidak ditemukan',
        ]);

        $piutang = Piutang::find($request->id_piutang);

        
        $piutang->terbayar += $request->nominal;
        $piutang->sisa_bayar -= $request->nominal;

        if ($piutang->sisa_bayar <= 0) {
            $piutang->status = 'Lunas';
            $piutang->sisa_bayar = 0;
            $piutang->tgl_pelunasan = now();
        }

        $piutang->save();

       
        $pemasukan->id_piutang = $piutang->id;
        $pemasukan->save();
    }

    return response()->json(['status' => 'success']);
}



    public function edit(Pemasukan $pemasukan)
    {
        $pemasukan->load(['piutang']);
        
        return response()->json([
            'status' => 'success',
            'data' => $pemasukan,
            'lampiran_url' => $pemasukan->lampiran 
                ? asset('storage/' . $pemasukan->lampiran)
                : null,
        ]);
    }

    public function update(Request $request, Pemasukan $pemasukan)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'id_bank' => 'required|exists:bank,id',
            'nominal' => 'required|numeric',
            'id_kategori_transaksi' => 'required|exists:kategori_transaksi,id',
        ]);

        $pemasukan->update($request->all());

        return response()->json(['status' => 'success']);
    }

    public function destroy(Pemasukan $pemasukan)
    {
        $pemasukan->delete();
        return response()->json(['status' => 'success']);
    }
}
