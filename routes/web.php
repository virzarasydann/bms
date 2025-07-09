<?php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DonasiBarangController;
use App\Http\Controllers\DonaturController;
use App\Http\Controllers\HakAksesController;
use App\Http\Controllers\KategoriPemasukanController;
use App\Http\Controllers\KategoriPengeluaranController;
use App\Http\Controllers\MustahikController;
use App\Http\Controllers\PenerimaanController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\TutupBukuController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\LaporanKeuanganController;
use App\Http\Controllers\LaporanJurnalController;

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

    Route::controller(DashboardController::class)->group(function () {
        Route::get('/dashboard', 'dashboard')->name('dashboard');
    });

    Route::resource('mustahik', MustahikController::class);
    Route::resource('donatur', DonaturController::class);
    Route::resource('pengguna', PenggunaController::class);
   
   
    Route::controller(LaporanJurnalController::class)->group(function () {
        
    Route::get('admin/laporanJurnal/export-pdf', [LaporanJurnalController::class, 'exportPdf'])->name('laporanJurnal.exportPdf');
    Route::get('admin/laporanJurnal/export-excel', [LaporanJurnalController::class, 'exportExcel'])->name('laporanJurnal.exportExcel');
        
    });
    Route::resource('laporanJurnal', LaporanJurnalController::class);

    Route::controller(LaporanKeuanganController::class)->group(function () {
        Route::get('laporanKeuangan/showJenis', [LaporanKeuanganController::class, 'showJenis'])
    ->name('laporanKeuangan.showJenis');
    Route::get('admin/laporanKeuangan/export-pdf', [LaporanKeuanganController::class, 'exportPdf'])->name('laporanKeuangan.exportPdf');
Route::get('admin/laporanKeuangan/export-excel', [LaporanKeuanganController::class, 'exportExcel'])->name('laporanKeuangan.exportExcel');
        
    });
    Route::resource('laporanKeuangan', LaporanKeuanganController::class);

     Route::controller(DonasiBarangController::class)->group(function () {
        Route::post('penyaluran', 'store')->name('penyaluran.store');
        Route::get('penerimaan/list', 'getPenerima')->name('penerimaan.list');
        Route::get('donasiBarang/{id}/penyaluran', 'penyaluran') ->name('donasiBarang.penyaluran');
         Route::get('donasiBarang/donatur', 'getDonatur')->name('donaturList');
         Route::resource('donasiBarang', DonasiBarangController::class);
    });


    Route::controller(PenerimaanController::class)->group(function () {
        Route::get('penerimaan/cetak/{id}', 'cetakKwitansi')->name('cetak-kwitansi-masuk');
        Route::get('penerimaan/penomoran', 'showNoTransaksi')->name('penerimaan.penomoran');
        Route::get('penerimaan/cetak-penerimaan', 'cetak')->name('cetak-pemasukan');
        Route::get('kategori-penerimaan/list', 'getKategoriPenerimaan')->name('kategori.penerimaan.list');
        Route::get('penerimaan/search',  'getDonatur')->name('penerimaan.search');
        Route::resource('penerimaan', PenerimaanController::class);
    });


    Route::controller(PengeluaranController::class)->group(function () {
        Route::post('/pengeluaran/detail/update', 'updateDetail')->name('pengeluaran.detail.update');
        Route::post('/pengeluaran/sumber/update',  'updateSumber')->name('pengeluaran.sumber.update');
        Route::get('pengeluaran/cetak/{id}', 'cetakKwitansi')->name('cetak-kwitansi');
        Route::get('pengeluaran/penomoran', 'showNoPengeluaran')->name('penomoran');
        Route::get('pengeluaran/cetak-pengeluaran', 'cetak')->name('cetak-pengeluaran');
        Route::get('pengeluaran/list-pemasukan', 'getKategoriPemasukan')->name('kategori-pemasukan.list');
        Route::get('kategori-pengeluaran/list', 'getKategoriPengeluaran')->name('kategori.pengeluaran.list');
        Route::get('pengeluaran/search',  'getMustahik')->name('pengeluaran.search');
        Route::resource('pengeluaran', PengeluaranController::class);
    });

    Route::controller(PengajuanController::class)->group(function () {
        Route::get('survey/penomoran', 'showNoSurvey')->name('survey.penomoran');
        Route::get('pengajuan/penomoran', 'showNoPengajuan')->name('pengajuan.penomoran');
        Route::get('pengajuan/cetak-form/{id}', 'cetak')->name('cetak-survey');
        Route::post('pengajuan/{id}/survey', 'storeSurvey')->name('pengajuan.storeSurvey');
        Route::get('pengajuan/{id}/survey',  'survey')->name('pengajuan.survey');
        Route::resource('pengajuan', PengajuanController::class);
    });

        Route::resource('tutupBuku', TutupBukuController::class);


    Route::resource('kategoriPemasukan', KategoriPemasukanController::class);
    Route::resource('kategoriPengeluaran', KategoriPengeluaranController::class);





    Route::post('admin/logout', [AuthController::class, 'logout'])->name('admin.logout');
    Route::get('admin/hak-akses', [HakAksesController::class, 'hak_akses'])->name('hakakses');
    Route::get('admin/get-hak-akses', [HakAksesController::class, 'getHakAkses'])->name('admin.getHakAkses');
    Route::put('admin/updateHakAkses', [HakAksesController::class, 'updateHakAkses'])->name('admin.updateHakAkses');

 });
