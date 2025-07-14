<?php
namespace App\Http\Controllers;

use App\Models\MutasiSaldo;
use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\HakAksesController;
use Yajra\DataTables\Facades\DataTables;

class MutasiSaldoController extends Controller
{
    public function index(Request $request)
    {
        $permissions = HakAksesController::getUserPermissions();
        if ($request->ajax()) {
            $data = MutasiSaldo::with(['asal', 'tujuan']);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('asal', fn($row) => $row->asal->nama_bank ?? '-')
                ->addColumn('tujuan', fn($row) => $row->tujuan->nama_bank ?? '-')
                ->addColumn('lampiran', function ($row) {
                    if ($row->lampiran) {
                        return '<a href="#" class="btn-preview-lampiran text-primary" data-url="' . asset('storage/' . $row->lampiran) . '">Lihat</a>';
                    }
                    return '-';
                })
                ->addColumn('action', function ($row) use ($permissions) {
                    $editUrl = route('mutasi.edit', $row->id);
                    $deleteUrl = route('mutasi.destroy', $row->id);
                    $btn = '<div class="d-flex justify-content-center">';
                    if ($permissions['edit']) {
                        $btn .= '<button class="btn btn-primary btn-xs mx-1 edit-button" data-id="' . $row->id . '" data-url="' . $editUrl . '" data-toggle="modal" data-target="#modalForm">Edit</button>';
                    }
                    if ($permissions['hapus']) {
                        $btn .= '<form action="' . $deleteUrl . '" method="POST" style="display:inline;">'
                              . csrf_field() . method_field('DELETE') .
                            '<button type="submit" class="delete-button btn btn-danger btn-xs mx-1">Hapus</button></form>';
                    }
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['lampiran', 'action'])
                ->make(true);
        }

        $dataBank = Bank::all();
        return view('admin.keuangan.mutasi_saldo.index', compact('permissions', 'dataBank'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required',
            'rekening_asal' => 'required',
            'rekening_tujuan' => 'required',
            'nominal' => 'required',
            'lampiran' => 'nullable|file|max:2048|mimes:jpg,jpeg,png,pdf',
            'keterangan' => 'required'
        ], [
            'tanggal.required' => 'Tanggal wajib diisi',
            'rekening_asal.required' => 'Rekening asal wajib diisi',
            'rekening_tujuan.required' => 'Rekening tujuan wajib diisi',
            'nominal.required' => 'Nominal wajib diisi',
            'lampiran.max' => 'Ukuran lampiran maksimal 2MB',
            'lampiran.mimes' => 'Lampiran harus berupa file JPG, PNG, JPEG, atau PDF',
            'keterangan' => 'Keterangan wajib diisi'
        ]);

        $data = $request->except(['lampiran']);
        if ($request->hasFile('lampiran')) {
            $data['lampiran'] = $request->file('lampiran')->store('asset/mutasisaldo', 'public');
        }

        MutasiSaldo::create($data);
        return response()->json(['status' => 'success']);
    }


    public function update(Request $request, MutasiSaldo $mutasi)
    {
        $request->validate([
            'tanggal' => 'required',
            'rekening_asal' => 'required',
            'rekening_tujuan' => 'required',
            'nominal' => 'required',
            'lampiran' => 'nullable|file|max:2048|mimes:jpg,jpeg,png,pdf',
        ], [
            'tanggal.required' => 'Tanggal wajib diisi',
            'rekening_asal.required' => 'Rekening asal wajib diisi',
            'rekening_tujuan.required' => 'Rekening tujuan wajib diisi',
            'nominal.required' => 'Nominal wajib diisi',
            'lampiran.max' => 'Ukuran lampiran maksimal 2MB',
            'lampiran.mimes' => 'Lampiran harus berupa file JPG, PNG, JPEG, atau PDF',
        ]);

        $data = $request->except(['lampiran']);

        if ($request->hasFile('lampiran')) {
            $data['lampiran'] = $request->file('lampiran')->store('asset/mutasisaldo', 'public');
        }

        $mutasi->update($data);

        return response()->json(['status' => 'success']);
    }

    public function destroy(MutasiSaldo $mutasi)
    {
        $mutasiSaldo->delete();
        return response()->json(['status' => 'success']);
    }

    public function edit(MutasiSaldo $mutasi)
    {
        // $mutas->load(['bank', 'kategoriTransaksi']);
        return response()->json([
            'status' => 'success',
            'data' => $mutasi,
            'lampiran_url' => $mutasi->lampiran ? asset('storage/' . $mutasi->lampiran) : null,
        ]);
    }
}

