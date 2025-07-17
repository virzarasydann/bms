<?php

namespace App\Http\Controllers;

use App\Models\Pengeluaran;
use App\Models\Bank;
use App\Models\Hutang;
use App\Models\KategoriTransaksi;
use App\Models\Pemasukan;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\HakAksesController;
use Illuminate\Validation\ValidationException;

class PengeluaranController extends Controller
{
    public function index(Request $request)
    {
        $permissions = HakAksesController::getUserPermissions();
        if ($request->ajax()) {
            $data = Pengeluaran::with(['bank', 'kategoriTransaksi']);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('bank', function ($row) {
                    return $row->bank->nama_bank ?? '-';
                })
                ->addColumn('kategori', function ($row) {
                    return $row->kategoriTransaksi->nama_kategori ?? '-';
                })
                ->addColumn('lampiran', function ($row) {
                    if ($row->lampiran) {
                        return '<a href="#" class="btn-preview-lampiran text-primary" data-url="' . asset('storage/' . $row->lampiran) . '">Lihat</a>';
                    }
                    return '-';
                })
                
                ->addColumn('action', function ($row) use ($permissions) {
                    $editUrl = route('pengeluaran.edit', $row->id);
                    $deleteUrl = route('pengeluaran.destroy', $row->id);
                    $btn = '<div class="d-flex justify-content-center">';

                    if ($permissions['edit']) {
                        $btn .= '<button class="btn btn-primary btn-xs mx-1 edit-button" data-id="' . e($row->id) . '"
                            data-url="' . e($editUrl) . '" data-toggle="modal" data-target="#modalForm">
                            Edit
                        </button>';
                    }

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
                ->rawColumns(['lampiran', 'action'])
                ->make(true);
        }

        
        $dataBank = Bank::all();
        $dataHutang = Hutang::all();
        $dataKategori = KategoriTransaksi::whereNotIn('nama_kategori', [
            'Hutang External',
            'Piutang Reseller',
            'Pembayaran Piutang'
        ])->get();

        return view('admin.keuangan.pengeluaran.index', compact('permissions', 'dataBank', 'dataKategori','dataHutang'));
    }

    public function store(Request $request)
    {
        // Validasi dasar
        $request->merge([
            'nominal' => str_replace('.', '', $request->nominal)
        ]);
        $request->validate([
            'id_hutang' => 'nullable|exists:hutang,id',
            'id_piutang' => 'nullable|exists:piutang,id',
            'tanggal' => 'required|date',
            'id_bank' => 'required|exists:bank,id',
            'nominal' => 'required|numeric|min:0',
            'lampiran' => 'required|file|mimes:jpg,jpeg,png,pdf',
            'id_kategori_transaksi' => 'required|exists:kategori_transaksi,id',
            'keterangan' => 'required|string',
        ], [
            'tanggal.required' => 'Tanggal wajib diisi',
            'id_bank.required' => 'Rekening wajib dipilih',
            'nominal.required' => 'Nominal wajib diisi',
            'nominal.numeric' => 'Nominal harus berupa angka',
            'lampiran.required' => 'Lampiran wajib diisi',
            'keterangan.required' => 'Keterangan wajib diisi',
            'lampiran.mimes' => 'Lampiran harus berupa file JPG, PNG, atau PDF',
            'id_kategori_transaksi.required' => 'Kategori transaksi wajib dipilih',
            'id_hutang.exists' => 'Hutang tidak ditemukan',
            'id_piutang.exists' => 'Piutang tidak ditemukan',
            'id_bank.exists' => 'Bank tidak ditemukan',
            'id_kategori_transaksi.exists' => 'Kategori transaksi tidak ditemukan',
        ]);

        // Cek apakah id_kategori_transaksi adalah "Pembayaran Hutang"
        $kategoriPembayaranHutang = KategoriTransaksi::where('nama_kategori', 'Pembayaran Hutang')->first();

        if ($kategoriPembayaranHutang && $request->id_kategori_transaksi == $kategoriPembayaranHutang->id) {
            // Pastikan id_hutang diisi
            if (empty($request->id_hutang)) {
                throw ValidationException::withMessages([
                    'id_hutang' => 'Hutang wajib dipilih untuk kategori Pembayaran Hutang',
                ]);
            }

            // Ambil data hutang berdasarkan id_hutang
            $hutang = Hutang::find($request->id_hutang);
            if (!$hutang) {
                throw ValidationException::withMessages([
                    'id_hutang' => 'Hutang tidak ditemukan',
                ]);
            }

            // Validasi nominal harus sama dengan sisa_bayar
            $sisaBayar = $hutang->nominal ?? 0;
            if ($request->nominal > $sisaBayar) {
                throw ValidationException::withMessages([
                    'nominal' => 'Nominal melebihi sisa bayar (Rp. ' . number_format($sisaBayar, 0, ',', '.') . ')',
                ]);
            }
        }

        $data = $request->all();

        // Handle file upload
        if ($request->hasFile('lampiran')) {
            $file = $request->file('lampiran');
            $path = $file->store('asset/pengeluaran', 'public');
            $data['lampiran'] = $path;
        }

        // Jika kategori adalah "Pembayaran Hutang", set status ke "Lunas" untuk Pemasukan dan Hutang
        if ($kategoriPembayaranHutang && $request->id_kategori_transaksi == $kategoriPembayaranHutang->id && !empty($request->id_hutang)) {
            // Update status di Hutang menjadi "Lunas"
            $hutang = Hutang::find($request->id_hutang);
            $hutangTerbayar = $hutang->terbayar +  $request->nominal;
            $sisaBayar = $hutang->sisa_bayar - $request->nominal;
            $status = $sisaBayar <= 0 ? 'LUNAS' : 'Belum Lunas';
            if ($hutang) {
                $hutang->update(['status' => $status,'sisa_bayar' => $sisaBayar, 
                'terbayar' => $hutangTerbayar, 'tgl_pelunasan' => $sisaBayar <= 0 ? now() : null]);
            }

            
        }

        // Simpan data Pemasukan
        Pengeluaran::create($data);

        return response()->json(['status' => 'success']);
    }

    public function edit(Pengeluaran $pengeluaran)
    {   
        $pengeluaran->load(['bank', 'kategoriTransaksi']);
        return response()->json([
            'status' => 'success',
            'data' => $pengeluaran,
            'lampiran_url' => $pengeluaran->lampiran 
                ? asset('storage/' . $pengeluaran->lampiran)
                : null,
        ]);
    }

    public function update(Request $request, Pengeluaran $pengeluaran)
    {
        $request->validate([
            'id_hutang' => 'nullable|exists:hutang,id',
            'id_piutang' => 'nullable|exists:piutang,id',
            'tanggal' => 'required|date',
            'id_bank' => 'required',
            'nominal' => 'required|numeric|min:0',
            'lampiran' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'id_kategori_transaksi' => 'required',
            'keterangan' => 'required',
        ], [
            'tanggal.required' => 'Tanggal wajib diisi',
            'id_bank.required' => 'Rekening wajib dipilih',
            'nominal.required' => 'Nominal wajib diisi',
            'keterangan.required' => 'Keterangan wajib diisi',
            'nominal.numeric' => 'Nominal harus berupa angka',
            'lampiran.mimes' => 'Lampiran harus berupa file JPG, PNG, atau PDF',
            'id_kategori_transaksi.required' => 'Kategori transaksi wajib dipilih',
        ]);

        $data = $request->all();

        if ($request->hasFile('lampiran')) {
            $file = $request->file('lampiran');
            $path = $file->store('asset/pengeluaran', 'public');
            $data['lampiran'] = $path;
        }

        $pengeluaran->update($data);

        return response()->json(['status' => 'success']);
    }

    public function destroy(Pengeluaran $pengeluaran)
    {
        $pengeluaran->delete();
        return response()->json(['status' => 'success']);
    }
}
