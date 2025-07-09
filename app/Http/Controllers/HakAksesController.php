<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\User;
use App\Models\HakAkses;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;

class HakAksesController extends Controller
{

    public function hak_akses(Request $request)
    {
        $permissions = $this->getUserPermissions();

        $users = User::select('id', 'username')->get();

        return view('admin.hak_akses.index', compact('users', 'permissions'));
    }

    public static function getUserPermissions()
    {
        $routeName = request()->route()->getName();
        $userId = Auth::id();

        $menu = Menu::where('route_name', $routeName)->first();

        if ($menu) {
            $hakAkses = HakAkses::where('id_user', $userId)
                ->where('id_menu', $menu->id)
                ->first();

            return $hakAkses ? [
                'tambah' => $hakAkses->tambah,
                'edit' => $hakAkses->edit,
                'hapus' => $hakAkses->hapus,
            ] : [
                'tambah' => 0,
                'edit' => 0,
                'hapus' => 0,
            ];
        }

        return [
            'tambah' => 0,
            'edit' => 0,
            'hapus' => 0,
        ];
    }

    public function getHakAkses(Request $request)
    {
        if (!$request->has('id_user') || empty($request->id_user)) {
            return response()->json(['data' => []]);
        }

        $permissions = $request->permissions;

        $hakAkses = HakAkses::with('menu')
            ->where('id_user', $request->id_user)
            ->get();

        $sorted = collect();

        $induk = $hakAkses->filter(fn($row) => $row->menu && $row->menu->id_parent == 0)
            ->sortBy(fn($row) => $row->menu->urutan ?? 0);

        foreach ($induk as $indukItem) {
            $sorted->push($indukItem);

            $anak = $hakAkses->filter(fn($row) => $row->menu && $row->menu->id_parent == $indukItem->id_menu)
                ->sortBy(fn($row) => $row->menu->urutan ?? 0);

            foreach ($anak as $anakItem) {
                $sorted->push($anakItem);
            }
        }

        return DataTables::of($sorted)
            ->addIndexColumn()
            ->addColumn('induk_menu', function ($row) {
                if (!$row->menu) return '-';
                if ($row->menu->id_parent == 0) return 'Induk';
                return Menu::find($row->menu->id_parent)?->title ?? 'Induk';
            })
            ->addColumn('title', fn($row) => $row->menu->title ?? '-')
            ->addColumn('route_name', fn($row) => $row->menu->route_name ?? '-')
            ->addColumn('lihat', function ($row) use ($permissions) {
                if (!$row->menu || $row->menu->lihat == 0) return '';
                $checked = $row->lihat == 1 ? 'checked' : '';
                $disabled = ($permissions['edit'] ?? 1) == 0 ? 'disabled' : '';
                return "<div class='text-center'><input type='checkbox' class='form-check-input' name='lihat[{$row->id}]' $checked $disabled></div>";
            })
            ->addColumn('tambah', function ($row) use ($permissions) {
                if (!$row->menu || $row->menu->tambah == 0) return '';
                $checked = $row->tambah == 1 ? 'checked' : '';
                $disabled = ($permissions['edit'] ?? 1) == 0 ? 'disabled' : '';
                return "<div class='text-center'><input type='checkbox' class='form-check-input' name='tambah[{$row->id}]' $checked $disabled></div>";
            })
            ->addColumn('edit', function ($row) use ($permissions) {
                if (!$row->menu || $row->menu->edit == 0) return '';
                $checked = $row->edit == 1 ? 'checked' : '';
                $disabled = ($permissions['edit'] ?? 1) == 0 ? 'disabled' : '';
                return "<div class='text-center'><input type='checkbox' class='form-check-input' name='edit[{$row->id}]' $checked $disabled></div>";
            })
            ->addColumn('hapus', function ($row) use ($permissions) {
                if (!$row->menu || $row->menu->hapus == 0) return '';
                $checked = $row->hapus == 1 ? 'checked' : '';
                $disabled = ($permissions['edit'] ?? 1) == 0 ? 'disabled' : '';
                return "<div class='text-center'><input type='checkbox' class='form-check-input' name='hapus[{$row->id}]' $checked $disabled></div>";
            })
            ->rawColumns(['induk_menu', 'title', 'route_name', 'lihat', 'tambah', 'edit', 'hapus'])
            ->make(true);
    }

    public function updateHakAkses(Request $request)
    {
        $hakAksesData = $request->hak_akses_data;

        if (isset($hakAksesData['lihat'])) {
            foreach ($hakAksesData['lihat'] as $id => $lihat) {

                $hakAkses = HakAkses::where('id', $id)->first();

                if ($hakAkses) {
                    $hakAkses->lihat = $lihat;
                    $hakAkses->tambah = $hakAksesData['tambah'][$id] ?? 0;
                    $hakAkses->edit = $hakAksesData['edit'][$id] ?? 0;
                    $hakAkses->hapus = $hakAksesData['hapus'][$id] ?? 0;

                    $hakAkses->save();

                    $userId = Auth::id();

                    $hakAkses = HakAkses::where('id_user', $userId)
                        ->where('lihat', 1)
                        ->get();

                    $allowedMenuIds = $hakAkses->pluck('id_menu')->toArray();

                    $getmenus = Menu::where('id_parent', 0)
                        ->whereIn('id', $allowedMenuIds)
                        ->orderBy('urutan')
                        ->with(['children' => function ($query) use ($allowedMenuIds) {
                            $query->whereIn('id', $allowedMenuIds);
                        }])
                        ->get();


                    session([
                        'getmenus' => $getmenus
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Hak Akses telah diperbarui.',
            ]);
        }

        return response()->json(['success' => false]);
    }
}
