<?php

namespace App\Http\Controllers\Auth;

use App\Models\Menu;
use App\Models\User;
use App\Models\HakAkses;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Models\KonfigurasiMedia;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class AuthController extends Controller
{
    public function getLogin(): View
    {
        return view('admin.login');
    }

    public function postLogin(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => 'required|exists:users,username',
            'password' => 'required',
            // 'captcha' => 'required|captcha',
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.exists' => 'Username tidak terdaftar.',
            'password.required' => 'Password wajib diisi.',
            // 'captcha.required' => 'Captcha wajib diisi.',
            // 'captcha.captcha' => 'Captcha salah.',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user) {
            return redirect()->route('login')->withErrors(['username' => 'Username tidak ditemukan.']);
        }

        if ($user->status === 'BLOKIR') {
            return redirect()->route('login')->withErrors(['username' => 'Akun anda telah diblokir.']);
        }

        if (!Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            return redirect()->route('login')->withErrors(['password' => 'Password yang Anda masukkan salah.']);
        }

        $userId = Auth::id();

        $hakAkses = HakAkses::where('id_user', $userId)
            ->where('lihat', 1)
            ->get();

        $allowedMenuIds = $hakAkses->pluck('id_menu')->toArray();

        $getmenus = Menu::where('id_parent', 0)
            ->whereIn('id', $allowedMenuIds)
            ->orderBy('urutan')
            ->with(['children' => function ($query) use ($allowedMenuIds) {
                $query->whereIn('id', $allowedMenuIds);
            }])
            ->get();


        session([
            'getmenus' => $getmenus,
        ]);

        return redirect()->route('dashboard')->withSuccess('Login Berhasil.');
    }


    public function master()
    {

    }

    public function pengaturan()
    {

    }

    public function pengaturanWa()
    {

    }

    public function customer()
    {

    }

    public function Marketing()
    {

    }
    public function keuangan()
    {

    }
    public function transaksi()
    {

    }

    public function logout(): RedirectResponse
    {
        Auth::guard('web')->logout();

        return redirect()->route('login')->with('success', 'Logout Berhasil.');
    }
}
