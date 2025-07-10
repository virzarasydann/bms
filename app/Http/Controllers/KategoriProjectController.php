<?php

namespace App\Http\Controllers;

use App\Models\KategoriProject;
use Illuminate\Http\Request;
use App\Http\Controllers\HakAksesController;
use Yajra\DataTables\Facades\DataTables;

class KategoriProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    

public function index(Request $request)
    {
        $permissions = HakAksesController::getUserPermissions();

        if ($request->ajax()) {
            $data = KategoriProject::orderBy('id', 'asc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) use ($permissions) {
                    $editUrl = route('kategoriProject.edit', $row->id);
                    $deleteUrl = route('kategoriProject.destroy', $row->id);

                    $btn = '<div class="d-flex justify-content-center">';
                    if ($permissions['edit']) {
                        $btn .= '<button class="btn btn-primary btn-xs mx-1 edit-button"
                                    data-id="' . e($row->id) . '"
                                    data-url="' . e($editUrl) . '"
                                    data-toggle="modal"
                                    data-target="#modalForm">Edit</button>';
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

        return view('admin.kategori_project.index', compact('permissions'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori' => 'required|string|max:100',
            'keterangan' => 'nullable|string|max:255',
        ], [
            'kategori.required' => 'Nama jenis sewa wajib diisi.',
        ]);

        $data = KategoriProject::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Kategori project berhasil ditambahkan.',
            'data' => $data
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function show(KategoriProject $kategoriProject)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KategoriProject $kategoriProject)
    {
        return response()->json([
            'status' => 'success',
            'data' => $kategoriProject,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KategoriProject $kategoriProject)
    {
        $validated = $request->validate([
            'kategori' => 'required|string|max:100',
            'keterangan' => 'nullable|string|max:255',
        ], [
            'kategori.required' => 'Nama kategori wajib diisi.',
        ]);

        $kategoriProject->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Kategori project berhasil diperbarui.',
            'data' => $kategoriProject
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KategoriProject $kategoriProject)
    {
        $kategoriProject->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Kategori project berhasil dihapus.'
        ]);
    }
}
