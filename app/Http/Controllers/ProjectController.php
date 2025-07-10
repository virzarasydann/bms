<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\KategoriProject;
use App\Models\Customer;
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

        return view('admin.project.index', compact('permissions', 'dataCustomer','dataKategori'));
    }


    public function store(Request $request)
    {
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
            
            
        ]);

        Project::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Project berhasil ditambahkan',
        ]);
    }

    public function edit(Project $project)
    {
        return response()->json([
            'status' => 'success',
            'data' => $project
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
