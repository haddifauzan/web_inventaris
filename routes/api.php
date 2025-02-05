<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/lokasi/{lokasi}/ip-addresses', function ($lokasiId) {
    $ipHosts = \App\Models\IpHost::where('id_lokasi', $lokasiId)
        ->with(['ipAddresses' => function($query) {
            $query->where('status', 'Available')
                  ->orderBy('ip_address');
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
