<?php

use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\ProdukController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RekapController;
use App\Http\Controllers\KebutuhanController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\PasangController;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

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

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

//customer//
Route::post('/customers/list', [CustomersController::class, 'data'])->name('customers.data');
Route::get('/customers', [CustomersController::class, 'index'])->name('customers.index');
Route::get('/customers/{id}/show_ajax', [CustomersController::class, 'show_ajax']);
Route::get('/customers/{id}/edit', [CustomersController::class, 'edit'])->name('customers.edit');
Route::put('/customers/{id}/update', [CustomersController::class, 'update'])->name('customers.update');
//kebutuhan//
Route::get('/kebutuhan', [KebutuhanController::class, 'index'])->name('kebutuhan.index');
Route::get('/kebutuhan/create', [KebutuhanController::class, 'create'])->name('kebutuhan.create');
Route::post('/kebutuhan', [KebutuhanController::class, 'store'])->name('kebutuhan.store');
Route::get('/kebutuhan/search-customer', [KebutuhanController::class, 'searchCustomer'])->name('kebutuhan.searchCustomer');
Route::get('/kebutuhan/get-customer/{id}', [KebutuhanController::class, 'getCustomer'])->name('kebutuhan.getCustomer');

Route::prefix('produk')->group(function () {
    Route::get('/', [ProdukController::class, 'index'])->name('produk.index');       // Halaman list produk
    Route::post('/list', [ProdukController::class, 'list'])->name('produk.list');    // DataTables JSON
    Route::get('/create', [ProdukController::class, 'create'])->name('produk.create'); // Form tambah
    Route::post('/', [ProdukController::class, 'store'])->name('produk.store');      // Simpan produk
    Route::get('/create_ajax', [ProdukController::class, 'create_ajax']);  //menampilkan halaman form tambah Barang Ajax
    Route::post('/ajax', [ProdukController::class, 'store_ajax']);         //menyimpan data Produk baru Ajax
    Route::get('/{id}/delete_ajax', [ProdukController::class, 'confirm_ajax']);  //tampilan form confirm delete Barang Ajax
    Route::delete('/{id}/delete_ajax', [ProdukController::class, 'delete_ajax']); //menghapus data Barang Ajax
    Route::get('/{id}/show_ajax', [ProdukController::class, 'show_ajax']);
});

Route::prefix('rekap')->group(function () {
    Route::get('/', [RekapController::class, 'index'])->name('rekap.index');       // Halaman list monthrekap
    Route::post('/list', [RekapController::class, 'list'])->name('rekap.list');
    Route::get('/{id}/show_ajax', [RekapController::class, 'show_ajax']);
    Route::post('/rekap/update-followup', [RekapController::class, 'updateFollowUp'])->name('rekap.updateFollowUp');
});
Route::prefix('survey')->group(function () {
    Route::get('/', [SurveyController::class, 'index'])->name('survey.index');       // Halaman list monthSurvey
    Route::post('/list', [SurveyController::class, 'list'])->name('survey.list');
    Route::get('/{id}/show_ajax', [SurveyController::class, 'show_ajax']);
});
Route::prefix('pasang')->group(function () {
    Route::get('/', [PasangController::class, 'index'])->name('pasang.index');       // Halaman list monthPasang
    Route::post('/list', [PasangController::class, 'list'])->name('pasang.list');
    Route::get('/{id}/show_ajax', [PasangController::class, 'show_ajax']);
});







Route::get('/profil', [DashboardController::class, 'index']);
