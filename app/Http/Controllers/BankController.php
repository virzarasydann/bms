<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\HakAksesController;

class BankController extends Controller
{
    public function index(Request $request)
    {
        $permissions = HakAksesController::getUserPermissions();

        if ($request->ajax()) {
            $data = Bank::query();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) use ($permissions) {
                    $editUrl = route('bank.edit', $row->id);
                    $deleteUrl = route('bank.destroy', $row->id);
                    $btn = '<div class="d-flex justify-content-center">';

                    if ($permissions['edit']) {
                        $btn .= '<button class="btn btn-primary btn-xs mx-1" data-id="' . $row->id . '" data-url="' . $editUrl . '" id="edit-button" data-toggle="modal" data-target="#modalForm">Edit</button>';
                    }

                    if ($permissions['hapus']) {
                        $btn .= '<form action="' . $deleteUrl . '" method="POST" style="display:inline;">'
                              . csrf_field() . method_field('DELETE') . '
                                <button type="submit" class="delete-button btn btn-danger btn-xs mx-1">Hapus</button>
                            </form>';
                    }

                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.master_data.bank.index', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_bank' => 'required',
            'no_rekening' => 'required',
            'pemilik' => 'required',
        ], [
            'nama_bank.required' => 'Nama Bank wajib diisi',
            'no_rekening.required' => 'No Rekening wajib diisi',
            'pemilik.required' => 'Pemilik wajib diisi'
           
        ]);

        Bank::create($request->all());

        return response()->json(['status' => 'success']);
    }

    public function edit(Bank $bank)
    {
        return response()->json([
            'status' => 'success',
            'data' => $bank,
        ]);
    }

    public function update(Request $request, Bank $bank)
    {
        $request->validate([
            'nama_bank' => 'required',
            'no_rekening' => 'required',
            'pemilik' => 'required',
        ]);

        $bank->update($request->all());

        return response()->json(['status' => 'success']);
    }

    public function destroy(Bank $bank)
    {
        $bank->delete();
        return response()->json(['status' => 'success']);
    }
}
