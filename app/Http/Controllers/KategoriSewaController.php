<?php

namespace App\Http\Controllers;

use App\Models\KategoriSewa;
use Illuminate\Http\Request;
use App\Http\Controllers\HakAksesController;
use Yajra\DataTables\Facades\DataTables;

class KategoriSewaController extends Controller
{
    public function index(Request $request)
    {
        $permissions = HakAksesController::getUserPermissions();

        if ($request->ajax()) {
            $data = KategoriSewa::orderBy('id', 'asc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) use ($permissions) {
                    $editUrl = route('kategoriSewa.edit', $row->id);
                    $deleteUrl = route('kategoriSewa.destroy', $row->id);
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
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.master_data.kategori_sewa.index', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_sewa' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ], [
            'jenis_sewa.required' => 'Jenis sewa wajib diisi.',
        ]);

        KategoriSewa::create($request->only('jenis_sewa', 'keterangan'));

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil disimpan',
        ]);
    }

    public function edit(KategoriSewa $kategoriSewa)
    {
        return response()->json([
            'status' => 'success',
            'data' => $kategoriSewa
        ]);
    }

    public function update(Request $request, KategoriSewa $kategoriSewa)
    {
        $request->validate([
            'jenis_sewa' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ], [
            'jenis_sewa.required' => 'Jenis sewa wajib diisi.',
        ]);

        $kategoriSewa->update($request->only('jenis_sewa', 'keterangan'));

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil diperbarui',
        ]);
    }

    public function destroy(KategoriSewa $kategoriSewa)
    {
        $kategoriSewa->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil dihapus',
        ]);
    }
}
