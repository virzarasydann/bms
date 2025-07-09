<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\HakAkses;
use Illuminate\Http\Request;
use App\Models\Pengguna;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\HakAksesController;
use App\Models\LevelUser;

class PenggunaController extends Controller
{
    public function index(Request $request)
    {
        $permissions = HakAksesController::getUserPermissions();

        if ($request->ajax()) {
            $data = Pengguna::orderBy('id', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()

                ->editColumn('role', function ($row) {
                    switch ($row->role) {
                        case 1:
                            return '<span class="badge bg-success text-dark">Super Admin</span>';
                        case 2:
                            return '<span class="badge bg-primary">Pimpinan</span>';
                        case 3:
                            return '<span class="badge bg-info text-dark">Keuangan</span>';
                        case 4:
                            return '<span class="badge bg-danger text-dark">Operator</span>';
                        case 4:
                            return '<span class="badge bg-warning text-dark">Anggota</span>';
                        default:
                            return '<span class="badge bg-secondary">Tidak Diketahui</span>';
                    }
                })

                 ->addColumn('action', function ($row) use ($permissions): string {
                    $editUrl = route('pengguna.edit', $row->id);
                    $deleteUrl = route('pengguna.destroy', $row->id);

                    $btn = '<div class="d-flex justify-content-center">';
                    if ($permissions['edit']) {
                        $btn .= '<button class="btn btn-primary btn-sm mx-1" data-id="' . e($row->id) . '"
                     data-url="' . e($editUrl) . '" data-toggle="modal" data-target="#modalForm" id="edit-button">
                     Edit
                 </button>';
                    }

                    if ($permissions['hapus']) {
                        $btn .= '<form action="' . e($deleteUrl) . '" method="POST" style="display:inline;">
                        ' . csrf_field() . method_field('DELETE') . '
                        <button type="submit" class="delete-button btn btn-danger btn-sm mx-1">
                            Hapus
                        </button>
                        </form>';
                    }

                    $btn .= '</div>';
                    return $btn;
                })

                ->rawColumns(['role', 'action'])
                ->make(true);
        }

        $roles = LevelUser::select('id', 'level_user')->get();

        return view('admin.pengguna.index', compact('roles', 'permissions'));
    }

    public function edit($id)
    {
        $list = Pengguna::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $list,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'surname' => 'required|string|max:255',
            'username'     => 'required|string|max:255|unique:users,username',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'nullable|string|min:6',
            'status'       => 'required|in:AKTIF,BLOKIR',
            'role'         => 'required',
        ], [
            'surname.required' => 'Nama lengkap wajib diisi.',
            'username.required'     => 'Username wajib diisi.',
            'username.unique'       => 'Username sudah digunakan.',
            'email.required'        => 'Email wajib diisi.',
            'email.email'           => 'Format email tidak valid.',
            'email.unique'          => 'Email sudah digunakan.',
            'password.min'          => 'Password minimal 6 karakter.',
            'status.required'       => 'Status wajib dipilih.',
            'status.in'             => 'Status tidak valid.',
            'role.required'         => 'Role wajib dipilih.',
        ]);

        $db = [
            'surname' => $request->surname,
            'username'     => $request->username,
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
            'status'       => $request->status,
            'role'         => $request->role,
        ];

        $user = Pengguna::create($db);

        $menus = Menu::all();

        foreach ($menus as $menu) {
            $akses = [
                'id_user' => $user->id,
                'id_menu' => $menu->id,
            ];

            if ($user->role == 1) {
                $akses['lihat'] = $menu->lihat;
                $akses['tambah'] = $menu->tambah;
                $akses['edit'] = $menu->edit;
                $akses['hapus'] = $menu->hapus;
            } elseif ($user->role == 2) {
                $akses['lihat'] = $menu->lihat;
                $akses['tambah'] = $menu->tambah;
                $akses['edit'] = $menu->edit;
                $akses['hapus'] = $menu->hapus;
            } elseif ($user->role == 3) {
                $akses['lihat'] = $menu->lihat;
                $akses['tambah'] = $menu->tambah;
                $akses['edit'] = $menu->edit;
                $akses['hapus'] = $menu->hapus;
            } elseif ($user->role == 4) {
                $akses['lihat'] = $menu->lihat;
                $akses['tambah'] = $menu->tambah;
                $akses['edit'] = $menu->edit;
                $akses['hapus'] = $menu->hapus;
            } elseif ($user->role == 5) {
                $akses['lihat'] = $menu->lihat;
                $akses['tambah'] = 0;
                $akses['edit'] = 0;
                $akses['hapus'] = 0;
            } else {
                $akses['lihat'] = 0;
                $akses['tambah'] = 0;
                $akses['edit'] = 0;
                $akses['hapus'] = 0;
            }


            HakAkses::create($akses);
        }

        return response()->json(['status' => 'success']);
    }

    public function update(Request $request, $id)
    {
        $data = Pengguna::findOrFail($id);

        $request->validate([
            'surname' => 'required|string|max:255',
            'username'     => 'required|string|max:255|unique:users,username,' . $data->id . ',id',
            'email'        => 'required|email|unique:users,email,' . $data->id . ',id',
            'password'     => 'nullable|string|min:6',
            'status'       => 'required|in:AKTIF,BLOKIR',
            'role'         => 'required',
        ], [
            'surname.required' => 'Nama lengkap wajib diisi.',
            'username.required'     => 'Username wajib diisi.',
            'username.unique'       => 'Username sudah digunakan.',
            'email.required'        => 'Email wajib diisi.',
            'email.email'           => 'Format email tidak valid.',
            'email.unique'          => 'Email sudah digunakan.',
            'password.min'          => 'Password minimal 6 karakter.',
            'status.required'       => 'Status wajib dipilih.',
            'status.in'             => 'Status tidak valid.',
            'role.required'         => 'Role wajib dipilih.',
        ]);

        $db = [
            'surname' => $request->surname,
            'username'     => $request->username,
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
            'status'       => $request->status,
            'role'         => $request->role,
        ];

        $data->update($db);

        return response()->json(['status' => 'success']);
    }

    public function destroy($id)
    {
        $data = Pengguna::findOrFail($id);
        $hakAkses = HakAkses::where('id_user', $data->id)->delete();
        $data->delete();

        return response()->json(['status' => 'success']);
    }
}
