<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TipeBarangController;
use App\Http\Controllers\DepartemenController;
use App\Http\Controllers\IpAddressController;

Route::get('/lokasi/{lokasi}/ip-addresses', [IpAddressController::class, 'getAvailableIpAddresses']);
Route::get('/tipe-barang/{id}', [TipeBarangController::class, 'getSpesifikasi']);
