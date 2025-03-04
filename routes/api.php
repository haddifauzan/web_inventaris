<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TipeBarangController;
use App\Http\Controllers\IpAddressController;

Route::get('/lokasi/{lokasi}/ip-addresses', [IpAddressController::class, 'getAvailableIpAddresses']);
Route::get('/tipe-barang/komputer/{id}', [TipeBarangController::class, 'getSpesifikasiKomputer']);
Route::get('/tipe-barang/tablet/{id}', [TipeBarangController::class, 'getSpesifikasiTablet']);
Route::get('/tipe-barang/switch/{id}', [TipeBarangController::class, 'getSpesifikasiSwitch']);
