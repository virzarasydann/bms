<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\HelpDesk;
use App\Models\Sewa;
use Carbon\Carbon;
\Carbon\Carbon::setLocale('id');
use App\Models\Pembayaran;
use App\Models\Progres;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->withError('Silahkan Login terlebih dahulu');
        }

        $projects = Project::where(function ($query) {
                $query->whereNull('tanggal_selesai')
                      ->orWhere('tanggal_selesai', '>', Carbon::today());
            })
            ->get();


        $helpdesks = HelpDesk::with('project')
        ->whereIn('status_komplen', ['open', 'progress'])
        ->get();

        $sewaExpiring = Sewa::with('kategori')
        ->expiringSoon()
        ->get();

        return view('admin.dashboard.dashboard', [
            'projects' => $projects,
            'helpdesks' => $helpdesks,
            'sewaExpiring' => $sewaExpiring,
        ]);
    }

    public function detail(Project $project)
    {
        $projectsList = Project::orderBy('nama_project')->get();

        return view('admin.dashboard.detail', [
            'project' => $project,
            'projectsList' => $projectsList,
        ]);
    }

  public function storePembayaran(Request $request)
    {
        $request->validate([
            'tgl_pembayaran' => 'required|date',
            'id_project' => 'required',
            'nominal' => 'required|array',
            'nominal.*' => 'required|string',
            'catatan' => 'nullable|string',
        ]);

        $cleanedNominal = array_map(function ($value) {
            return (int) str_replace(['Rp', '.', ',', ' '], '', $value);
        }, $request->nominal);

        Pembayaran::create([
            'tgl_pembayaran' => $request->tgl_pembayaran,
            'id_project' => $request->id_project,
            'nominal' => $cleanedNominal,
            'catatan' => $request->catatan,
        ]);

        return redirect()->back()->with('success', 'Data pembayaran berhasil disimpan!');
    }

  public function storeProgres(Request $request)
    {
        $request->validate([
            'tgl_progres' => 'required|date',
            'stt_progres' => 'required',
            'project_id' => 'required',
            'catatan' => 'nullable|string',
        ]);

        Progres::create([
            'tgl_progres' => $request->tgl_progres,
            'stt_progres' => $request->stt_progres,
            'project_id' => $request->project_id,
            'catatan' => $request->catatan,
        ]);

        return redirect()->back()->with('success', 'Data pembayaran berhasil disimpan!');
    }


}

