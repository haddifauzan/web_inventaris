<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\EnsureAdmin;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\DepartemenController;
use App\Http\Controllers\IpAddressController;
use App\Http\Controllers\KomputerController;
use App\Http\Controllers\LaporanController;


Route::get('/', [AuthController::class, 'showLoginForm']);
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login-submit', [AuthController::class, 'login'])->name('login.submit');
Route::post('/reset-credential', [AuthController::class, 'resetCredential'])->name('resetCredential');

Route::prefix('admin')->middleware(['auth', EnsureAdmin::class])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');

    // DATA MASTER
    Route::resource('lokasi', LokasiController::class);
    Route::resource('departemen', DepartemenController::class);
    Route::get('ip-address', [IpAddressController::class, 'index'])->name('ip-address.index');
    Route::get('ip-address/create', [IpAddressController::class, 'create'])->name('ip-address.create');
    Route::post('ip-address/store', [IpAddressController::class, 'store'])->name('ip-address.store');
    Route::post('/ip-address/store-single', [IpAddressController::class, 'storeSingle'])->name('ip-address.store-single');
    Route::put('ip-address/update-range', [IpAddressController::class, 'updateRange'])->name('ip-address.update-range');
    Route::delete('ip-address/destroy-range', [IpAddressController::class, 'destroyRange'])->name('ip-address.destroy-range');
    Route::put('ip-address/update-status/{id}', [IpAddressController::class, 'updateIpStatus'])->name('ip-address.update-status');
    Route::put('/ip-address/update-host/{id}', [IpAddressController::class, 'updateHost'])->name('ip-address.update-host');
    Route::get('ip-address/{idIpHost}/detail', [IpAddressController::class, 'showDetail'])->name('ip-address.detail');

    // DATA BARANG

    // KOMPUTER
    Route::get('/komputer/{tab?}', [KomputerController::class, 'index'])->name('komputer.index');
    Route::get('/komputer/barang/create', [KomputerController::class, 'create'])->name('komputer.create');
    Route::post('/komputer/barang/store', [KomputerController::class, 'store'])->name('komputer.store');
    Route::get('/komputer/{id}/edit', [KomputerController::class, 'edit'])->name('komputer.edit');
    Route::put('/komputer/{id}/update', [KomputerController::class, 'update'])->name('komputer.update');
    Route::delete('/komputer/{id}/destroy', [KomputerController::class, 'destroy'])->name('komputer.destroy');
    Route::post('/komputer/{id}/aktivasi', [KomputerController::class, 'aktivasi'])->name('komputer.aktivasi');
    Route::put('/komputer/{id}/musnah', [KomputerController::class, 'backupToMusnah'])->name('komputer.musnah');
    Route::put('/komputer/{id}/tobackup', [KomputerController::class, 'aktifToBackup'])->name('komputer.tobackup');
    Route::put('/komputer/{id}/topemusnahan', [KomputerController::class, 'aktifToMusnah'])->name('komputer.topemusnahan');
    
    Route::get('/laporan/export-computer-active', [LaporanController::class, 'exportReportComputerActive'])
    ->name('laporan.export-computer-active');
    Route::put('/update-credentials/{id}', [AuthController::class, 'updateCredentials'])->name('updateCredential');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
