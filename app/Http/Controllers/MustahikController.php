<?php

namespace App\Http\Controllers;

use App\Models\Mustahik;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\HakAksesController;
use Carbon\Carbon;

class MustahikController extends Controller
{
    public function index(Request $request)
    {
        Carbon::setLocale('id');
        $permissions = HakAksesController::getUserPermissions();

        if ($request->ajax()) {
            $data = Mustahik::orderBy('id', 'asc');

            return DataTables::of($data)
                ->addIndexColumn()

                ->addColumn('jenis_kelamin', function ($row) {
                    return $row->jenis_kelamin ?? '-';
                })

                ->addColumn('action', function ($row) use ($permissions): string {
                    $editUrl = route('mustahik.edit', $row->id);
                    $deleteUrl = route('mustahik.destroy', $row->id);

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

        return view('admin.mustahik.index', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap'   => 'required|string|max:255',
            'alamat'         => 'required|string',
            'jenis_kelamin'  => 'required|in:Laki-laki,Perempuan',
            'no_telp'      => 'required|string|max:20',
            'nik'            => 'required|string|max:20',
        ], [
            'nama_lengkap.required'  => 'Nama lengkap wajib diisi',
            'alamat.required'        => 'Alamat wajib diisi',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih',
            'jenis_kelamin.in'       => 'Jenis kelamin tidak valid',
            'no_telp.required'     => 'Nomor telepon wajib diisi',
            'nik.required'           => 'NIK wajib diisi',
        ]);

        Mustahik::create([
            'nama_lengkap'   => $request->nama_lengkap,
            'alamat'         => $request->alamat,
            'jenis_kelamin'  => $request->jenis_kelamin,
            'no_telp'      => $request->no_telp,
            'nik'            => $request->nik,
        ]);

        return response()->json(['message' => 'Data mustahik berhasil disimpan.']);
    }











    public function edit($id)
    {
        $data = Mustahik::findOrFail($id);
        return response()->json($data);
    }

    public function update(Request $request, $id)
{
    $request->validate([
        'nama_lengkap'   => 'required|string|max:255',
        'alamat'         => 'required|string',
        'jenis_kelamin'  => 'required|in:Laki-laki,Perempuan',
        'no_telp'      => 'required|string|max:20',
        'nik'            => 'required|string|max:20',
    ], [
        'nama_lengkap.required'  => 'Nama lengkap wajib diisi',
        'alamat.required'        => 'Alamat wajib diisi',
        'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih',
        'jenis_kelamin.in'       => 'Jenis kelamin tidak valid',
        'no_telp.required'     => 'Nomor telepon wajib diisi',
        'nik.required'           => 'NIK wajib diisi',
    ]);

    $mustahik = Mustahik::findOrFail($id);

    $mustahik->update([
        'nama_lengkap'   => $request->nama_lengkap,
        'alamat'         => $request->alamat,
        'jenis_kelamin'  => $request->jenis_kelamin,
        'no_telp'      => $request->no_telp,
        'nik'            => $request->nik,
    ]);

    return response()->json(['message' => 'Data mustahik berhasil diperbarui.']);
}


    public function destroy($id)
    {
        $mustahik = Mustahik::findOrFail($id);
        $mustahik->delete();

        return response()->json(['message' => 'Data berhasil dihapus.']);
    }






}
