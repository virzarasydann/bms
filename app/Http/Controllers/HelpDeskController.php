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
                        $btn .= '<button class="btn btn-primary btn-xs mx-1" data-id="' . $row->id . '" data-url="' . $editUrl . '" data-toggle="modal" data-target="#modalForm" id="edit-button">Edit</button>';
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
            'deskripsi_komplen' => 'required',
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
        $helpdesk->load('project');

        return response()->json([
            'status' => 'success',
            'data' => $helpdesk,
        ]);
    }


    public function update(Request $request, HelpDesk $helpdesk)
    {
        $request->validate([
            'id_project' => 'required',
            'tgl_komplen' => 'required|date',
            'tgl_target_selesai' => 'required|date',
            'deskripsi_komplen' => 'required',
            'penanggung_jawab' => 'required',
            'status_komplen' => 'required|in:open,progress,closed',
        ]);

        $helpdesk->update($request->all());

        return response()->json(['status' => 'success']);
    }

    public function destroy(HelpDesk $helpdesk)
    {
        $helpdesk->delete();
        return response()->json(['status' => 'success']);
    }
}
