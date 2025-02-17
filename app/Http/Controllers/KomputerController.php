<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\MenuAktif;
use App\Models\MenuBackup;
use App\Models\MenuPemusnahan;
use App\Models\Riwayat;
use App\Models\IpAddress;
use App\Models\Departemen;
use App\Models\Lokasi;
use App\Models\TipeBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\BarangKelayakanTracker;

class KomputerController extends Controller
{
    public function index(Request $request, $tab = 'barang')
    {
        $title = 'Management Komputer';
        $breadcrumbs = [['url' => route('komputer.index'), 'text' => 'Komputer']];
        $lokasi_id = $request->input('lokasi_id'); // Ambil lokasi dari request

        switch ($tab) {
            case 'barang':
                $query = Barang::where('jenis_barang', 'Komputer')->latest('created_at');
                break;
            case 'backup':
                $query = Barang::where('jenis_barang', 'Komputer')->where('status', 'Backup')->latest('created_at');
                break;
            case 'aktif':
                $query = Barang::where('jenis_barang', 'Komputer')
                    ->where('status', 'Aktif')
                    ->with(['menuAktif.departemen', 'menuAktif.lokasi', 'ipAddress']);
                
                if ($lokasi_id) {
                    $query->whereHas('menuAktif', function ($q) use ($lokasi_id) {
                        $q->where('id_lokasi', $lokasi_id);
                    });
                }
                
                $query->join('tbl_menu_aktif', 'tbl_barang.id_barang', '=', 'tbl_menu_aktif.id_barang')
                    ->join('tbl_departemen', 'tbl_menu_aktif.id_departemen', '=', 'tbl_departemen.id_departemen')
                    ->orderBy('tbl_departemen.nama_departemen', 'asc');
                break;
            case 'pemusnahan':
                $query = Barang::where('jenis_barang', 'Komputer')->where('status', 'Pemusnahan')->latest('created_at');
                break;
            case 'riwayat':
                $query = Barang::where('jenis_barang', 'Komputer')
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
                return redirect()->route('komputer.index', 'barang');
        }

        $data = $query->get();
        $lokasi = Lokasi::all();
        $departemen = Departemen::all();
        $ipAddresses = IpAddress::where('status', 'Available')->get();

        $viewPath = auth()->user()->role === 'admin' ? 'admin.komputer.index' : 'user.komputer.index';
        return view($viewPath, compact(
            'title', 'breadcrumbs', 'tab', 'data', 'lokasi', 'departemen', 'ipAddresses', 'lokasi_id'
        ));
    }


