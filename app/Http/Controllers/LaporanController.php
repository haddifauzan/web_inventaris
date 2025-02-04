<?php
namespace App\Http\Controllers;

use App\Exports\ComputerActiveExport;
use App\Models\Barang;
use App\Models\Lokasi;
use App\Models\Departemen;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Carbon\Carbon;

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
}
