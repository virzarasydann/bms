<?php

namespace App\Http\Controllers;

use App\Models\Prospek;
use Illuminate\Http\Request;
use App\Http\Controllers\HakAksesController;
use Yajra\DataTables\Facades\DataTables;


class ProspekController extends Controller
{

    public function index(Request $request)
    {
        $permissions = HakAksesController::getUserPermissions();

        if ($request->ajax()) {
            $customer = Prospek::orderBy('id', 'asc');

            return DataTables::of($customer)
                ->addIndexColumn()
                ->addColumn('action', function ($row) use ($permissions) {
                    $editUrl = route('prospek.edit', $row->id);
                    $deleteUrl = route('prospek.destroy', $row->id);

                    $btn = '<div class="d-flex justify-content-center">';

                    if ($permissions['edit']) {
                        $btn .= '<button class="btn btn-primary btn-xs mx-1 edit-button"
                                    data-id="' . e($row->id) . '"
                                    data-url="' . e($editUrl) . '"
                                    data-toggle="modal"
                                    data-target="#modalForm">
                                    Edit
                                </button>';
                    }

                    if ($permissions['hapus']) {
                        $btn .= '<form action="' . e($deleteUrl) . '" method="POST" style="display:inline;">
                                    ' . csrf_field() . method_field('DELETE') . '
                                    <button type="submit" class="btn btn-danger btn-xs mx-1 delete-button">
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

        return view('admin.master_data.prospek.index', compact('permissions'));
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'nama' => 'required|string',
            'alamat' => 'required|string',
            'no_telp' => 'required|string',
        ], [
            'nama.required' => 'Nama customer wajib diisi.',
            'alamat.required' => 'Alamat wajib diisi.',
            'no_telp.required' => 'Nomor telepon wajib diisi.',
        ]);


        $customer = Prospek::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Customer berhasil ditambahkan.',
            'data' => $customer
        ]);
    }


    /**
     * Show the form for editing the specified resource.
     */
   public function edit(Prospek $prospek)
    {
        return response()->json([
            'status' => 'success',
            'data' => $prospek
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
public function update(Request $request, $id)
{
    $validated = $request->validate([
        'nama' => 'required|string|max:100',
        'alamat' => 'required|string|max:255',
        'no_telp' => 'required|string|max:20',
    ], [
        'nama.required' => 'Nama customer wajib diisi.',
        'alamat.required' => 'Alamat wajib diisi.',
        'no_telp.required' => 'Nomor telepon wajib diisi.',
    ]);

    $customer = Prospek::findOrFail($id);
    $customer->update($validated);

    return response()->json([
        'status' => 'success',
        'message' => 'Data customer berhasil diperbarui.',
        'data' => $customer
    ]);
}


    /**
     * Remove the specified resource from storage.
     */
  public function destroy(Prospek $prospek)
{
    $prospek->delete();

    return response()->json([
        'status' => 'success',
        'message' => 'Data berhasil dihapus.'
    ]);
}

}
