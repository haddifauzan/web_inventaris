<?php
namespace App\Http\Controllers;

use App\Exports\ComputerActiveExport;
use App\Exports\TabletActiveExport;
use App\Exports\SwitchMaintenanceExport;
use App\Models\Barang;
use App\Models\Lokasi;
use App\Models\Departemen;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class LaporanController extends Controller
{
    public function exportReportComputerActive(Request $request)
    {
        $fileName = 'computer_active_report';
        
        if ($request->filled('periode')) {
            $fileName .= '_' . Carbon::parse($request->periode)->format('M_Y');
        }
        if ($request->filled('lokasi')) {
            $lokasi = Lokasi::findOrFail($request->lokasi);
            $fileName .= '_' . Str::slug($lokasi->nama_lokasi);
        }
        if ($request->filled('departemen')) {
            $departemen = Departemen::findOrFail($request->departemen);
            $fileName .= '_' . Str::slug($departemen->nama_departemen);
        }
        
        $fileName .= '.xlsx';

        
        return Excel::download(new ComputerActiveExport(
            $request->periode,
            $request->lokasi,
            $request->departemen,
        ), $fileName);
    }

    public function exportReportTabletActive(Request $request)
    {
        $fileName = 'tablet_active_report';
        
        if ($request->filled('periode')) {
            $fileName .= '_' . Carbon::parse($request->periode)->format('M_Y');
        }
        if ($request->filled('lokasi')) {
            $lokasi = Lokasi::findOrFail($request->lokasi);
            $fileName .= '_' . Str::slug($lokasi->nama_lokasi);
        }
        if ($request->filled('departemen')) {
            $departemen = Departemen::findOrFail($request->departemen);
            $fileName .= '_' . Str::slug($departemen->nama_departemen);
        }
        
        $fileName .= '.xlsx';

        
        return Excel::download(new TabletActiveExport(
            $request->periode,
            $request->lokasi,
            $request->departemen,
        ), $fileName);
    }
    
    public function exportSwitchMaintenance(Request $request)
    {
        $request->validate([
            'tanggal_awal' => 'nullable|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_awal',
            'lokasi' => 'nullable|exists:tbl_lokasi,id_lokasi',
            'departemen' => 'nullable|exists:tbl_departemen,id_departemen',
            'petugas' => 'required|array',
            'petugas.*' => 'required|string',
        ]);

        $tanggalAwal = $request->input('tanggal_awal');
        $tanggalAkhir = $request->input('tanggal_akhir');
        $lokasiId = $request->input('lokasi');
        $departemenId = $request->input('departemen');
        
        // Format petugas array into JSON format
        $petugasList = array_filter($request->input('petugas', [])); // Remove empty values
        $formattedPetugas = json_encode($petugasList);
        
        $fileName = 'maintenance_jaringan_';
        
        if ($tanggalAwal && $tanggalAkhir) {
            $fileName .= Carbon::parse($tanggalAwal)->format('d-m-Y') . '_to_' . Carbon::parse($tanggalAkhir)->format('d-m-Y');
        } else {
            $fileName .= Carbon::now()->format('d-m-Y');
        }
        
        $fileName .= '.xlsx';
        
        return Excel::download(
            new SwitchMaintenanceExport($tanggalAwal, $tanggalAkhir, $lokasiId, $departemenId, $formattedPetugas),
            $fileName
        );
    }

}
