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
use App\Models\Maintenance;
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
            case 'baru':
                $query = Barang::where('jenis_barang', 'Switch')
                    ->where('status', 'Baru')
                    ->latest('created_at')
                    ->leftJoin('tbl_maintenance', 'tbl_barang.id_barang', '=', 'tbl_maintenance.id_barang')
                    ->select('tbl_barang.*', 
                        'tbl_maintenance.node_terpakai',
                        'tbl_maintenance.node_bagus',
                        'tbl_maintenance.node_rusak'
                    );
                break;
            case 'backup':
                $query = Barang::where('jenis_barang', 'Switch')
                    ->where('status', 'Backup')
                    ->latest('created_at')
                    ->leftJoin('tbl_maintenance', 'tbl_barang.id_barang', '=', 'tbl_maintenance.id_barang')
                    ->select('tbl_barang.*', 
                        'tbl_maintenance.node_terpakai',
                        'tbl_maintenance.node_bagus',
                        'tbl_maintenance.node_rusak'
                    );
                break;
            case 'aktif':
                $query = Barang::where('jenis_barang', 'Switch')
                    ->where('status', 'Aktif')
                    ->with(['menuAktif.departemen', 'menuAktif.lokasi', 'ipAddress', 'maintenance']); // Tambahkan relasi maintenance
            
                // Filter Lokasi
                $lokasi_id = $request->input('lokasi_id');
                if ($lokasi_id) {
                    $query->whereHas('menuAktif', function ($q) use ($lokasi_id) {
                        $q->where('id_lokasi', $lokasi_id);
                    });
                }
            
                // Filter Departemen
                $departemen_id = $request->input('departemen_id'); 
                if ($departemen_id) {
                    $query->whereHas('menuAktif', function ($q) use ($departemen_id) {
                        $q->where('id_departemen', $departemen_id);
                    });
                }
            
                // Filter Tipe/Merk 
                $tipe_merk = $request->input('tipe_merk');
                if ($tipe_merk) {
                    $query->where('tipe_merk', $tipe_merk);
                }
            
                // Filter Tahun Perolehan
                $tahun_perolehan = $request->input('tahun_perolehan');
                if ($tahun_perolehan) {
                    $query->whereYear('tahun_perolehan', $tahun_perolehan);
                }
            
                // Join dan Order By
                $query->join('tbl_menu_aktif', 'tbl_barang.id_barang', '=', 'tbl_menu_aktif.id_barang')
                    ->join('tbl_departemen', 'tbl_menu_aktif.id_departemen', '=', 'tbl_departemen.id_departemen')
                    ->join('tbl_lokasi', 'tbl_menu_aktif.id_lokasi', '=', 'tbl_lokasi.id_lokasi')
                    ->leftJoin('tbl_maintenance', 'tbl_barang.id_barang', '=', 'tbl_maintenance.id_barang') // Join dengan tabel maintenance
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
        $tipeMerk = TipeBarang::where('jenis_barang', 'Switch')->orderBy('tipe_merk', 'asc')->get();

        $viewPath = auth()->user()->role === 'admin' ? 'admin.switch.index' : 'user.switch.index';
        return view($viewPath, compact(
            'title', 'breadcrumbs', 'tab', 'data', 'lokasi', 'departemen', 'ipAddresses', 'lokasi_id', 'tipeMerk'
        ));
    }

    public function create()
    {
        $title = 'Tambah Switch';
        $breadcrumbs = [
            ['url' => route('switch.index', 'baru'), 'text' => 'Switch'],
            ['url' => '#', 'text' => 'Tambah Switch'],
        ];

        $tipeBarang = TipeBarang::where('jenis_barang', 'Switch')->orderBy('tipe_merk', 'asc')->get();
        return view('admin.switch.create', compact('title', 'breadcrumbs', 'tipeBarang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipe_merk' => 'required',
            'serial' => 'required|unique:tbl_barang,serial',
            'tahun_perolehan' => 'required|date_format:Y-m',
            'spesifikasi_keys' => 'required|array',
            'spesifikasi_values' => 'required|array',
            'status' => 'required|in:Baru,Backup',
        ]);

        // Ubah format tahun_perolehan menjadi YYYY-MM-01
        $tahunPerolehan = $request->tahun_perolehan . '-01';

        DB::beginTransaction();

        $spesifikasi = [];
        $keys = $request->spesifikasi_keys ?? [];
        $values = $request->spesifikasi_values ?? [];
        
        foreach ($keys as $index => $key) {
            if (!empty($key) && isset($values[$index])) {
                $spesifikasi[$key] = $values[$index];
            }
        }

        try {
            $barang = Barang::create([
                'jenis_barang' => 'Switch',
                'model' => 'Switch',
                'tipe_merk' => TipeBarang::findOrFail($request->tipe_merk)->tipe_merk,
                'serial' => $request->serial,
                'spesifikasi' => json_encode($spesifikasi),
                'tahun_perolehan' => $tahunPerolehan, // Simpan dengan format YYYY-MM-01
                'status' => $request->status,
            ]);

            MenuBackup::create([
                'id_barang' => $barang->id_barang,
                'keterangan' => $request->keterangan
            ]);

            DB::commit();
            return redirect()
                ->route('switch.index', ['tab' => 'barang'])
                ->with('success', 'Switch berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $title = 'Edit Switch';
        $breadcrumbs = [
            ['url' => route('switch.index'), 'text' => 'Switch'],
            ['url' => '#', 'text' => 'Edit Switch'],
        ];

        $barang = Barang::where('id_barang', $id)
            ->where('jenis_barang', 'Switch')
            ->firstOrFail();

        $tipeBarang = TipeBarang::where('jenis_barang', 'Switch')->orderBy('tipe_merk', 'asc')->get();

        return view('admin.switch.edit', compact('title', 'breadcrumbs', 'barang', 'tipeBarang'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tipe_merk' => 'required',
            'serial' => 'required|unique:tbl_barang,serial,' . $id . ',id_barang',
            'tahun_perolehan' => 'required|date_format:Y-m',
            'spesifikasi_keys' => 'required|array',
            'spesifikasi_values' => 'required|array',
        ]);

        // Check if barang exists and not in Pemusnahan status
        $barang = Barang::where('id_barang', $id)
            ->where('status', '!=', 'Pemusnahan')
            ->firstOrFail();

        // Format tahun_perolehan menjadi YYYY-MM-01
        $tahunPerolehan = $request->tahun_perolehan . '-01';
        
        $spesifikasi = [];
        $keys = $request->spesifikasi_keys ?? [];
        $values = $request->spesifikasi_values ?? [];
        
        foreach ($keys as $index => $key) {
            if (!empty($key) && isset($values[$index])) {
            $spesifikasi[$key] = $values[$index];
            }
        }

        DB::beginTransaction();
        try {
            $barang->update([
                'model' => 'Switch',
                'tipe_merk' => TipeBarang::findOrFail($request->tipe_merk)->tipe_merk,
                'serial' => $request->serial,
                'spesifikasi' => json_encode($spesifikasi),
                'tahun_perolehan' => $tahunPerolehan,
                'keterangan' => $request->keterangan
            ]);

            DB::commit();
            return redirect()
            ->route('switch.index')
            ->with('success', 'Data switch berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
            ->back()
            ->withInput()
            ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $barang = Barang::findOrFail($id);
            $barang->delete();

            DB::commit();
            return redirect()
                ->route('switch.index')
                ->with('success', 'Data switch berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function aktivasi(Request $request, $id)
    {
        $request->validate([
            'id_lokasi' => 'required|exists:tbl_lokasi,id_lokasi',
            'id_departemen' => 'required|exists:tbl_departemen,id_departemen',
            'node_terpakai' => 'nullable|integer|lte:node_bagus',
            'node_bagus' => 'nullable|integer',
            'node_rusak' => 'nullable|integer',
            'lokasi_switch' => 'required|string',
            'keterangan' => 'nullable',
        ]);

        DB::beginTransaction();
        try {
            $barang = Barang::where('id_barang', $id)
                ->where('jenis_barang', 'Switch')
                ->where('status', 'Backup')
                ->firstOrFail();
            
            $barang->update(['status' => 'Aktif']);
            
            // Cek apakah switch sudah pernah diaktivasi sebelumnya
            $existingActivation = Maintenance::where('id_barang', $id)->exists();

            if (!$existingActivation) {
                // Jika belum pernah diaktivasi, gunakan input dari form
                $node_terpakai = $request->node_terpakai;
                $node_bagus = $request->node_bagus;
                $node_rusak = $request->node_rusak;
            } else {
                // Jika sudah pernah diaktivasi, ambil data dari maintenance terakhir
                $maintenance = Maintenance::whereHas('barang', function ($query) use ($id) {
                    $query->where('id_barang', $id);
                })->latest()->first();
                
                $node_terpakai = $maintenance->node_terpakai ?? 0;
                $node_bagus = $maintenance->node_bagus ?? 0;
                $node_rusak = $maintenance->node_rusak ?? 0;
            }

            // Simpan ke Menu Aktif
            $menuAktif = MenuAktif::create([
                'id_barang' => $id,
                'id_lokasi' => $request->id_lokasi,
                'id_departemen' => $request->id_departemen,
                'node_terpakai' => $node_terpakai,
                'node_bagus' => $node_bagus,
                'node_rusak' => $node_rusak,
                'keterangan' => $request->keterangan
            ]);

            // Jika baru pertama kali diaktivasi, simpan ke tbl_maintenance
            if (!$existingActivation) {
                Maintenance::create([
                    'id_barang' => $id,
                    'status_net' => 'OK',
                    'node_terpakai' => $node_terpakai,
                    'node_bagus' => $node_bagus,
                    'node_rusak' => $node_rusak,
                    'lokasi_switch' => $request->lokasi_switch,
                ]);
            }

            // Simpan ke Riwayat
            Riwayat::create([
                'id_barang' => $barang->id_barang,
                'id_lokasi' => $request->id_lokasi,
                'id_departemen' => $request->id_departemen,
                'user' => $request->user,
                'waktu_awal' => now(),
                'status' => 'Aktif',
                'keterangan' => $request->keterangan
            ]);

            // Hapus dari backup jika ada
            MenuBackup::where('id_barang', $barang->id_barang)->delete();

            DB::commit();
            return redirect()
                ->route('switch.index', ['tab' => 'backup'])
                ->with('success', 'Switch berhasil diaktivasi');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function backupToMusnah(Request $request, $id)
    {
        $request->validate([
            'keterangan' => 'nullable'
        ]);

        DB::beginTransaction();
        try {
            $barang = Barang::where('id_barang', $id)
                ->where('jenis_barang', 'Switch')
                ->where('status', 'Backup')
                ->firstOrFail();
            
            // Check if switch has no history
            if ($barang->riwayat()->exists()) {
                $barang->update([
                    'status' => 'Pemusnahan',
                ]);
                
                MenuPemusnahan::create([
                    'id_barang' => $id,
                    'keterangan' => $request->keterangan
                ]);

                MenuBackup::where('id_barang', $id)->delete();

                DB::commit();
                return redirect()
                    ->route('switch.index', ['tab' => 'backup'])
                    ->with('success', 'Switch berhasil dimusnahkan');
            } else {
                throw new \Exception('Switch tidak dapat dimusnahkan karena belum memiliki riwayat penggunaan');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    public function aktifToBackup(Request $request, $id)
    {
        $request->validate([
            'keterangan' => 'nullable'
        ]);

        DB::beginTransaction();
        try {
            $barang = Barang::where('id_barang', $id)
                ->where('jenis_barang', 'Switch')
                ->where('status', 'Aktif')
                ->firstOrFail();
            
            // Update status barang
            $barang->update([
                'status' => 'Backup',
            ]);

            // Create menu backup entry
            MenuBackup::create([
                'id_barang' => $id,
                'keterangan' => $request->keterangan
            ]);

            // Update riwayat
            Riwayat::where('id_barang', $id)
                ->whereNull('waktu_akhir')
                ->update([
                    'waktu_akhir' => now(),
                    'status' => 'Non-Aktif'
                ]);

            // Delete from menu aktif
            MenuAktif::where('id_barang', $id)->delete();

            DB::commit();
            return redirect()
                ->route('switch.index', ['tab' => 'aktif'])
                ->with('success', 'Switch berhasil dikembalikan ke backup');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function aktifToMusnah(Request $request, $id)
    {
        $request->validate([
            'keterangan' => 'nullable'
        ]);

        DB::beginTransaction();
        try {
            $barang = Barang::where('id_barang', $id)
                ->where('jenis_barang', 'Switch')
                ->where('status', 'Aktif')
                ->firstOrFail();
            
            // Update status barang
            $barang->update([
                'status' => 'Pemusnahan',
            ]);

            // Create menu pemusnahan entry
            MenuPemusnahan::create([
                'id_barang' => $id,
                'keterangan' => $request->keterangan
            ]);

            // Update riwayat
            Riwayat::where('id_barang', $id)
                ->whereNull('waktu_akhir')
                ->update([
                    'waktu_akhir' => now(),
                    'status' => 'Non-Aktif'
                ]);

            // Delete from menu aktif
            MenuAktif::where('id_barang', $id)->delete();

            DB::commit();
            return redirect()
                ->route('switch.index', ['tab' => 'aktif'])
                ->with('success', 'Switch berhasil dipindahkan ke pemusnahan');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function getDestroyedByYear($year)
    {
        $switchs = Barang::where('jenis_barang', 'Switch')
            ->whereHas('menuPemusnahan', function($query) use ($year) {
                $query->whereYear('created_at', $year);
            })
            ->with('menuPemusnahan')
            ->get();

        return response()->json($switchs);
    }

    public function destroyMultiple(Request $request)
    {
        try {
            $switchIds = $request->switchs;
            
            // Validate that all IDs belong to switchs
            $validSwitchs = Barang::where('jenis_barang', 'Switch')
                ->whereIn('id_barang', $switchIds)
                ->count();
                
            if ($validSwitchs !== count($switchIds)) {
                throw new \Exception('Invalid switch IDs detected');
            }
            
            // Begin transaction
            DB::beginTransaction();
            
            // Delete related menuPemusnahan records
            MenuPemusnahan::whereIn('id_barang', $switchIds)->delete();
            
            // Delete switchs
            Barang::whereIn('id_barang', $switchIds)->delete();
            
            // Commit transaction
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus, halaman akan ter refresh.',

            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }
}
