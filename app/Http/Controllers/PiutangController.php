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
                ->addColumn('lampiran', function ($row) {
                    if ($row->lampiran) {
                        return '<a href="#" class="btn-preview-lampiran text-primary" data-url="' . asset('storage/' . $row->lampiran) . '">Lihat</a>';
                    }
                    return '-';
                })
                ->addColumn('action', function ($row) use ($permissions) {
                    $editUrl = route('piutang.edit', $row->id);
                    $deleteUrl = route('piutang.destroy', $row->id);

                    $btn = '<div class="d-flex justify-content-center">';
                    if ($permissions['edit']) {
                        $label = $row->id_project == 0 ? 'Edit' : 'Detail';
                        $mode = $row->id_project == 0 ? 'edit' : 'detail';
                        
                        $btn .= '<button class="btn btn-primary btn-xs mx-1" data-id="' . $row->id . '"
                            data-url="' . $editUrl . '" data-mode="' . $mode . '" id="edit-button">' . $label . '</button>';
                        
                    }
                    if ($permissions['hapus']) {
                        $btn .= '<form action="' . $deleteUrl . '" method="POST">' .
                                csrf_field() . method_field('DELETE') .
                                '<button type="submit" class="delete-button btn btn-danger btn-xs mx-1">Hapus</button></form>';
                    }
                    return $btn . '</div>';
                })
                ->rawColumns(['action','lampiran'])
                ->make(true);
        }

        $dataBank = Bank::all();
        return view('admin.keuangan.piutang.index', compact('permissions', 'dataBank'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'nominal' => str_replace('.', '', $request->nominal),
            
        ]);
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
        $data = $request->all();
        if ($request->hasFile('lampiran')) {
            $file = $request->file('lampiran');
            $lampiranPath = $file->store('asset/piutang', 'public');
            $data['lampiran'] = $lampiranPath;
        }
        Piutang::create($data);

        return response()->json(['status' => 'success']);
    }

    public function edit(Piutang $piutang)
    {
        return response()->json(['status' => 'success', 'data' => $piutang,  'lampiran_url' => $piutang->lampiran 
        ? asset('storage/' . $piutang->lampiran)
        : null]);
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
