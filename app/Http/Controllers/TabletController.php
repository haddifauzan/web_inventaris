<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\MenuAktif;
use App\Models\MenuBackup;
use App\Models\MenuPemusnahan;
use App\Models\Riwayat;
use App\Models\IpAddress;
use App\Models\Departemen;
use App\Models\Lokasi;
use App\Models\TipeBarang;
use Illuminate\Support\Facades\DB;

class TabletController extends Controller
{
    public function index($tab = 'barang') // 'backup' sebagai default tab
    {
        $title = 'Management Tablet';
        $breadcrumbs = [
            ['url' => route('tablet.index'), 'text' => 'Tablet'],
        ];
    
        // Ambil data sesuai tab yang dipilih
        switch ($tab) {
            case 'barang':
                $data = Barang::where('jenis_barang', 'Tablet')
                    ->get();
                break;
            case 'backup':
                $data = Barang::where('jenis_barang', 'Tablet')
                    ->where('status', 'Backup')
                    ->get();
                break;
            case 'aktif':
                $data = Barang::where('jenis_barang', 'Tablet')
                    ->where('status', 'Aktif')
                    ->with(['menuAktif', 'ipAddress'])
                    ->get();
                break;
            case 'pemusnahan':
                $data = Barang::where('jenis_barang', 'Tablet')
                    ->where('status', 'Pemusnahan')
                    ->get();
                break;
            case 'riwayat':
                $data = Barang::where('jenis_barang', 'Tablet')
                    ->whereHas('riwayat')
                    ->with(['riwayat' => function($query) {
                        $query->latest('waktu_awal');
                    }, 'riwayat.lokasi', 'riwayat.departemen'])
                    ->get();
                break;
            default:
                return redirect()->route('tablet.index', 'barang'); 
        }
    
        $lokasi = Lokasi::all();
        $departemen = Departemen::all();
        $ipAddresses = IpAddress::where('status', 'Available')->get();

        return view('admin.tablet.index', compact(
            'title', 
            'breadcrumbs', 
            'tab', 
            'data',
            'lokasi',
            'departemen',
            'ipAddresses'
        ));
    }
}
