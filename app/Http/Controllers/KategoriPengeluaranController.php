<?php

namespace App\Http\Controllers;

use App\Models\KategoriPengeluaran;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\HakAksesController;

class KategoriPengeluaranController extends Controller
{
    public function index(Request $request)
    {
        $permissions = HakAksesController::getUserPermissions();

        if ($request->ajax()) {
            $opd = KategoriPengeluaran::orderBy('id', 'asc');

            return DataTables::of($opd)
                ->addIndexColumn()
                 ->addColumn('action', function ($row) use ($permissions): string {
                    $editUrl = route('kategoriPengeluaran.edit', $row->id);
                    $deleteUrl = route('kategoriPengeluaran.destroy', $row->id);

                    $btn = '<div class="d-flex justify-content-center">';
                    if ($permissions['edit']) {
                    $btn .= '<button class="btn btn-primary btn-xs mx-1" data-id="' . e($row->id) . '"
                         data-url="' . e($editUrl) . '" data-toggle="modal" data-target="#modalForm" id="edit-button">
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
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.kategori_pengeluaran.index', compact('permissions'));
    }

    public function edit($id)
    {
        $list = KategoriPengeluaran::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $list,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|max:255',
            'jenis_kategori' => 'required',
        ], [
            'nama.required' => 'Nama wajib diisi',
            'nama.max' => 'Nama maximal 255 Karakter',
            'jenis_kategori.required' => 'Jenis Kategori wajib diisi.',
        ]);

        $db = [
            'nama'              =>       $request->nama,
            'jenis_kategori'    =>       $request->jenis_kategori,
        ];

        KategoriPengeluaran::create($db);

        return response()->json(['status' => 'success']);
    }

    public function update(Request $request, $id)
    {
        $data = KategoriPengeluaran::findOrFail($id);

        $request->validate([
            'nama' => 'required|max:255',
            'jenis_kategori' => 'required',
        ], [
            'nama.required' => 'Nama wajib diisi',
            'nama.max' => 'Nama maximal 255 Karakter',
            'jenis_kategori.required' => 'Jenis Kategori wajib diisi.',
        ]);

       $db = [
            'nama'              =>       $request->nama,
            'jenis_kategori'    =>       $request->jenis_kategori,
        ];

        $data->update($db);

        return response()->json(['status' => 'success']);
    }

    public function destroy($id)
    {
        $data = KategoriPengeluaran::findOrFail($id);
        $data->delete();

        return response()->json(['status' => 'success']);
    }
}
