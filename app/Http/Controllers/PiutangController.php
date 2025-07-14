<?php

namespace App\Http\Controllers;

use App\Models\Piutang;
use App\Models\Bank;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\HakAksesController;

class PiutangController extends Controller
{
    public function index(Request $request)
    {
        $permissions = HakAksesController::getUserPermissions();

        if ($request->ajax()) {
            $data = Piutang::with('bank')->orderBy('id', 'desc');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('bank', fn ($row) => $row->bank->nama_bank ?? '-')
                ->addColumn('action', function ($row) use ($permissions) {
                    $editUrl = route('piutang.edit', $row->id);
                    $deleteUrl = route('piutang.destroy', $row->id);

                    $btn = '<div class="d-flex justify-content-center">';
                    if ($permissions['edit']) {
                        $btn .= '<button class="btn btn-primary btn-xs mx-1" data-id="' . $row->id . '"
                            data-url="' . $editUrl . '" id="edit-button">Edit</button>';
                    }
                    if ($permissions['hapus']) {
                        $btn .= '<form action="' . $deleteUrl . '" method="POST">' .
                                csrf_field() . method_field('DELETE') .
                                '<button type="submit" class="delete-button btn btn-danger btn-xs mx-1">Hapus</button></form>';
                    }
                    return $btn . '</div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $dataBank = Bank::all();
        return view('admin.keuangan.piutang.index', compact('permissions', 'dataBank'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_bank' => 'required',
            'tanggal_piutang' => 'required|date',
            'nominal' => 'required|numeric',
            'deskripsi' => 'required',
            'lampiran' => 'required',
           
        ], [
            'id_bank.required' => 'Rekening wajib diisi',
            'tanggal_piutang.required' => 'Tanggal wajib diisi',
            'nominal.required' => 'Nominal wajib diisi',
            'deskripsi.required' => 'Deskripsi wajib diisi',
            'lampiran.required' => 'Lampiran wajib diisi',
           
            
        ]);

        Piutang::create($request->all());

        return response()->json(['status' => 'success']);
    }

    public function edit(Piutang $piutang)
    {
        return response()->json(['status' => 'success', 'data' => $piutang]);
    }

    public function update(Request $request, Piutang $piutang)
    {
        $request->validate([
            'id_bank' => 'required',
            'tanggal_piutang' => 'required|date',
            'nominal' => 'required|numeric',
        ]);

        $piutang->update($request->all());

        return response()->json(['status' => 'success']);
    }

    public function destroy(Piutang $piutang)
    {
        $piutang->delete();
        return response()->json(['status' => 'success']);
    }
}
