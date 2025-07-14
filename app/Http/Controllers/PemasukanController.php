<?php

namespace App\Http\Controllers;

use App\Models\Pemasukan;
use App\Models\Bank;
use App\Models\KategoriTransaksi;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PemasukanController extends Controller
{
    public function index(Request $request)
    {
        $permissions = HakAksesController::getUserPermissions();
        $dataBank = Bank::all();
        $dataKategori = KategoriTransaksi::where('jenis_transaksi', 'pemasukan')->get();

        if ($request->ajax()) {
            $query = Pemasukan::with(['bank', 'kategoriTransaksi'])->orderBy('tanggal', 'desc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('bank', fn ($row) => $row->bank->nama_bank ?? '-')
                ->addColumn('kategori', fn ($row) => $row->kategoriTransaksi->nama_kategori ?? '-')
                ->addColumn('action', function ($row) use ($permissions) {
                    $editUrl = route('pemasukan.edit', $row->id);
                    $deleteUrl = route('pemasukan.destroy', $row->id);
                    $btn = '<div class="text-center">';
                    if ($permissions['edit']) {
                        $btn .= '<button class="btn btn-primary btn-xs mx-1" data-id="' . $row->id . '" data-url="' . $editUrl . '" data-toggle="modal" data-target="#modalForm" id="edit-button">Edit</button>';
                    }
                    if ($permissions['hapus']) {
                        $btn .= '<form action="' . $deleteUrl . '" method="POST" style="display:inline;">' .
                                csrf_field() . method_field('DELETE') .
                                '<button type="submit" class="delete-button btn btn-danger btn-xs mx-1">Hapus</button>' .
                                '</form>';
                    }
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.keuangan.pemasukan.index', compact('permissions', 'dataBank', 'dataKategori'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'id_bank' => 'required',
            'nominal' => 'required|numeric',
            'id_kategori_transaksi' => 'required',
        ], [
            'tanggal.required' => 'Tanggal wajib diisi',
            'id_bank.required' => 'Bank wajib dipilih',
            'nominal.required' => 'Nominal wajib diisi',
            'id_kategori_transaksi.required' => 'Kategori wajib dipilih',
        ]);

        Pemasukan::create($request->all());

        return response()->json(['status' => 'success']);
    }

    public function edit(Pemasukan $pemasukan)
    {
        return response()->json([
            'status' => 'success',
            'data' => $pemasukan
        ]);
    }

    public function update(Request $request, Pemasukan $pemasukan)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'id_bank' => 'required|exists:bank,id',
            'nominal' => 'required|numeric',
            'id_kategori_transaksi' => 'required|exists:kategori_transaksi,id',
        ]);

        $pemasukan->update($request->all());

        return response()->json(['status' => 'success']);
    }

    public function destroy(Pemasukan $pemasukan)
    {
        $pemasukan->delete();
        return response()->json(['status' => 'success']);
    }
}
