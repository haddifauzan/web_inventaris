<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Departemen;
use App\Models\IpAddress;
use App\Models\Lokasi;
use App\Models\MenuAktif;
use App\Models\MenuBackup;
use App\Models\MenuPemusnahan;
use App\Models\Riwayat;
use App\Models\TipeBarang;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');
        $category = $request->input('category', 'all');
        
        // If AJAX request, return JSON
        if ($request->ajax()) {
            return $this->getSearchResults($query, $category);
        }
        
        // For regular requests, return view with results
        $results = $this->getSearchResults($query, $category);
        
        $view = auth()->user()->role === 'admin' ? 'admin.search.results' : 'user.search.results';
        
        $breadcrumbs = [
            ['url' => '', 'text' => 'Search Results']
        ];
        
        return view($view, [
            'title' => 'Search Results',
            'results' => $results,
            'query' => $query,
            'category' => $category,
            'breadcrumbs' => $breadcrumbs
        ]);
    }

    private function getSearchResults($query, $category)
    {
        $results = [];
        
        if ($category === 'all' || $category === 'barang') {
            $barang = Barang::where('jenis_barang', 'like', "%{$query}%")
            ->orWhere('model', 'like', "%{$query}%")
            ->orWhere('tipe_merk', 'like', "%{$query}%")
            ->orWhere('serial', 'like', "%{$query}%")
            ->orWhere('operating_system', 'like', "%{$query}%")
            ->orWhere('kelayakan', 'like', "%{$query}%")
            ->orWhere('kepemilikan', 'like', "%{$query}%")
            ->orWhere('status', 'like', "%{$query}%")
            ->get();
            
            foreach ($barang as $item) {
            $route = '';
            $routeParams = ['serial' => $item->serial];

            if ($item->jenis_barang === 'Komputer') {
                $route = 'komputer.index';
                $serialData = json_decode($item->serial, true);
                $routeParams = ['serial' => $serialData['cpu'] ?? ''];
            } elseif ($item->jenis_barang === 'Tablet') {
                $route = 'tablet.index';
            } elseif ($item->jenis_barang === 'Switch') {
                $route = 'switch.index';
            }
            
            $title = $item->jenis_barang === 'Komputer' 
                ? $item->model . ' - ' . implode(', ', array_map(function($key, $value) {
                return ucfirst($key) . ': ' . $value;
                }, array_keys(json_decode($item->serial, true)), json_decode($item->serial, true)))
                : $item->model . ' - ' . $item->serial;

            $results[] = [
                'id' => $item->id_barang,
                'type' => 'Barang - ' . $item->jenis_barang,
                'title' => $title,
                'description' => 'Status: ' . $item->status . ', Tipe: ' . $item->tipe_merk,
                'route' => $route,
                'route_params' => $routeParams,
                'category' => 'barang'
            ];
            }
        }
        
        if ($category === 'all' || $category === 'departemen') {
            $departemen = Departemen::where('nama_departemen', 'like', "%{$query}%")
                ->orWhere('deskripsi', 'like', "%{$query}%")
                ->get();
            
            foreach ($departemen as $item) {
                $results[] = [
                    'id' => $item->id_departemen,
                    'type' => 'Departemen',
                    'title' => $item->nama_departemen,
                    'description' => $item->deskripsi ?? '',
                    'route' => 'departemen.index',
                    'route_params' => ['departemen' => $item->nama_departemen],
                    'category' => 'departemen'
                ];
            }
        }
        
        if ($category === 'all' || $category === 'lokasi') {
            $lokasi = Lokasi::where('nama_lokasi', 'like', "%{$query}%")
                ->orWhere('deskripsi', 'like', "%{$query}%")
                ->get();
            
            foreach ($lokasi as $item) {
                $results[] = [
                    'id' => $item->id_lokasi,
                    'type' => 'Lokasi',
                    'title' => $item->nama_lokasi,
                    'description' => $item->deskripsi ?? '',
                    'route' => 'lokasi.index',
                    'route_params' => ['lokasi' => $item->nama_lokasi],
                    'category' => 'lokasi'
                ];
            }
        }
        
        if ($category === 'all' || $category === 'ip') {
            $ipAddresses = IpAddress::where('ip_address', 'like', "%{$query}%")
                ->orWhere('status', 'like', "%{$query}%")
                ->get();
            
            foreach ($ipAddresses as $item) {
                $description = 'Status: ' . $item->status;
                if ($item->barang) {
                    $description .= ', Barang: ' . $item->barang->model;
                }
                
                $results[] = [
                    'id' => $item->id_ip,
                    'type' => 'IP Address',
                    'title' => $item->ip_address,
                    'description' => $description,
                    'route' => 'ip-address.detail',
                    'route_params' => ['idIpHost' => $item->id_ip_host, 'ipAddress' => $item->ip_address],
                    'category' => 'ip'
                ];
            }
        }
        
        if ($category === 'all' || $category === 'menuaktif') {
            $menuAktif = MenuAktif::where('komputer_name', 'like', "%{$query}%")
            ->orWhere('user', 'like', "%{$query}%")
            ->orWhere('keterangan', 'like', "%{$query}%")
            ->get();
            
            foreach ($menuAktif as $item) {
            $description = 'User: ' . $item->user;
            $route = '';
            
            if ($item->barang) {
                $description .= ', Jenis: ' . $item->barang->jenis_barang;
                
                if ($item->barang->jenis_barang === 'Komputer') {
                $url = 'komputer/aktif';
                } elseif ($item->barang->jenis_barang === 'Tablet') {
                $url = 'tablet/aktif';
                } elseif ($item->barang->jenis_barang === 'Switch') {
                $url = 'switch/aktif';
                }
            }
            
            $results[] = [
                'id' => $item->id_aktif,
                'type' => 'Aktif',
                'title' => $item->barang && $item->barang->jenis_barang === 'Komputer' ? $item->komputer_name : ($item->barang ? $item->barang->tipe_merk : 'No Name'),
                'description' => $description,
                'route' => $url,
                'route_params' => ['id' => $item->id_aktif],
                'category' => 'menuaktif'
            ];
            }
        }
        
        if ($category === 'all' || $category === 'tipebarang') {
            $tipeBarang = TipeBarang::where('jenis_barang', 'like', "%{$query}%")
                ->orWhere('tipe_merk', 'like', "%{$query}%")
                ->get();
            
            foreach ($tipeBarang as $item) {
                $results[] = [
                    'id' => $item->id_tipe_barang,
                    'type' => 'Tipe Barang',
                    'title' => $item->tipe_merk,
                    'description' => 'Jenis: ' . $item->jenis_barang,
                    'route' => 'tipe-barang.index',
                    'route_params' => ['tipeBarang' => $item->tipe_merk],
                    'category' => 'tipebarang'
                ];
            }
        }
        
        
        return $results;
    }
}