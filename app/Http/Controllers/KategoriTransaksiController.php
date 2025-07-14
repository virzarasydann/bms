<?php

namespace App\Http\Controllers;

use App\Models\KategoriTransaksi;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\HakAksesController;

class KategoriTransaksiController extends Controller
{
    public function index(Request $request)
    {
        $permissions = HakAksesController::getUserPermissions();

        if ($request->ajax()) {
            $data = KategoriTransaksi::select(['id', 'nama_kategori', 'jenis_transaksi']);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) use ($permissions) {
                    $editUrl = route('kategoriTransaksi.edit', $row->id);
                    $deleteUrl = route('kategoriTransaksi.destroy', $row->id);
                    $btn = '<div class="d-flex justify-content-center">';
                    if ($permissions['edit']) {
                        $btn .= '<button class="btn btn-primary btn-xs mx-1" data-id="' . $row->id . '" data-url="' . $editUrl . '" data-toggle="modal" data-target="#modalForm" id="edit-button">Edit</button>';
                    }
                    if ($permissions['hapus']) {
                        $btn .= '<form action="' . $deleteUrl . '" method="POST" style="display:inline;">'
                            . csrf_field() . method_field('DELETE') .
                            '<button type="submit" class="delete-button btn btn-danger btn-xs mx-1">Hapus</button>
                        </form>';
                    }
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.keuangan.kategori_transaksi.index', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required',
            'jenis_transaksi' => 'required',
        ], [
            'nama_kategori.required' => 'Nama kategori wajib diisi',
            'jenis_transaksi.required' => 'Jenis transaksi wajib dipilih',
            'jenis_transaksi.in' => 'Jenis transaksi tidak valid',
        ]);

        KategoriTransaksi::create($request->all());

        return response()->json(['status' => 'success']);
    }

    public function edit(KategoriTransaksi $kategoriTransaksi)
    {
        return response()->json([
            'status' => 'success',
            'data' => $kategoriTransaksi
        ]);
    }

    public function update(Request $request, KategoriTransaksi $kategoriTransaksi)
    {
        $request->validate([
            'nama_kategori' => 'required',
            'jenis_transaksi' => 'required',
        ]);

        $kategoriTransaksi->update($request->all());

        return response()->json(['status' => 'success']);
    }

    public function destroy(KategoriTransaksi $kategoriTransaksi)
    {
        $kategoriTransaksi->delete();
        return response()->json(['status' => 'success']);
    }
}
