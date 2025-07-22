<?php

namespace App\Http\Controllers;

use App\Models\HelpDesk;
use App\Models\Project;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\HakAksesController;
use Carbon\Carbon;

class HelpDeskController extends Controller
{
  public function index(Request $request)
    {
        $permissions = HakAksesController::getUserPermissions();
        Carbon::setLocale('id');

        if ($request->ajax()) {
            $data = HelpDesk::with('project')->orderBy('id', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('project', fn($row) => $row->project->nama_project ?? '-')

                ->addColumn('tgl_komplen', fn($row) =>
                    Carbon::parse($row->tgl_komplen)->translatedFormat('d F Y')
                )

                ->addColumn('tgl_target_selesai', fn($row) =>
                    Carbon::parse($row->tgl_target_selesai)->translatedFormat('d F Y')
                )

                ->addColumn('status_komplen', function ($row) {
                    $status = strtolower($row->status_komplen);
                    $badgeClass = match ($status) {
                        'open' => 'primary',
                        'progress' => 'success',
                        'closed' => 'danger',
                        default => 'secondary'
                    };
                    return '<span class="badge badge-' . $badgeClass . '">' . ucfirst($status) . '</span>';
                })

                ->addColumn('komplain', function ($row) {
                    $list = '';
                    $komplainArr = is_array($row->komplain) ? $row->komplain : json_decode($row->komplain, true);
                    $catatanArr = is_array($row->catatan_komplain) ? $row->catatan_komplain : json_decode($row->catatan_komplain, true);

                    if (is_array($komplainArr)) {
                        foreach ($komplainArr as $i => $k) {
                            $catatan = $catatanArr[$i] ?? '-';
                            $list .= htmlspecialchars($k) . ' <small>(' . htmlspecialchars($catatan) . ')</small><br>';
                        }
                    }

                    return $list ?: '-';
                })

                ->addColumn('action', function ($row) use ($permissions) {
                    $editUrl = route('helpdesk.edit', $row->id);
                    $deleteUrl = route('helpdesk.destroy', $row->id);

                    $btn = '<div class="d-flex justify-content-center">';
                    if ($permissions['edit']) {
                        $btn .= '<button class="btn btn-primary btn-xs mx-1" data-id="' . $row->id . '" data-url="' . $editUrl . '" id="edit-button" data-toggle="modal" data-target="#modalForm">Edit</button>';
                    }
                    if ($permissions['hapus']) {
                        $btn .= '<form action="' . $deleteUrl . '" method="POST" style="display:inline;">' .
                            csrf_field() . method_field('DELETE') .
                            '<button type="submit" class="delete-button btn btn-danger btn-xs mx-1">Hapus</button></form>';
                    }
                    $btn .= '</div>';
                    return $btn;
                })

                ->rawColumns(['action', 'status_komplen', 'komplain'])
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
            'komplain' => 'nullable|array',
            'catatan_komplain' => 'nullable|array',
            'penanggung_jawab' => 'required',
            'status_komplen' => 'required|in:open,progress,closed',
        ], [
            'id_project.required' => 'Project wajib dipilih',
            'tgl_komplen.required' => 'Tanggal komplen wajib diisi',
            'tgl_target_selesai.required' => 'Target selesai wajib diisi',
            'komplain.required' => 'Komplain wajib diisi',
            'catatan_komplain.required' => 'Catatan Komplain wajib diisi',
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

        return response()->json([
            'status' => 'success',
            'data' => $helpdesk,
            'projects' => $dataProject,
        ]);
    }


    public function update(Request $request, HelpDesk $helpdesk)
    {
        $request->validate([
            'id_project' => 'required',
            'tgl_komplen' => 'required|date',
            'tgl_target_selesai' => 'required|date',
            'komplain' => 'nullable|array',
            'catatan_komplain' => 'nullable|array',
            'penanggung_jawab' => 'required',
            'status_komplen' => 'required|in:open,progress,closed',
        ], [
            'id_project.required' => 'Project wajib dipilih',
            'tgl_komplen.required' => 'Tanggal komplen wajib diisi',
            'tgl_target_selesai.required' => 'Target selesai wajib diisi',
            'komplain.required' => 'Komplain wajib diisi',
            'catatan_komplain.required' => 'Catatan Komplain wajib diisi',
            'penanggung_jawab.required' => 'Penanggung jawab wajib diisi',
            'status_komplen.required' => 'Status wajib dipilih',
        ]);

        $helpdesk->update([
            'tgl_komplen' => $request->tgl_komplen,
            'id_project' => $request->id_project,
            'komplain' => $request->komplain,
            'catatan_komplain' => $request->catatan_komplain,
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


}
