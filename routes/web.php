<?php

use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\ProdukController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KebutuhanController;
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

Route::get('/', function () {
    return view('layouts.master');
});
Route::get('/', [DashboardController::class, 'index']);

//customer//
Route::get('/customers', [CustomersController::class, 'index'])->name('customers.index');

//kebutuhan//
Route::resource('interaksi', KebutuhanController::class);

Route::prefix('produk')->group(function () {
    Route::get('/', [ProdukController::class, 'index'])->name('produk.index');       // Halaman list produk
    Route::post('/list', [ProdukController::class, 'list'])->name('produk.list');    // DataTables JSON
    Route::get('/create', [ProdukController::class, 'create'])->name('produk.create'); // Form tambah
    Route::post('/', [ProdukController::class, 'store'])->name('produk.store');      // Simpan produk
});



























Route::get('/profil', [DashboardController::class, 'index']);