    public function create()
    {
        $title = 'Tambah Komputer';
        $breadcrumbs = [
            ['url' => route('komputer.index'), 'text' => 'Komputer'],
            ['url' => '#', 'text' => 'Tambah Komputer'],
        ];

        $tipeBarang = TipeBarang::where('jenis_barang', 'Komputer')->get();
        return view('admin.komputer.create', compact('title', 'breadcrumbs', 'tipeBarang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'model' => 'required|in:PC,Laptop',
            'tipe_merk' => 'required',
            'serial' => 'required|array',
            'serial.cpu' => 'nullable',
            'serial.monitor' => 'nullable',
            'operating_system' => 'required',
            'tahun_perolehan' => 'required|date_format:Y-m',
            'kelayakan' => 'required|numeric|min:0|max:100',
            'spesifikasi_keys' => 'required|array',
            'spesifikasi_values' => 'required|array'
        ]);

        // Ubah format tahun_perolehan menjadi YYYY-MM-01
        $tahunPerolehan = $request->tahun_perolehan . '-01';

        // Prepare serial data for JSON storage
        $serialData = json_encode($request->serial);

        // Check if either CPU or Monitor serial already exists
        $existingSerial = Barang::where(function($query) use ($serialData) {
            $serial = json_decode($serialData, true);
            $query->whereRaw("JSON_EXTRACT(serial, '$.cpu') = ?", [$serial['cpu']])
                ->orWhereRaw("JSON_EXTRACT(serial, '$.monitor') = ?", [$serial['monitor']]);
        })->first();

        if ($existingSerial) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Serial number CPU atau Monitor sudah terdaftar');
        }

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
                'jenis_barang' => 'Komputer',
                'model' => $request->model,
                'tipe_merk' => TipeBarang::findOrFail($request->tipe_merk)->tipe_merk,
                'serial' => $serialData, // Store as JSON string
                'operating_system' => $request->operating_system,
                'spesifikasi' => json_encode($spesifikasi),
                'kelayakan' => $request->kelayakan,
                'tahun_perolehan' => $tahunPerolehan, // Simpan dengan format YYYY-MM-01
                'status' => 'Backup'
            ]);

            MenuBackup::create([
                'id_barang' => $barang->id_barang,
                'keterangan' => $request->keterangan
            ]);

            DB::commit();
            return redirect()
                ->route('komputer.index')
                ->with('success', 'Komputer berhasil ditambahkan');
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
        $title = 'Edit Komputer';
        $breadcrumbs = [
            ['url' => route('komputer.index'), 'text' => 'Komputer'],
            ['url' => '#', 'text' => 'Edit Komputer'],
        ];

        $barang = Barang::where('id_barang', $id)
            ->where('jenis_barang', 'Komputer')
            ->firstOrFail();

        $tipeBarang = TipeBarang::where('jenis_barang', 'Komputer')->get();

        return view('admin.komputer.edit', compact('title', 'breadcrumbs', 'barang', 'tipeBarang'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'model' => 'required|in:PC,Laptop',
            'tipe_merk' => 'required',
            'serial' => 'required|array',
            'serial.cpu' => 'required',
            'serial.monitor' => 'required',
            'operating_system' => 'required',
            'kelayakan' => 'required|numeric|min:0|max:100',
            'tahun_perolehan' => 'required|date_format:Y-m',
            'spesifikasi_keys' => 'required|array',
            'spesifikasi_values' => 'required|array'
        ]);

        // Check if barang exists and not in Pemusnahan status
        $barang = Barang::where('id_barang', $id)
            ->where('status', '!=', 'Pemusnahan')
            ->firstOrFail();

        // Format tahun_perolehan menjadi YYYY-MM-01
        $tahunPerolehan = $request->tahun_perolehan . '-01';
        
        // Prepare serial data
        $serialData = json_encode($request->serial);
        
        $spesifikasi = [];
        $keys = $request->spesifikasi_keys ?? [];
        $values = $request->spesifikasi_values ?? [];
        
        foreach ($keys as $index => $key) {
            if (!empty($key) && isset($values[$index])) {
                $spesifikasi[$key] = $values[$index];
            }
        }

        // Check existing serials
        $existingSerial = Barang::where('id_barang', '!=', $id)
            ->where(function ($query) use ($request) {
                $query->whereRaw("JSON_EXTRACT(serial, '$.cpu') = ?", [$request->serial['cpu']])
                    ->orWhereRaw("JSON_EXTRACT(serial, '$.monitor') = ?", [$request->serial['monitor']]);
            })->first();

        if ($existingSerial) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Serial number CPU atau Monitor sudah terdaftar pada komputer lain');
        }

        DB::beginTransaction();
        try {
            $barang->update([
                'model' => $request->model,
                'tipe_merk' => TipeBarang::findOrFail($request->tipe_merk)->tipe_merk,
                'serial' => $serialData,
                'operating_system' => $request->operating_system,
                'spesifikasi' => json_encode($spesifikasi),
                'kelayakan' => $request->kelayakan,
                'tahun_perolehan' => $tahunPerolehan,
                'keterangan' => $request->keterangan
            ]);

            DB::commit();
            return redirect()
                ->route('komputer.index')
                ->with('success', 'Data komputer berhasil diperbarui');
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
                ->route('komputer.index')
                ->with('success', 'Data komputer berhasil dihapus');
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
            'komputer_name' => 'required',
            'user' => 'required'
        ]);

        DB::beginTransaction();
        try {
            $barang = Barang::where('id_barang', $id)
                ->where('jenis_barang', 'Komputer')
                ->where('status', 'Backup')
                ->firstOrFail();
            
            $barang->update([
                'status' => 'Aktif', 
            ]);

            // Create or update kelayakan tracker
            $tracker = BarangKelayakanTracker::firstOrCreate(
                ['id_barang' => $barang->id_barang],
                [
                    'last_update' => now(),
                    'accumulated_days' => 0
                ]
            );

            // If tracker already existed, update last_update but keep accumulated_days
            if (!$tracker->wasRecentlyCreated) {
                $tracker->update([
                    'last_update' => now()
                ]);
            }

            // Create menu aktif entry
            MenuAktif::create([
                'id_barang' => $id,
                'id_lokasi' => $request->id_lokasi,
                'id_departemen' => $request->id_departemen,
                'id_ip' => $request->ip_address,
                'komputer_name' => $request->komputer_name,
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
                'kelayakan_awal' => $barang->kelayakan,
                'waktu_awal' => now(),
                'status' => 'Aktif',
                'keterangan' => $request->keterangan
            ]);

            // Delete from backup
            MenuBackup::where('id_barang', $barang->id_barang)->delete();

            DB::commit();
            return redirect()
                ->route('komputer.index', ['tab' => 'backup'])
                ->with('success', 'Komputer berhasil diaktivasi');
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
                ->where('jenis_barang', 'Komputer')
                ->where('status', 'Backup')
                ->firstOrFail();
            
            // Check if computer has no history
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
                    ->route('komputer.index', ['tab' => 'backup'])
                    ->with('success', 'Komputer berhasil dimusnahkan');
            } else {
                throw new \Exception('Komputer tidak dapat dimusnahkan belum memiliki riwayat penggunaan');
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
                ->where('jenis_barang', 'Komputer')
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
                    'kelayakan_akhir' => $barang->kelayakan,
                    'status' => 'Selesai'
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
                ->route('komputer.index', ['tab' => 'aktif'])
                ->with('success', 'Komputer berhasil dikembalikan ke backup');
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
                ->where('jenis_barang', 'Komputer')
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
                    'kelayakan_akhir' => $barang->kelayakan,
                    'status' => 'Selesai'
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
                ->route('komputer.index', ['tab' => 'aktif'])
                ->with('success', 'Komputer berhasil dipindahkan ke pemusnahan');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}