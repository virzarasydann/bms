<?php

namespace App\Http\Controllers;

use App\Models\Pengeluaran;
use App\Models\Bank;
use App\Models\KategoriTransaksi;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\HakAksesController;

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
        $dataKategori = KategoriTransaksi::all();

        return view('admin.keuangan.pengeluaran.index', compact('permissions', 'dataBank', 'dataKategori'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_hutang' => 'nullable|exists:hutang,id',
            'id_piutang' => 'nullable|exists:piutang,id',
            'tanggal' => 'required|date',
            'id_bank' => 'required',
            'nominal' => 'required|numeric|min:0',
            'lampiran' => 'required|file|mimes:jpg,jpeg,png,pdf',
            'id_kategori_transaksi' => 'required',
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
        ]);

        $data = $request->all();

        if ($request->hasFile('lampiran')) {
            $file = $request->file('lampiran');
            $path = $file->store('asset/pengeluaran', 'public');
            $data['lampiran'] = $path;
        }

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
