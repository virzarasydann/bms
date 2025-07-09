<?php

namespace App\Http\Controllers;

use App\Models\Donatur;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\HakAksesController;
use Carbon\Carbon;

class DonaturController extends Controller
{
    public function index(Request $request)
    {
        Carbon::setLocale('id');
        $permissions = HakAksesController::getUserPermissions();

        if ($request->ajax()) {
            $data = Donatur::orderBy('id', 'asc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) use ($permissions): string {
                    $editUrl = route('donatur.edit', $row->id);
                    $deleteUrl = route('donatur.destroy', $row->id);

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

        return view('admin.donatur.index', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap'   => 'required|string|max:255',
            'jenis_kelamin'  => 'required|in:Laki-laki,Perempuan',
            'alamat'         => 'required|string',
            'no_telp'        => 'required|string|max:20',
        ], [
            'nama_lengkap.required'  => 'Nama lengkap wajib diisi',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih',
            'jenis_kelamin.in'       => 'Jenis kelamin tidak valid',
            'alamat.required'        => 'Alamat wajib diisi',
            'no_telp.required'       => 'Nomor telepon wajib diisi',
        ]);

        Donatur::create([
            'nama_lengkap'   => $request->nama_lengkap,
            'jenis_kelamin'  => $request->jenis_kelamin,
            'alamat'         => $request->alamat,
            'no_telp'        => $request->no_telp,
        ]);

        return response()->json(['message' => 'Data donatur berhasil disimpan.']);
    }

    public function edit($id)
    {
        $donatur = Donatur::findOrFail($id);
        return response()->json($donatur);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_lengkap'   => 'required|string|max:255',
            'jenis_kelamin'  => 'required|in:Laki-laki,Perempuan',
            'alamat'         => 'required|string',
            'no_telp'        => 'required|string|max:20',
        ], [
            'nama_lengkap.required'  => 'Nama lengkap wajib diisi',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih',
            'jenis_kelamin.in'       => 'Jenis kelamin tidak valid',
            'alamat.required'        => 'Alamat wajib diisi',
            'no_telp.required'       => 'Nomor telepon wajib diisi',
        ]);

        $donatur = Donatur::findOrFail($id);

        $donatur->update([
            'nama_lengkap'   => $request->nama_lengkap,
            'jenis_kelamin'  => $request->jenis_kelamin,
            'alamat'         => $request->alamat,
            'no_telp'        => $request->no_telp,
        ]);

        return response()->json(['message' => 'Data donatur berhasil diperbarui.']);
    }

    public function destroy($id)
    {
        $donatur = Donatur::findOrFail($id);
        $donatur->delete();

        return response()->json(['message' => 'Data donatur berhasil dihapus.']);
    }
}
