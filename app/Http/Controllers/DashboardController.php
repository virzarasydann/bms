<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\HelpDesk;
use App\Models\Sewa;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->withError('Silahkan Login terlebih dahulu');
        }

        // Ambil semua project yang belum selesai (independen)
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
    return view('admin.dashboard.detail', [
        'project' => $project,
    ]);
}
}
