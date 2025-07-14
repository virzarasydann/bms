<?php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HakAksesController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\KategoriProjectController;
use App\Http\Controllers\KategoriSewaController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SewaController;
use App\Http\Controllers\HelpDeskController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\KategoriTransaksiController;
use App\Http\Controllers\PemasukanController;
use App\Http\Controllers\PiutangController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\HutangController;
use App\Http\Controllers\MutasiSaldoController;


Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::group(['middleware' => 'guest'], function () {
    Route::get('admin/login', [AuthController::class, 'getLogin'])->name('login');
    Route::post('admin/post-login', [AuthController::class, 'postLogin'])->name('admin.loginPost');
});


Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('admin/master', action: [AuthController::class, 'master'])->name('master');
    Route::get('admin/pengaturan', [AuthController::class, 'pengaturan'])->name('pengaturan');
    Route::resource('pengguna', PenggunaController::class);

    Route::controller(DashboardController::class)->group(function () {
        Route::get('/dashboard', 'dashboard')->name('dashboard');
    });

    Route::resource('customer', CustomerController::class);
    Route::resource('kategoriSewa', KategoriSewaController::class);
    Route::resource('kategoriProject', KategoriProjectController::class);
    Route::resource('project', ProjectController::class);
    Route::resource('sewa', SewaController::class);
    Route::resource('helpdesk', HelpDeskController::class);
    Route::resource('bank', BankController::class);
    Route::resource('kategoriTransaksi', KategoriTransaksiController::class);
    Route::resource('pemasukan', PemasukanController::class);
    Route::resource('piutang', PiutangController::class);
    Route::resource('pengeluaran', PengeluaranController::class);
    Route::resource('hutang', HutangController::class);
    Route::resource('mutasi', MutasiSaldoController::class);


    Route::post('admin/logout', [AuthController::class, 'logout'])->name('admin.logout');
    Route::get('admin/hak-akses', [HakAksesController::class, 'hak_akses'])->name('hakakses');
    Route::get('admin/get-hak-akses', [HakAksesController::class, 'getHakAkses'])->name('admin.getHakAkses');
    Route::put('admin/updateHakAkses', [HakAksesController::class, 'updateHakAkses'])->name('admin.updateHakAkses');

 });
