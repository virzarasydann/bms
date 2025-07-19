<?php

namespace App\Http\Controllers;

use App\Models\HelpDesk;
use App\Models\Project;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\HakAksesController;

class HelpDeskController extends Controller
{
    public function index(Request $request)
    {
        $permissions = HakAksesController::getUserPermissions();

        if ($request->ajax()) {
            $data = HelpDesk::with('project')->orderBy('id', 'desc');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('project', fn($row) => $row->project->nama_project ?? '-')
                ->addColumn('action', function ($row) use ($permissions) {
                    $editUrl = route('helpdesk.edit', $row->id);
                    $deleteUrl = route('helpdesk.destroy', $row->id);

                    $btn = '<div class="d-flex justify-content-center">';
                    if ($permissions['edit']) {
                        $btn .= '<a href="' . $editUrl . '" class="btn btn-primary btn-xs mx-1">Edit</a>';

                    }
                    if ($permissions['hapus']) {
                        $btn .= '<form action="' . $deleteUrl . '" method="POST" style="display:inline;">' .
                            csrf_field() . method_field('DELETE') .
                            '<button type="submit" class="delete-button btn btn-danger btn-xs mx-1">Hapus</button></form>';
                    }
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $dataProject = Project::all();
        return view('admin.help_desk.index', compact('permissions', 'dataProject'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_project' => 'required',
            'tgl_komplen' => 'required|date',
            'tgl_target_selesai' => 'required|date',
            'deskripsi_komplen' => 'required|array',
            'penanggung_jawab' => 'required',
            'status_komplen' => 'required|in:open,progress,closed',
        ], [
            'id_project.required' => 'Project wajib dipilih',
            'tgl_komplen.required' => 'Tanggal komplen wajib diisi',
            'tgl_target_selesai.required' => 'Target selesai wajib diisi',
            'deskripsi_komplen.required' => 'Deskripsi wajib diisi',
            'penanggung_jawab.required' => 'Penanggung jawab wajib diisi',
            'status_komplen.required' => 'Status wajib dipilih',
        ]);

        HelpDesk::create($request->all());

        return response()->json(['status' => 'success']);
    }

    public function edit(HelpDesk $helpdesk)
    {
        $dataProject = Project::all();
        $helpdesk->load('project');
        return view('admin.help_desk.edit', compact('helpdesk', 'dataProject'));
    }



    public function update(Request $request, HelpDesk $helpdesk)
{
    $request->validate([
        'tgl_komplen' => 'required|date',
        'id_project' => 'required|exists:project,id',
        'deskripsi_komplen' => 'required|array',
        'penanggung_jawab' => 'required|string',
        'status_komplen' => 'required|in:open,progress,closed',
        'tgl_target_selesai' => 'required|date',
    ]);

    $helpdesk->update([
        'tgl_komplen' => $request->tgl_komplen,
        'id_project' => $request->id_project,
        'deskripsi_komplen' => $request->deskripsi_komplen,
        'penanggung_jawab' => $request->penanggung_jawab,
        'status_komplen' => $request->status_komplen,
        'catatan_penanggung_jawab' => $request->catatan_penanggung_jawab,
        'tgl_target_selesai' => $request->tgl_target_selesai,
    ]);

    return response()->json(['status' => 'success']);
}


    public function destroy(HelpDesk $helpdesk)
    {
        $helpdesk->delete();
        return response()->json(['status' => 'success']);
    }


    public function create(Request $request)
    {
        
        $dataProject = Project::all();
        return view('admin.help_desk.create', compact( 'dataProject'));
    }
}
