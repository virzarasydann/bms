<?php

namespace App\Http\Controllers;

use App\Models\Sewa;
use App\Models\KategoriSewa;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\HakAksesController;

class SewaController extends Controller
{
    public function index(Request $request)
    {
        $permissions = HakAksesController::getUserPermissions();

        if ($request->ajax()) {
            $data = Sewa::with('kategori')->orderBy('id', 'desc');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('kategori', fn($row) => $row->kategori->jenis_sewa ?? '-')
                ->addColumn('action', function ($row) use ($permissions) {
                    $editUrl = route('sewa.edit', $row->id);
                    $deleteUrl = route('sewa.destroy', $row->id);
                    $btn = '<div class="d-flex justify-content-center">';
                    if ($permissions['edit']) {
                        $btn .= '<button class="btn btn-primary btn-xs mx-1" data-url="' . e($editUrl) . '" data-toggle="modal" data-target="#modalForm" id="edit-button">Edit</button>';
                    }
                    if ($permissions['hapus']) {
                        $btn .= '<form action="' . e($deleteUrl) . '" method="POST" style="display:inline;">'
                            . csrf_field() . method_field('DELETE') . 
                            '<button type="submit" class="delete-button btn btn-danger btn-xs mx-1">Hapus</button></form>';
                    }
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $dataKategori = KategoriSewa::all();
        return view('admin.sewa.index', compact('permissions', 'dataKategori'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_kategori_sewa' => 'required',
            'nama_layanan' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'tgl_sewa' => 'required|date',
            'tgl_expired' => 'required|date',
            'vendor' => 'required',
            'url_vendor' => 'required',
        ], [
            'id_kategori_sewa.required' => 'Kategori wajib dipilih.',
            'nama_layanan.required' => 'Nama layanan wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'tgl_sewa.required' => 'Tanggal sewa wajib diisi.',
            'tgl_expired.required' => 'Tanggal expired wajib diisi.',
            'vendor.required' => 'Vendor wajib diisi.',
            'url_vendor.required' => 'URL vendor wajib diisi.',
        ]);

        Sewa::create($request->all());

        return response()->json(['status' => 'success']);
    }

    public function edit(Sewa $sewa)
    {
        return response()->json(['status' => 'success', 'data' => $sewa]);
    }

    public function update(Request $request, Sewa $sewa)
    {
        $request->validate([
            'id_kategori_sewa' => 'required',
            'nama_layanan' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'tgl_sewa' => 'required|date',
            'tgl_expired' => 'required|date',
            'vendor' => 'required',
            'url_vendor' => 'required',
        ]);

        $sewa->update($request->all());

        return response()->json(['status' => 'success']);
    }

    public function destroy(Sewa $sewa)
    {
        $sewa->delete();
        return response()->json(['status' => 'success']);
    }
}
