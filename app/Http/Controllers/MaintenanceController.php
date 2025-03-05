<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Maintenance;
use App\Models\MenuAktif;
use Illuminate\Support\Facades\DB;

class MaintenanceController extends Controller
{
    public function index()
    {
        $title = 'Data Maintenance';
        $breadcrumbs = [
            ['url' => route('switch.index', ['tab' => 'aktif']), 'text' => 'Menu Aktif Switch'],
            ['url' => '#', 'text' => 'Data Maintenance']
        ];

        $maintenances = Maintenance::with('barang')
            ->orderByRaw("CASE WHEN status_maintenance = 'Belum' THEN 0 ELSE 1 END")
            ->get();
        return view('admin.switch.maintenance', compact('maintenances', 'breadcrumbs', 'title'));
    }

    public function action(Request $request, $id)
    {
        // Validasi input
        $validatedData = $request->validate([
            'id_barang' => 'required|exists:tbl_barang,id_barang',
            'tgl_maintenance' => 'required|date',
            'node_terpakai' => 'required|numeric|min:0',
            'node_bagus' => 'required|numeric|min:0',
            'node_rusak' => 'required|numeric|min:0',
            'status_net' => 'required|in:OK,Rusak',
            'petugas' => 'nullable|array',
            'keterangan' => 'nullable|string'
        ]);

        // Gabungkan nama petugas menjadi string
        $petugasString = implode(', ', array_filter($request->input('petugas', [])));

        DB::beginTransaction();
        try {
            // Update data maintenance
            $maintenance = Maintenance::findOrFail($id);
            $maintenance->update([
                'id_barang' => $validatedData['id_barang'],
                'status_maintenance' => 'Sudah',
                'tgl_maintenance' => $validatedData['tgl_maintenance'],
                'node_terpakai' => $validatedData['node_terpakai'],
                'node_bagus' => $validatedData['node_bagus'],
                'node_rusak' => $validatedData['node_rusak'],
                'status_net' => $validatedData['status_net'],
                'petugas' => $petugasString,
                'keterangan' => $request->input('keterangan')
            ]);

            // Update MenuAktif dengan data maintenance terbaru
            $menuAktif = MenuAktif::where('id_barang', $validatedData['id_barang'])->first();
            if ($menuAktif) {
                $menuAktif->update([
                    'node_terpakai' => $validatedData['node_terpakai'],
                    'node_bagus' => $validatedData['node_bagus'],
                    'node_rusak' => $validatedData['node_rusak']
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Maintenance berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mengupdate maintenance: ' . $e->getMessage());
        }
    }
}
