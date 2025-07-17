<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataAkun;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class DataAkunController extends Controller
{
    public function index(Request $request)
{
    $permissions = HakAksesController::getUserPermissions();

    if ($request->ajax()) {
        $data = DataAkun::query();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($row) use ($permissions) {
                $editUrl = route('akun.edit', $row->id);
                $deleteUrl = route('akun.destroy', $row->id);
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

    return view('admin.data_akun.index', compact('permissions'));
}

    public function create()
    {
        return view('data_akun.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_akun' => 'required|string|max:100',
            'user_id' => 'required|string|max:100',
            'password' => 'required|string|min:6',
        ]);

        DataAkun::create([
            'nama_akun' => $request->nama_akun,
            'user_id' => $request->user_id,
            'password' => Hash::make($request->password), // hash untuk keamanan
        ]);

        return response()->json(['status' => 'success']);
    }

    public function edit($id)
    {
        $akun = DataAkun::findOrFail($id);
        return response()->json(['status' => 'success','data' => $akun]);
    }

    public function update(Request $request, $id)
    {
        $akun = DataAkun::findOrFail($id);

        $request->validate([
            'nama_akun' => 'required|string|max:100',
            'user_id' => 'required|string|max:100',
        ]);

        $akun->nama_akun = $request->nama_akun;
        $akun->user_id = $request->user_id;

        // Update password hanya jika diisi
        if ($request->filled('password')) {
            $akun->password = Hash::make($request->password);
        }

        $akun->save();

        return redirect()->route('akun.index')->with('success', 'Data akun berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $akun = DataAkun::findOrFail($id);
        $akun->delete();

        return redirect()->route('akun.index')->with('success', 'Data akun berhasil dihapus.');
    }
}
