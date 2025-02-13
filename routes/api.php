<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TipeBarangController;
use App\Http\Controllers\DepartemenController;

Route::get('/lokasi/{lokasi}/ip-addresses', function ($lokasiId) {
    $ipHosts = \App\Models\IpHost::where('id_lokasi', $lokasiId)
        ->with(['ipAddresses' => function($query) {
            $query->where('status', 'Available')
                  ->orderByRaw('CAST(SUBSTRING_INDEX(ip_address, ".", -1) AS UNSIGNED)');
        }])
        ->get();
    
    return response()->json([
        'ipHosts' => $ipHosts->map(function($ipHost) {
            return [
                'ip_host' => $ipHost->ip_host,
                'ip_addresses' => $ipHost->ipAddresses
            ];
        })
    ]);
});

Route::get('/tipe-barang/{id}', [TipeBarangController::class, 'getSpesifikasi']);
Route::get('/lokasi/{id}/departments', [DepartemenController::class, 'getDepartmentsByLocation']);
