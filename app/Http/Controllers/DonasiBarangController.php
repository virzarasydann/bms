<?php

namespace App\Http\Controllers;

use App\Models\DonasiBarang;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\HakAksesController;
use App\Models\Donatur;
use App\Models\Mustahik;
use Carbon\Carbon;

class DonasiBarangController extends Controller
{
    public function index(Request $request)
    {
        Carbon::setLocale('id');
        $permissions = HakAksesController::getUserPermissions();

        if ($request->ajax()) {
            $opd = DonasiBarang::orderBy('id', 'asc');

            return DataTables::of($opd)
                ->addIndexColumn()

                  ->addIndexColumn()
                  ->addColumn('tgl_donasi', function ($row) {
                        return Carbon::parse($row->tgl_donasi)->translatedFormat('j F Y');
                    })

                 ->addColumn('action', function ($row) use ($permissions): string {
                    $editUrl = route('donasiBarang.edit', $row->id);
                    $deleteUrl = route('donasiBarang.destroy', $row->id);
                     $penyaluranUrl = route('donasiBarang.penyaluran', $row->id);

                    $btn = '<div class="d-flex justify-content-center">';
                    if ($permissions['edit']) {
                    $btn .= '<button class="btn btn-primary btn-xs mx-1" data-id="' . e($row->id) . '"
                         data-url="' . e($editUrl) . '" data-toggle="modal" data-target="#modalForm" id="edit-button">
                         Edit
                     </button>';
                            }


                        $btn .= '<a href="' . e($penyaluranUrl) . '" class="btn btn-success btn-xs mx-1">
                            Penyaluran
                        </a>';

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
        return view('admin.donasi_barang.index', compact('permissions'));
    }



    public function penyaluran($id)
    {
        $donasi = DonasiBarang::findOrFail($id);
        return view('admin.donasi_barang.penyaluran', compact('donasi'));
    }

    public function getPenerima()
    {
        $data = Mustahik::select('id', 'nama_lengkap')->get();

        return response()->json($data);
    }

    public function getDonatur()
    {
        $data = Donatur::select('id', 'nama_lengkap')->get();

        return response()->json($data);
    }



  public function store(Request $request)
    {
        $request->validate([
            'tgl_donasi' => 'required|date',
            'nama_barang' => 'required',
            'jumlah' => 'required',
            'satuan' => 'required',
            'donatur_id' => 'required',
            'keterangan' => 'required',
        ], [
            'tgl_donasi.required' => 'Tanggal donasi wajib diisi.',
            'tgl_donasi.date' => 'Format tanggal donasi tidak valid.',
            'nama_barang.required' => 'Nama barang tidak boleh kosong.',
            'jumlah.required' => 'Jumlah barang wajib diisi.',
            'satuan.required' => 'Satuan barang wajib diisi.',
            'donatur_id.required' => 'Pilih donatur terlebih dahulu.',
            'keterangan.required' => 'Keterangan harus diisi, jangan dikosongin dong!',
        ]);

        $donatur = Donatur::find($request->donatur_id);

        if (!$donatur) {
            return response()->json([
                'status' => 'error',
                'message' => 'Donatur tidak ditemukan.'
            ], 404);
        }

        $db = [
            'tgl_donasi'     => $request->tgl_donasi,
            'nama_barang'    => $request->nama_barang,
            'jumlah'         => $request->jumlah,
            'satuan'         => $request->satuan,
            'nama_donatur'   => $donatur->nama_lengkap,
            'keterangan'     => $request->keterangan,
        ];

        DonasiBarang::create($db);

        return response()->json(['status' => 'success']);
    }



    public function edit($id)
    {
        $list = DonasiBarang::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $list,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tgl_donasi' => 'required|date',
            'nama_barang' => 'required',
            'jumlah' => 'required',
            'satuan' => 'required',
            'donatur_id' => 'required',
            'keterangan' => 'required',
        ], [
            'tgl_donasi.required' => 'Tanggal donasi wajib diisi.',
            'tgl_donasi.date' => 'Format tanggal donasi tidak valid.',
            'nama_barang.required' => 'Nama barang tidak boleh kosong.',
            'jumlah.required' => 'Jumlah barang wajib diisi.',
            'satuan.required' => 'Satuan barang wajib diisi.',
            'donatur_id.required' => 'Pilih donatur terlebih dahulu.',
            'keterangan.required' => 'Keterangan harus diisi, jangan dikosongin dong!',
        ]);

        $donasi = DonasiBarang::find($id);

        if (!$donasi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data donasi tidak ditemukan.'
            ], 404);
        }

        $donatur = Donatur::find($request->donatur_id);

        if (!$donatur) {
            return response()->json([
                'status' => 'error',
                'message' => 'Donatur tidak ditemukan.'
            ], 404);
        }

        $donasi->update([
            'tgl_donasi'     => $request->tgl_donasi,
            'nama_barang'    => $request->nama_barang,
            'jumlah'         => $request->jumlah,
            'satuan'         => $request->satuan,
            'nama_donatur'   => $donatur->nama_lengkap,
            'keterangan'     => $request->keterangan,
        ]);

        return response()->json(['status' => 'success']);
    }


    public function destroy($id)
    {
        $data = DonasiBarang::findOrFail($id);
        $data->delete();

        return response()->json(['status' => 'success']);
    }
}
