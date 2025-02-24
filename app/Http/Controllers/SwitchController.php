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

class SwitchController extends Controller
{
    public function index(Request $request, $tab = 'barang')
    {
        $title = 'Management Switch';
        $breadcrumbs = [['url' => route('switch.index'), 'text' => 'Switch']];
        $lokasi_id = $request->input('lokasi_id'); // Ambil lokasi dari request

        switch ($tab) {
            case 'barang':
                $query = Barang::where('jenis_barang', 'Switch')->latest('created_at');
                break;
            case 'backup':
                $query = Barang::where('jenis_barang', 'Switch')->where('status', 'Backup')->latest('created_at');
                break;
            case 'aktif':
                $query = Barang::where('jenis_barang', 'Switch')
                    ->where('status', 'Aktif')
                    ->with(['menuAktif.departemen', 'menuAktif.lokasi', 'ipAddress']);
                
                if ($lokasi_id) {
                    $query->whereHas('menuAktif', function ($q) use ($lokasi_id) {
                        $q->where('id_lokasi', $lokasi_id);
                    });
                }
                
                $query->join('tbl_menu_aktif', 'tbl_barang.id_barang', '=', 'tbl_menu_aktif.id_barang')
                    ->join('tbl_departemen', 'tbl_menu_aktif.id_departemen', '=', 'tbl_departemen.id_departemen')
                    ->join('tbl_lokasi', 'tbl_menu_aktif.id_lokasi', '=', 'tbl_lokasi.id_lokasi')
                    ->orderBy('tbl_lokasi.nama_lokasi', 'asc')
                    ->orderBy('tbl_departemen.nama_departemen', 'asc');
                break;
            case 'pemusnahan':
                $query = Barang::where('jenis_barang', 'Switch')->where('status', 'Pemusnahan')->latest('created_at');
                break;
            case 'riwayat':
                $query = Barang::where('jenis_barang', 'Switch')
                    ->whereHas('riwayat')
                    ->with([
                        'riwayat' => function ($q) {
                            $q->orderBy('waktu_awal', 'desc');
                        },
                        'riwayat.lokasi',
                        'riwayat.departemen'
                    ])
                    ->orderBy('updated_at', 'desc');
                break;
            default:
                return redirect()->route('switch.index', ['tab' => 'barang']);
        }

        $data = $query->get();
        $lokasi = Lokasi::orderBy('nama_lokasi', 'asc')->get();
        $departemen = Departemen::orderBy('nama_departemen', 'asc')->get();
        $ipAddresses = IpAddress::where('status', 'Available')->get();

        $viewPath = auth()->user()->role === 'admin' ? 'admin.switch.index' : 'user.switch.index';
        return view($viewPath, compact(
            'title', 'breadcrumbs', 'tab', 'data', 'lokasi', 'departemen', 'ipAddresses', 'lokasi_id'
        ));
    }
}
