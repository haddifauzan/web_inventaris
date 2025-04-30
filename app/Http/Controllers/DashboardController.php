<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Departemen;
use App\Models\Lokasi;
use App\Models\MenuAktif;
use App\Models\MenuBackup;
use App\Models\MenuPemusnahan;
use App\Models\Maintenance;
use App\Models\IpAddress;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    
    public function index()
    {
        // Counts for main stats
        $totalBarang = Barang::count();
        $barangBaru = Barang::where('status', 'Baru')->count();
        $barangAktif = Barang::where('status', 'Aktif')->count();
        $barangBackup = Barang::where('status', 'Backup')->count();
        $barangPemusnahan = Barang::where('status', 'Pemusnahan')->count();
        $barangBaru = Barang::where('status', 'Baru')->count();
        
        // Counts by equipment type
        $totalKomputer = Barang::where('jenis_barang', 'Komputer')->count();
        $totalTablet = Barang::where('jenis_barang', 'Tablet')->count();
        $totalSwitch = Barang::where('jenis_barang', 'Switch')->count();
        
        // Get latest items
        $barangTerbaru = Barang::orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Low suitability computers
        $komputerKelayakanRendah = Barang::where('jenis_barang', 'Komputer')
            ->where('status', 'Aktif')
            ->where('kelayakan', '<', 60)
            ->with(['menuAktif', 'menuAktif.lokasi'])
            ->orderBy('kelayakan', 'asc')
            ->take(5)
            ->get();
        
        // Latest maintenance activities
        $maintenanceTerbaru = Maintenance::with(['barang'])
            ->orderBy('tgl_maintenance', 'desc')
            ->take(5)
            ->get();
        
        // Computer suitability data (for chart)
        $kelayakanKomputerData = [
            Barang::where('jenis_barang', 'Komputer')->whereBetween('kelayakan', [90, 100])->count(),
            Barang::where('jenis_barang', 'Komputer')->whereBetween('kelayakan', [80, 89])->count(),
            Barang::where('jenis_barang', 'Komputer')->whereBetween('kelayakan', [70, 79])->count(),
            Barang::where('jenis_barang', 'Komputer')->whereBetween('kelayakan', [60, 69])->count(),
            Barang::where('jenis_barang', 'Komputer')->where('kelayakan', '<', 60)->count()
        ];
        
        // Equipment per location data (for chart)
        $lokasiData = DB::table('tbl_menu_aktif')
            ->join('tbl_lokasi', 'tbl_menu_aktif.id_lokasi', '=', 'tbl_lokasi.id_lokasi')
            ->select('tbl_lokasi.nama_lokasi', DB::raw('count(*) as total'))
            ->groupBy('tbl_lokasi.nama_lokasi')
            ->orderBy('total', 'desc')
            ->take(10)
            ->get();
        
        $barangPerLokasiLabels = $lokasiData->pluck('nama_lokasi')->map(function($item) {
            return '"' . $item . '"';
        })->implode(',');
        
        $barangPerLokasiData = $lokasiData->pluck('total')->implode(',');
        
        // Equipment by department data (for chart) - Computers only
        $departemenData = DB::table('tbl_menu_aktif')
            ->join('tbl_departemen', 'tbl_menu_aktif.id_departemen', '=', 'tbl_departemen.id_departemen')
            ->join('tbl_barang', 'tbl_menu_aktif.id_barang', '=', 'tbl_barang.id_barang')
            ->where('tbl_barang.jenis_barang', 'Komputer')
            ->select('tbl_departemen.nama_departemen', DB::raw('count(*) as total'))
            ->groupBy('tbl_departemen.nama_departemen')
            ->orderBy('total', 'desc')
            ->take(5)
            ->get();
        
        $departemenLabels = $departemenData->pluck('nama_departemen')->map(function($item) {
            return '"' . $item . '"';
        })->implode(',');
        
        $departemenValues = $departemenData->pluck('total')->implode(',');
        
        // Equipment acquisition by year (for chart)
        $tahunPerolehanData = DB::table('tbl_barang')
            ->selectRaw('YEAR(tahun_perolehan) as tahun, COUNT(*) as total')
            ->groupBy('tahun')
            ->orderBy('tahun', 'asc')
            ->get();
        
        $acquisitionYearLabels = $tahunPerolehanData->pluck('tahun')->map(function($item) {
            return '"' . $item . '"';
        })->implode(',');
        
        $acquisitionYearValues = $tahunPerolehanData->pluck('total')->implode(',');
        
        // IP Address usage stats
        $ipStats = [
            'total' => IpAddress::count(),
            'used' => IpAddress::where('status', 'In Use')->count(),
            'available' => IpAddress::where('status', 'Available')->count(),
            'blocked' => IpAddress::where('status', 'Blocked')->count(),
        ];
        
        // Recent history data
        $recentRiwayat = DB::table('tbl_riwayat')
            ->join('tbl_barang', 'tbl_riwayat.id_barang', '=', 'tbl_barang.id_barang')
            ->join('tbl_lokasi', 'tbl_riwayat.id_lokasi', '=', 'tbl_lokasi.id_lokasi')
            ->join('tbl_departemen', 'tbl_riwayat.id_departemen', '=', 'tbl_departemen.id_departemen')
            ->select('tbl_riwayat.*', 'tbl_barang.model', 'tbl_barang.tipe_merk', 'tbl_lokasi.nama_lokasi', 'tbl_departemen.nama_departemen')
            ->orderBy('tbl_riwayat.waktu_awal', 'desc')
            ->take(5)
            ->get();

        $title = 'Dashboard';
        $breadcrumbs = [
            ['url' => '#', 'text' => 'Dashboard']
        ];

        $viewData = compact(
            'title', 'breadcrumbs', 'totalBarang', 'barangBaru', 'barangAktif', 'barangBackup', 'barangPemusnahan', 'barangBaru',
            'totalKomputer', 'totalTablet', 'totalSwitch',
            'barangTerbaru', 'komputerKelayakanRendah', 'maintenanceTerbaru',
            'kelayakanKomputerData', 'barangPerLokasiLabels', 'barangPerLokasiData',
            'departemenLabels', 'departemenValues', 'acquisitionYearLabels', 'acquisitionYearValues',
            'ipStats', 'recentRiwayat'

        );

        return view(auth()->user()->role === 'admin' ? 'admin.index' : 'user.index', $viewData);
    }


    public function getBarangTrends()
    {
        // Get monthly equipment trends for the last 12 months
        $monthlyData = [];
        $labels = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthYear = $date->format('M Y');
            $labels[] = $monthYear;
            
            $startDate = $date->startOfMonth()->format('Y-m-d');
            $endDate = $date->endOfMonth()->format('Y-m-d');
            
            $monthlyData[] = [
                'komputer' => Barang::where('jenis_barang', 'Komputer')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count(),
                'tablet' => Barang::where('jenis_barang', 'Tablet')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count(),
                'switch' => Barang::where('jenis_barang', 'Switch')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count(),
            ];
        }
        
        return response()->json([
            'labels' => $labels,
            'data' => $monthlyData
        ]);
    }
    
    public function getMaintenanceStats()
    {
        // Get maintenance stats for switches
        $maintenanceData = Maintenance::selectRaw('DATE_FORMAT(tgl_maintenance, "%b %Y") as month, COUNT(*) as total, SUM(CASE WHEN status_net = "OK" THEN 1 ELSE 0 END) as ok_count, SUM(CASE WHEN status_net != "OK" THEN 1 ELSE 0 END) as issue_count')
            ->groupBy('month')
            ->orderBy(DB::raw('MIN(tgl_maintenance)'))
            ->limit(12)
            ->get();
            
        return response()->json($maintenanceData);
    }

    // Pada controller untuk halaman Tentang Aplikasi
    public function tentangAplikasi()
    {
        $data = [
            'title' => 'Tentang Aplikasi',
            'breadcrumbs' => [
                [
                    'text' => 'Tentang Aplikasi',
                    'url' => route('admin.tentang-aplikasi')
                ]
            ]
        ];

        return view('admin.tentang-aplikasi', $data);
    }
}
