<?php

use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\ProdukController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomersController;

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
Route::get('/', [WelcomeController::class, 'index']);

Route::get('/customers', [CustomersController::class, 'index'])->name('customers.index');

Route::group(['prefix' => 'produk'], function () {
    Route::get('/', [ProdukController::class, 'index']);          //menampilkan halaman awal Produk
    Route::post('/list', [ProdukController::class, 'list']);      //menampilkan data Produk dalam bentuk json untuk datatables
    Route::get('/create', [ProdukController::class, 'create']);   //menammpilkan halaman form tambah Produk
    Route::post('/', [ProdukController::class, 'store']);         //menyimpan data Produk baru
});
