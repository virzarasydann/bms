<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Hutang;
use App\Models\Pemasukan;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\KategoriTransaksi;

class HutangController extends Controller
{
    public function index(Request $request)
    {
        $permissions = HakAksesController::getUserPermissions();

        if ($request->ajax()) {
            $data = Hutang::with('bank');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('bank', fn($row) => $row->bank->nama_bank ?? '-')
                ->addColumn('lampiran', fn($row) => $row->lampiran
                    ? '<a href="' . asset('storage/' . $row->lampiran) . '" class="btn-preview-lampiran text-primary" data-url="' . asset('storage/' . $row->lampiran) . '">Lihat</a>'
                    : '-')
                ->addColumn('action', function ($row) use ($permissions) {
                    $editUrl = route('hutang.edit', $row->id);
                    $deleteUrl = route('hutang.destroy', $row->id);
                    $btn = '<div class="d-flex justify-content-center">';

                    if ($permissions['edit']) {
                        $btn .= '<button class="btn btn-primary btn-xs mx-1 edit-button" data-id="' . $row->id . '" data-url="' . $editUrl . '" data-toggle="modal" data-target="#modalForm">Edit</button>';
                    }

                    if ($permissions['hapus']) {
                        $btn .= '<form action="' . $deleteUrl . '" method="POST" style="display:inline;">' . csrf_field() . method_field('DELETE') . '<button type="submit" class="btn btn-danger btn-xs mx-1 delete-button">Hapus</button></form>';
                    }

                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['lampiran', 'action'])
                ->make(true);
        }

        $dataBank = Bank::all();
        return view('admin.keuangan.hutang.index', compact('permissions', 'dataBank'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'nominal' => str_replace('.', '', $request->nominal),
            'sisa_bayar' => str_replace('.', '', $request->nominal),
            'terbayar' => str_replace('.', '', $request->terbayar ?? '0'),
        ]);
        $request->validate([
            'tanggal_hutang' => 'required|date',
            'deskripsi' => 'required',
            'id_bank' => 'required',
            'nominal' => 'required|numeric',
            'lampiran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
          
            'terbayar' => 'required|numeric',
            'sisa_bayar' => 'required|numeric',
            'tgl_pelunasan' => 'nullable|date',
            'terbayar' => 'nullable|numeric|min:0',
            'sisa_bayar' => 'nullable|numeric|min:0',
            'status' => 'belum lunas'
        ], [
            'tanggal_hutang.required' => 'Tanggal hutang wajib diisi',
            'deskripsi.required' => 'Deskripsi wajib diisi',
            'id_bank.required' => 'Rekening wajib dipilih',
            'nominal.required' => 'Nominal wajib diisi',
            'lampiran.mimes' => 'Format file tidak valid',
            
        ]);
       
        $data = $request->except('lampiran');
        if ($request->hasFile('lampiran')) {
            $file = $request->file('lampiran')->store('asset/hutang', 'public');
            $data['lampiran'] = $file;
        }
        
        $hutang = Hutang::create($data);
        $kategoriHutang = KategoriTransaksi::where('nama_kategori', 'Pencairan Hutang')->first();

        if (!$kategoriHutang) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'id_kategori_transaksi' => 'Kategori Hutang External tidak ditemukan',
            ]);
        }
        $dataPemasukan = [
            'id_hutang' => $hutang->id,
            'id_project' => 0, // Sesuaikan jika ada logika untuk project
            'id_piutang' => 0, // Sesuaikan jika ada logika untuk piutang
            'tanggal' => $data['tanggal_hutang'],
            'id_bank' => $data['id_bank'],
            'nominal' => $data['nominal'],
            'lampiran' => $data['lampiran'] ?? null,
            'id_kategori_transaksi' => $kategoriHutang->id,
            'keterangan' => 'Pemasukan dari hutang: ' . $data['deskripsi'],
        ];

        // Buat record Pemasukan
        Pemasukan::create($dataPemasukan);

        return response()->json(['status' => 'success']);
    }

    public function edit(Hutang $hutang)
    {
        $url = $hutang->lampiran ? asset('storage/' . $hutang->lampiran) : null;
        return response()->json(['status' => 'success', 'data' => $hutang, 'lampiran_url' => $url]);
    }

    public function update(Request $request, Hutang $hutang)
    {
        $request->validate([
            'tanggal_hutang' => 'required|date',
            'deskripsi' => 'required',
            'id_bank' => 'required',
            'nominal' => 'required|numeric',
            'lampiran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'status' => 'required',
            'terbayar' => 'required|numeric',
            'sisa_bayar' => 'required|numeric',
            'tgl_pelunasan' => 'nullable|date',
        ]);

        $data = $request->except('lampiran');

        if ($request->hasFile('lampiran')) {
            $file = $request->file('lampiran')->store('asset/hutang', 'public');
            $data['lampiran'] = $file;
        }

        $hutang->update($data);

        return response()->json(['status' => 'success']);
    }

    public function destroy(Hutang $hutang)
    {
        $hutang->delete();
        return response()->json(['status' => 'success']);
    }
}


