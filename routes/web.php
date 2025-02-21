<?php

use App\Http\Controllers\BkdController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DataFeedController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\PendidikanController;
use App\Http\Controllers\PenelitianController;
use App\Http\Controllers\PengabdianController;
use App\Http\Controllers\PublikasiController;
use App\Http\Controllers\SdmController;
use App\Http\Controllers\UserController;
use App\Http\Livewire\DetailPublikasiSDM;
use App\Models\KategoriKegiatan;
use App\Models\Publikasi;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::redirect('/', 'login');


Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/publikasi', [PublikasiController::class, 'index'])->name('publikasi');
    Route::get('/dashboard/penelitian', [PenelitianController::class, 'index'])->name('penelitian');
    Route::get('/dashboard/pengabdian', [PengabdianController::class, 'index'])->name('pengabdian');
    Route::get('/dashboard/pendidikan', [PendidikanController::class, 'index'])->name('pendidikan');
    // Route::middleware(['checkSession'])->group(function () {
    //     Route::get('/dashboard/publikasi', [PublikasiController::class, 'index'])->name('publikasi');
    //     Route::get('/dashboard/penelitian', [PenelitianController::class, 'index'])->name('penelitian');
    //     Route::get('/dashboard/pengabdian', [PengabdianController::class, 'index'])->name('pengabdian');
    //     Route::get('/dashboard/pendidikan', [PendidikanController::class, 'index'])->name('pendidikan');
    //     // Route::get('/calendar', function () {
    //     //     return view('pages/calendar');
    //     // })->name('calendar');
    // }); 

    Route::get('/dashboard/datadosen', [SdmController::class, 'index'])->name('datadosen');
    Route::get('/dashboard/sdm/{id_sdm}', [PublikasiController::class, 'detailPublikasiSDM']);
    Route::get('/dashboard/penelitian/{id}', [PenelitianController::class, 'detailPenelitian']);
    Route::get('/dashboard/pengabdian/{id}', [PengabdianController::class, 'detailPengabdian']);
    Route::post('/sdm/sync', [SdmController::class, 'getAllDataSDMByNIDN'])->name('sdm.sync');
    Route::get('/publikasi/add', [PublikasiController::class, 'addPublikasi'])->name('pblks.add');
    Route::post('/savePublikasi', [PublikasiController::class, 'savePublikasi'])->name('savePublikasi');
    Route::get('/dashboard/datapegawai/{id_sdm}', [SdmController::class, 'getDetailRekomendasi']);
    // Route::get('/dashboard/datapegawai/{id_sdm}', [SdmController::class, 'getDetailRekomendasiLivewire']);
    Route::get('/dashboard/laporanBkd', [BkdController::class, 'index'])->name('laporanBkd');
    Route::post('/dashboard/laporanBkd', [BkdController::class, 'cekBkd'])->name('cekBKD');
    Route::get('/get-subkategori/{id}', function ($id) {
        $subKategori = KategoriKegiatan::where('parent_id', $id)->get();
        return response()->json($subKategori);
    });
    Route::get('/get-sub2kategori/{id}', function ($id) {
        $sub2Kategori = KategoriKegiatan::where('parent_id', $id)->get();
        return response()->json($sub2Kategori);
    });
    Route::post('/sync-sdm', [SdmController::class, 'syncData']);
    Route::get('/dashboard/user', [UserController::class, 'index'])->name('userBKD');
    Route::post('/user/create', [UserController::class, 'createUserBKDAdmin'])->name('user.create');
});
