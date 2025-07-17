<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\HelpDesk;
use Carbon\Carbon;

class DashboardController extends Controller
{
        public function dashboard(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->withError('Silahkan Login terlebih dahulu');
        }

        
        $projectBelumSelesai = Project::where(function ($query) {
            $query->whereNull('tanggal_selesai')
                ->orWhere('tanggal_selesai', '>', Carbon::today());
        })
        ->whereHas('helpDesk', function ($query) {
            $query->whereIn('status_komplen', ['open', 'progress']);
        })
        ->with(['helpDesk' => function ($query) {
            $query->whereIn('status_komplen', ['open', 'progress']);
        }])
        ->get();

        // dd($projectBelumSelesai);

        return view('admin.dashboard.dashboard', [
            'projectBelumSelesai' => $projectBelumSelesai,
        ]);
    }
}

