<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\KategoriProject;
use App\Models\Customer;
use App\Models\Pemasukan;
use App\Models\Piutang;
use App\Models\Bank;
use Illuminate\Http\Request;
use App\Http\Controllers\HakAksesController;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;


class ProjectController extends Controller
{
    
    public function index(Request $request)
    {
        $permissions = HakAksesController::getUserPermissions();
        $dataCustomer = Customer::all();
        $dataKategori = KategoriProject::all();
        $dataBank = Bank::all();
        if ($request->ajax()) {
            $data = Project::with(['customer'])->orderBy('id', 'desc');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('customer', fn ($row) => $row->customer->nama ?? '-')
                ->addColumn('action', function ($row) use ($permissions) {
                    $editUrl = route('project.edit', $row->id);
                    $deleteUrl = route('project.destroy', $row->id);
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
                ->editColumn('status_pembayaran', fn ($row) => ucfirst($row->status_pembayaran))
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.project.index', compact('permissions', 'dataCustomer','dataKategori','dataBank'));
    }


    public function store(Request $request)
{
    $request->merge([
        'nilai_project' => str_replace('.', '', $request->nilai_project),
        
    ]);
    $request->validate([
        'id_kategori_project' => 'required',
        'nama_project' => 'required|string|max:255',
        'id_customer' => 'required',
        'tgl_kontrak' => 'required|date',
        'tanggal_selesai' => 'required|date',
        'nilai_project' => 'required|numeric|min:0',
        'penanggung_jawab' => 'required|string|max:255',
        'status_pembayaran' => 'required',
    ], [
        'id_kategori_project.required' => 'Kategori Project wajib diisi',
        'nama_project.required' => 'Nama project wajib diisi.',
        'id_customer.required' => 'Customer wajib diisi',
        'tgl_kontrak.required' => 'Tanggal Kontrak wajib diisi',
        'tanggal_selesai.required' => "Tanggal Selesai wajib diisi",
        'nilai_project.required' => 'Nilai project wajib diisi.',
        'penanggung_jawab.required' => 'Penanggung jawab wajib diisi.',
        'status_pembayaran.required' => 'Status pembayaran wajib dipilih.'
    ]);
    
    $project = [];

   
    if ($request->status_pembayaran === 'Paid') {
        $project = Project::create($request->all());
        Pemasukan::create([
            'id_project' => $project->id,
            'id_hutang' => 0,
            'id_piutang' => 0,
            'tanggal' => now()->format('Y-m-d'),
            'id_bank' => $request->id_bank, 
            'nominal' => $request->nilai_project,
            'id_kategori_transaksi' => 0, 
            'keterangan' => 'Pemasukan dari project ' . $project->nama_project,
        ]);
    }


    if ($request->status_pembayaran === 'Cicil') {
        $project = Project::create($request->all());
        Piutang::create([
            'id_project' => $project->id,
            'tanggal_piutang' => now()->format('Y-m-d'),
            'id_bank' => $request->id_bank,
            'deskripsi' => 'Piutang dari Project ' . $project->nama_project,
            'nominal' => $request->nilai_project,
            'status' => 'Belum Lunas',
            'terbayar' => 0,
            'sisa_bayar' => $request->nilai_project,
        ]);
    }
    
    if ($request->status_pembayaran === 'DP') {
        $project = Project::create($request->all());
        $request->merge([
            'nilai_dp' => str_replace('.', '', $request->nilai_dp),
        ]);
        $nilaiDP = $request->nilai_dp;
    
        $piutang = Piutang::create([
            'id_project' => $project->id,
            'tanggal_piutang' => now(),
            'id_bank' => $request->id_bank,
            'deskripsi' => 'Piutang  (DP) dari project' . $project->nama_project,
            'nominal' => $request->nilai_project,
            'terbayar' => $nilaiDP,
            'sisa_bayar' => $request->nilai_project - $nilaiDP,
            'status' => 'Belum Lunas',
        ]);
    
        Pemasukan::create([
            'id_project' => $project->id,
            'id_piutang' => $piutang->id,
            'tanggal' => now(),
            'id_bank' => $request->id_bank,
            'nominal' => $nilaiDP,
            'id_kategori_transaksi' => 0, // atau bisa defaultkan
            'keterangan' => 'Pembayaran DP untuk Project ' . $project->nama_project,
        ]);
    }

    return response()->json([
        'status' => 'success',
        'message' => 'Project berhasil ditambahkan',
    ]);
}


    public function edit(Project $project)
    {
        $project->load(['piutang', 'pemasukan']);
        $idBank = $project->pemasukan->id_bank ?? $project->piutang->id_bank ?? null;

        $nominalDp = null;
        if ($project->status_pembayaran === 'DP') {
            $nominalDp = $project->piutang->terbayar ?? null;
        }

        return response()->json([
            'status' => 'success',
            'data' => $project,
            'id_bank' => $idBank,
            'nominal_dp' => $nominalDp,
        ]);
    }


    public function update(Request $request, Project $project)
    {
        $request->validate([
            'id_kategori_project' => 'required|exists:kategori_project,id',
            'nama_project' => 'required|string|max:255',
            'id_customer' => 'required|exists:customer,id',
            'tgl_kontrak' => 'required|date',
            'tanggal_selesai' => 'required|date',
            'nilai_project' => 'required|numeric|min:0',
            'penanggung_jawab' => 'required|string|max:255',
            'status_pembayaran' => 'required',
        ]);

        $project->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Project berhasil diperbarui',
        ]);
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Project berhasil dihapus',
        ]);
    }
}
