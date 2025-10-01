<?php

use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\TambahKebutuhanController;
use App\Http\Controllers\PICController;
use App\Http\Controllers\DataInvoiceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RekapController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\KebutuhanController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Produk\AskController;
use App\Http\Controllers\Produk\HoldController;
use App\Http\Controllers\Produk\ClosingController;
use App\Http\Controllers\PasangController;
use Dflydev\DotAccessData\Data;
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

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'postLogin']);
Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/ghost', [DashboardController::class, 'ghost'])->name('dashboard.ghost');
    Route::get('/dashboard/ask', [DashboardController::class, 'ask'])->name('dashboard.ask');
    Route::get('/dashboard/followup', [DashboardController::class, 'followup'])->name('dashboard.followup');
    Route::get('/dashboard/hold', [DashboardController::class, 'hold'])->name('dashboard.hold');
    Route::get('/dashboard/closing', [DashboardController::class, 'closing'])->name('dashboard.closing');

    //broadcast//
    Route::get('/ask/broadcast', [AskController::class, 'broadcast'])->name('ask.broadcast');
    Route::post('/ask/send-broadcast', [AskController::class, 'sendBroadcast'])->name('ask.sendBroadcast');
    Route::get('/broadcast/followup', [DashboardController::class, 'broadcast'])->name('broadcast.followup');
    Route::post('/broadcast/send-followup', [DashboardController::class, 'sendBroadcast'])->name('broadcast.sendFollowup');
    Route::get('/broadcast/hold', [HoldController::class, 'broadcast'])->name('broadcast.hold');
    Route::post('/broadcast/hold/send', [HoldController::class, 'sendBroadcast'])->name('broadcast.sendHold');
    Route::get('/broadcast/closing', [ClosingController::class, 'broadcast'])->name('broadcast.closing');
    Route::post('/broadcast/closing/send', [ClosingController::class, 'sendBroadcast'])->name('broadcast.sendClosing');

    //customer//
    Route::post('/customers/list', [CustomersController::class, 'data'])->name('customers.data');
    Route::get('/customers', [CustomersController::class, 'index'])->name('customers.index');
    Route::get('/customers/{id}/show_ajax', [CustomersController::class, 'show_ajax']);
    Route::get('/customers/{id}/edit_ajax', [CustomersController::class, 'edit'])->name('customers.edit_ajax');
    Route::put('/customers/{id}/update', [CustomersController::class, 'update'])->name('customers.update');
    //kebutuhan//
    // routes/web.php
    Route::get('/kebutuhan', [KebutuhanController::class, 'index'])->name('kebutuhan.index'); // daftar semua customer
    Route::get('/kebutuhan/create', [KebutuhanController::class, 'create'])->name('kebutuhan.create');
    Route::post('/kebutuhan', [KebutuhanController::class, 'store'])->name('kebutuhan.store');

    Route::get('/kebutuhan/search-customer', [KebutuhanController::class, 'searchCustomer'])->name('kebutuhan.searchCustomer');
    Route::get('/kebutuhan/get-customer/{id}', [KebutuhanController::class, 'getCustomer'])->name('kebutuhan.getCustomer');

    Route::prefix('tambahkebutuhan')->name('tambahkebutuhan.')->group(function () {
        Route::get('/', [TambahKebutuhanController::class, 'index'])->name('index');
        Route::get('/create/{customer_id}', [TambahKebutuhanController::class, 'create'])->name('create');
        Route::post('/store', [TambahKebutuhanController::class, 'store'])->name('store');
        Route::get('/edit/{interaksi_id}', [TambahKebutuhanController::class, 'edit'])->name('edit');
        Route::post('/update/{interaksi_id}', [TambahKebutuhanController::class, 'update'])->name('update');
    });
    // kalau mau detail berdasarkan customer_id, kasih nama route beda
    Route::get('/kebutuhan/customer/{customer_id}', [KebutuhanController::class, 'showByCustomer'])->name('kebutuhan.byCustomer');
    Route::get('/rekap/{interaksi_id}/realtime', [KebutuhanController::class, 'index'])->name('rekap.realtime');
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
        Route::get('{id}/edit_ajax', [ProdukController::class, 'edit_ajax']);
        Route::post('{id}/update_ajax', [ProdukController::class, 'update_ajax']);
    });

    Route::prefix('rekap')->group(function () {
        Route::get('/', [RekapController::class, 'index'])->name('rekap.index');       // Halaman list monthrekap
        Route::post('/list', [RekapController::class, 'list'])->name('rekap.list');
        Route::get('/{interaksi_id}/show_ajax', [RekapController::class, 'show_ajax'])->name('rekap.show_ajax');    // Route::get('/search-product', [RekapController::class, 'searchProduct'])->name('rekap.searchProduct');
        // web.php
        Route::post('/rekap/update-followup', [RekapController::class, 'updateFollowUp'])->name('rekap.updateFollowUp');
        Route::get('realtime/list/{interaksi}', [RekapController::class, 'getRealtimeList']);
        Route::get('/rekap/realtime/{interaksi_id}', [RekapController::class, 'indexRealtime'])->name('rekap.indexRealtime');
        Route::get('/rekap/{interaksi_id}/kebutuhan', [RekapController::class, 'showKebutuhanProduk'])->name('rekap.showKebutuhanProduk');
        Route::get('/rekap/{interaksi_id}/identifikasi-awal/', [RekapController::class, 'showIdentifikasiAwal'])->name('rekap.showIdentifikasiAwal');
        Route::delete('/rekap/identifikasi-awal/{awal_id}/delete', [RekapController::class, 'deleteIdentifikasiAwal'])->name('rekap.deleteIdentifikasiAwal');
        Route::get('/rekap/identifikasi-awal/create', [RekapController::class, 'createIdentifikasiAwal'])->name('rekap.createIdentifikasiAwal');
        Route::post('/rekap/identifikasi-awal/store', [RekapController::class, 'storeIdentifikasiAwal'])->name('interaksiAwal.store');
        Route::get('/rekap/identifikasi-awal/list/{interaksi_id}', [RekapController::class, 'listIdentifikasiAwal'])->name('interaksiAwal.list');
        // Route::post('/realtime/store', [RekapController::class, 'storeRealtime'])->name('rekap.storeRealtime');
        // Route::get('/realtime/list/{id}', [RekapController::class, 'listRealtime'])->name('rekap.listRealtime');
        // Route::delete('/realtime/delete/{id}', [RekapController::class, 'deleteRealtime'])->name('rekap.deleteRealtime');
    });
    // TARUH DI LUAR Route::prefix('rekap')
    Route::post('/rekap/update-status/{interaksi_id}', [RekapController::class, 'updateStatus'])->name('rekap.updateStatus');

    Route::prefix('ask')->group(function () {
        Route::get('/', [AskController::class, 'index'])->name('ask.index');       // Halaman list monthSurvey
        Route::post('/list', [AskController::class, 'list'])->name('ask.list');
        Route::get('/{id}/show_ajax', [AskController::class, 'show_ajax'])->name('ask.show_ajax');
    });
    Route::prefix('hold')->group(function () {
        Route::get('/', [HoldController::class, 'index'])->name('hold.index');       // Halaman list monthSurvey
        Route::post('/list', [HoldController::class, 'list'])->name('hold.list');
        Route::get('/{id}/show_ajax', [HoldController::class, 'show_ajax'])->name('hold.show_ajax');
    });
    Route::prefix('closing')->group(function () {
        Route::get('/', [ClosingController::class, 'index'])->name('closing.index');       // Halaman list monthSurvey
        Route::post('/list', [ClosingController::class, 'list'])->name('closing.list');
        Route::get('/{id}/show_ajax', [ClosingController::class, 'show_ajax'])->name('closing.show_ajax');
    });
    Route::group(['prefix' => 'user'], function () {
        Route::get('/', [UserController::class, 'index'])->name('user.index');
        Route::post('/list', [UserController::class, 'list']);      //menampilkan data user dalam bentuk json untuk datatables
        Route::get('/create_ajax', [UserController::class, 'create_ajax']);  //menampilkan halaman form tambah user Ajax
        Route::post('/ajax', [UserController::class, 'store_ajax']);         //menyimpan data user baru Ajax
        Route::get('/{id}/edit_ajax', [UserController::class, 'edit_ajax']);  //menampilkan halaman form edit user Ajax
        Route::put('/{id}/update_ajax', [UserController::class, 'update_ajax']);  //Menyimpan halaman form edit user Ajax
        Route::get('/{id}/delete_ajax', [UserController::class, 'confirm_ajax']);  //tampilan form confirm delete user Ajax
        Route::delete('/{id}/delete_ajax', [UserController::class, 'delete_ajax']); //menghapus data user Ajax
    });

    Route::get('/datainvoice', [DataInvoiceController::class, 'index'])->name('datainvoice.index');
    Route::get('datainvoice/{id}', [DataInvoiceController::class, 'show'])->name('datainvoice.show');


    Route::prefix('realtime')->group(function () {
        Route::get('/create/{id_interaksi}', [RekapController::class, 'createRealtime'])->name('realtime.create');
        Route::post('/store', [RekapController::class, 'storeRealtime'])->name('realtime.store');
    });
    Route::prefix('rincian')->group(function () {
        Route::get('/create/{id_interaksi}', [RekapController::class, 'createRincian'])->name('rincian.create');
        Route::post('/store', [RekapController::class, 'storeRincian'])->name('rincian.store');
        Route::get('/{id}/edit', [RekapController::class, 'editRincian'])->name('rincian.edit');
        Route::put('/{id}/update', [RekapController::class, 'updateRincian'])->name('rincian.update');
    });
    Route::prefix('survey')->group(function () {
        Route::get('/{id}/create', [RekapController::class, 'createSurvey'])->name('survey.create');
        Route::post('/store', [RekapController::class, 'storeSurvey'])->name('survey.store');
    });
    Route::prefix('pasang')->group(function () {
        Route::get('/{id}/create', [RekapController::class, 'createPasang'])->name('pasang.create');
        Route::post('/store', [RekapController::class, 'storePasang'])->name('pasang.store');
        Route::get('/{id}/edit', [RekapController::class, 'editPasang'])->name('pasang.edit');
        Route::put('/{id}/update', [RekapController::class, 'updatePasang'])->name('pasang.update');
    });
    Route::prefix('invoice')->group(function () {
        Route::get('/{id}/create', [RekapController::class, 'createInvoice'])->name('invoice.create');
        Route::post('/store', [RekapController::class, 'storeInvoice'])->name('invoice.store');
        Route::get('/{id}/edit', [RekapController::class, 'editInvoice'])->name('invoice.edit');
        Route::put('/{id}/update', [RekapController::class, 'updateInvoice'])->name('invoice.update');
        Route::get('invoice/{id}/export_pdf', [RekapController::class, 'export_pdf'])
            ->name('invoice.export_pdf');
    });
    Route::get('/profil', [ProfilController::class, 'index']);
    Route::post('/profil/update', [ProfilController::class, 'update']);
    Route::post('/profil/update_data_diri', [ProfilController::class, 'update_data_diri']);
    Route::post('/profil/update_password', [ProfilController::class, 'updatePassword']);
});
