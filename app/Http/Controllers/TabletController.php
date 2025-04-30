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
    public function index(Request $request, $tab = 'barang')
    {
        $title = 'Management Tablet';
        $breadcrumbs = [['url' => route('tablet.index'), 'text' => 'Tablet']];
        $lokasi_id = $request->input('lokasi_id'); // Ambil lokasi dari request

        switch ($tab) {
            case 'barang':
                $query = Barang::where('jenis_barang', 'Tablet')->latest('created_at');
                break;
            case 'baru':
                $query = Barang::where('jenis_barang', 'Tablet')->where('status', 'Baru')->latest('created_at');
                break;
            case 'backup':
                $query = Barang::where('jenis_barang', 'Tablet')->where('status', 'Backup')->latest('created_at');
                break;
            case 'aktif':
                $query = Barang::where('jenis_barang', 'Tablet')
                    ->where('status', 'Aktif')
                    ->with(['menuAktif.departemen', 'menuAktif.lokasi', 'ipAddress']);
            
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
                    ->orderBy('tbl_lokasi.nama_lokasi', 'asc')
                    ->orderBy('tbl_departemen.nama_departemen', 'asc');
                break;
            case 'pemusnahan':
                $query = Barang::where('jenis_barang', 'Tablet')->where('status', 'Pemusnahan')->latest('created_at');
                break;
            case 'riwayat':
                $query = Barang::where('jenis_barang', 'Tablet')
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
                return redirect()->route('tablet.index', ['tab' => 'barang']);
        }

        $data = $query->get();
        $lokasi = Lokasi::orderBy('nama_lokasi', 'asc')->get();
        $departemen = Departemen::orderBy('nama_departemen', 'asc')->get();
        $ipAddresses = IpAddress::where('status', 'Available')->get();
        $tipeMerk = TipeBarang::where('jenis_barang', 'Tablet')->orderBy('tipe_merk', 'asc')->get();

        $viewPath = auth()->user()->role === 'admin' ? 'admin.tablet.index' : 'user.tablet.index';
        return view($viewPath, compact(
            'title', 'breadcrumbs', 'tab', 'data', 'lokasi', 'departemen', 'ipAddresses', 'lokasi_id', 'tipeMerk'
        ));
    }

    public function create()
    {
        $title = 'Tambah Tablet';
        $breadcrumbs = [
            ['url' => route('tablet.index', 'barang'), 'text' => 'Tablet'],
            ['url' => '#', 'text' => 'Tambah Tablet'],
        ];

        $tipeBarang = TipeBarang::where('jenis_barang', 'Tablet')->orderBy('tipe_merk', 'asc')->get();
        return view('admin.tablet.create', compact('title', 'breadcrumbs', 'tipeBarang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipe_merk' => 'required',
            'serial' => 'required|unique:tbl_barang,serial',
            'tahun_perolehan' => 'required|date_format:Y-m',
            'spesifikasi_keys' => 'required|array',
            'spesifikasi_values' => 'required|array',
            'status' => 'required|in:Backup,Baru'
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
                'jenis_barang' => 'Tablet',
                'model' => 'Tablet',
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
                ->route('tablet.index', ['tab' => 'barang'])
                ->with('success', 'Tablet berhasil ditambahkan');
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
        $title = 'Edit Tablet';
        $breadcrumbs = [
            ['url' => route('tablet.index', 'barang'), 'text' => 'Tablet'],
            ['url' => '#', 'text' => 'Edit Tablet'],
        ];

        $barang = Barang::where('id_barang', $id)
            ->where('jenis_barang', 'Tablet')
            ->firstOrFail();

        $tipeBarang = TipeBarang::where('jenis_barang', 'Tablet')->orderBy('tipe_merk', 'asc')->get();

        return view('admin.tablet.edit', compact('title', 'breadcrumbs', 'barang', 'tipeBarang'));
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
                'model' => 'Tablet',
                'tipe_merk' => TipeBarang::findOrFail($request->tipe_merk)->tipe_merk,
                'serial' => $request->serial,
                'spesifikasi' => json_encode($spesifikasi),
                'tahun_perolehan' => $tahunPerolehan,
                'keterangan' => $request->keterangan,
            ]);

            DB::commit();
            return redirect()
                ->route('tablet.index')
                ->with('success', 'Data tablet berhasil diperbarui');
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
                ->route('tablet.index')
                ->with('success', 'Data tablet berhasil dihapus');
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
            'ip_address' => 'nullable|exists:tbl_ip_address,id_ip',
            'user' => 'required'
        ]);

        DB::beginTransaction();
        try {
            $barang = Barang::where('id_barang', $id)
                ->where('jenis_barang', 'Tablet')
                ->whereIn('status', ['Backup', 'Baru'])
                ->firstOrFail();
            
            $barang->update([
                'status' => 'Aktif', 
            ]);

            // Create menu aktif entry
            MenuAktif::create([
                'id_barang' => $id,
                'id_lokasi' => $request->id_lokasi,
                'id_departemen' => $request->id_departemen,
                'id_ip' => $request->ip_address,
                'user' => $request->user,
                'keterangan' => $request->keterangan
            ]);

            // Update IP Address status if provided
            if ($request->ip_address) {
                IpAddress::where('id_ip', $request->ip_address)
                    ->update([
                        'status' => 'In Use',
                        'id_barang' => $barang->id_barang
                    ]);
            }

            // Create riwayat entry
            Riwayat::create([
                'id_barang' => $barang->id_barang,
                'id_lokasi' => $request->id_lokasi,
                'id_departemen' => $request->id_departemen,
                'user' => $request->user,
                'waktu_awal' => now(),
                'status' => 'Aktif',
                'keterangan' => $request->keterangan
            ]);

            // Delete from backup
            MenuBackup::where('id_barang', $barang->id_barang)->delete();

            DB::commit();
            return redirect()
                ->back()
                ->with('success', 'Switch berhasil diaktivasi');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function updateTeknis(Request $request, $id)
    {
        $request->validate([
            'id_lokasi' => 'nullable|exists:tbl_lokasi,id_lokasi',
            'id_departemen' => 'nullable|exists:tbl_departemen,id_departemen', 
            'ip_address' => 'nullable|exists:tbl_ip_address,id_ip',
        ]);

        DB::beginTransaction();
        try {
            $barang = Barang::findOrFail($id);
            $menuAktif = MenuAktif::where('id_barang', $id)->firstOrFail();

            // Only handle IP changes if ip_address is present in request
            if ($request->has('ip_address')) {
                // If there's an existing IP address, set it back to available
                if ($menuAktif->id_ip) {
                    IpAddress::where('id_ip', $menuAktif->id_ip)
                        ->update([
                            'status' => 'Available',
                            'id_barang' => null
                        ]);
                }

                // If new IP is provided, update it
                if ($request->ip_address) {
                    IpAddress::where('id_ip', $request->ip_address)
                        ->update([
                            'status' => 'In Use',
                            'id_barang' => $id
                        ]);
                }
            }

            // Prepare update data - only include fields that were provided
            $updateData = [];
            
            if ($request->filled('id_lokasi')) {
                $updateData['id_lokasi'] = $request->id_lokasi;
            }
            
            if ($request->filled('id_departemen')) {
                $updateData['id_departemen'] = $request->id_departemen;
            }

            if ($request->has('ip_address')) {
                $updateData['id_ip'] = $request->ip_address;
            }

            // Update menu aktif if there are any changes
            if (!empty($updateData)) {
                $menuAktif->update($updateData);
            }

            DB::commit();
            return back()->with('success', 'Data teknis berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function updateAktivasi(Request $request, $id)
    {
        $request->validate([
            'id_lokasi' => 'required|exists:tbl_lokasi,id_lokasi',
            'id_departemen' => 'required|exists:tbl_departemen,id_departemen',
            'ip_address' => 'nullable|exists:tbl_ip_address,id_ip',
            'user' => 'required'
        ]);

        DB::beginTransaction();
        try {
            $barang = Barang::findOrFail($id);
            $menuAktif = MenuAktif::where('id_barang', $id)->firstOrFail();

            // Release old IP if exists
            if ($menuAktif->id_ip) {
                IpAddress::where('id_ip', $menuAktif->id_ip)
                    ->update([
                        'status' => 'Available',
                        'id_barang' => null
                    ]);
            }

            // Update MenuAktif
            $menuAktif->update([
                'id_lokasi' => $request->id_lokasi,
                'id_departemen' => $request->id_departemen,
                'id_ip' => $request->ip_address,
                'user' => $request->user,
                'keterangan' => $request->keterangan
            ]);

            // Update IP status if new IP is provided
            if ($request->ip_address) {
                IpAddress::where('id_ip', $request->ip_address)
                    ->update([
                        'status' => 'In Use',
                        'id_barang' => $id
                    ]);
            }

            // Close previous riwayat
            Riwayat::where('id_barang', $id)
                ->whereNull('waktu_akhir')
                ->update([
                    'waktu_akhir' => now(),
                    'status' => 'Non-Aktif'
                ]);

            // Create new riwayat
            Riwayat::create([
                'id_barang' => $id,
                'id_lokasi' => $request->id_lokasi,
                'id_departemen' => $request->id_departemen,
                'user' => $request->user,
                'waktu_awal' => now(),
                'status' => 'Aktif',
                'keterangan' => $request->keterangan
            ]);

            DB::commit();
            return back()->with('success', 'Data aktivasi berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
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
                ->where('jenis_barang', 'Tablet')
                ->where('status', 'Backup')
                ->firstOrFail();
            
            // Check if tablet has no history
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
                    ->route('tablet.index', ['tab' => 'backup'])
                    ->with('success', 'Tablet berhasil dimusnahkan');
            } else {
                throw new \Exception('Tablet tidak dapat dimusnahkan karena belum memiliki riwayat penggunaan');
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
                ->where('jenis_barang', 'Tablet')
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

            // Get MenuAktif data for IP cleanup
            $menuAktif = MenuAktif::where('id_barang', $id)->first();

            // Update IP Address status if exists
            if ($menuAktif && $menuAktif->id_ip) {
                IpAddress::where('id_ip', $menuAktif->id_ip)
                    ->update([
                        'status' => 'Available',
                        'id_barang' => null
                    ]);
            }

            // Delete from menu aktif
            MenuAktif::where('id_barang', $id)->delete();

            DB::commit();
            return redirect()
                ->route('tablet.index', ['tab' => 'aktif'])
                ->with('success', 'Tablet berhasil dikembalikan ke backup');
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
                ->where('jenis_barang', 'Tablet')
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

            // Get MenuAktif data for IP cleanup
            $menuAktif = MenuAktif::where('id_barang', $id)->first();

            // Update IP Address status if exists
            if ($menuAktif && $menuAktif->id_ip) {
                IpAddress::where('id_ip', $menuAktif->id_ip)
                    ->update([
                        'status' => 'Available',
                        'id_barang' => null
                    ]);
            }

            // Delete from menu aktif
            MenuAktif::where('id_barang', $id)->delete();

            DB::commit();
            return redirect()
                ->route('tablet.index', ['tab' => 'aktif'])
                ->with('success', 'Tablet berhasil dipindahkan ke pemusnahan');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function getDestroyedByYear($year)
    {
        $tablets = Barang::where('jenis_barang', 'Tablet')
            ->whereHas('menuPemusnahan', function($query) use ($year) {
                $query->whereYear('created_at', $year);
            })
            ->with('menuPemusnahan')
            ->get();

        return response()->json($tablets);
    }

    public function destroyMultiple(Request $request)
    {
        try {
            $tabletIds = $request->tablets;
            
            // Validate that all IDs belong to tablets
            $validTablets = Barang::where('jenis_barang', 'Tablet')
                ->whereIn('id_barang', $tabletIds)
                ->count();
                
            if ($validTablets !== count($tabletIds)) {
                throw new \Exception('Invalid tablet IDs detected');
            }
            
            // Begin transaction
            DB::beginTransaction();
            
            // Delete related menuPemusnahan records
            MenuPemusnahan::whereIn('id_barang', $tabletIds)->delete();
            
            // Delete tablets
            Barang::whereIn('id_barang', $tabletIds)->delete();
            
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
