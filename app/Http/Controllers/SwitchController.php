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

    public function create()
    {
        $title = 'Tambah Switch';
        $breadcrumbs = [
            ['url' => route('switch.index', 'backup'), 'text' => 'Switch'],
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
                'status' => 'Backup'
            ]);

            MenuBackup::create([
                'id_barang' => $barang->id_barang,
                'keterangan' => $request->keterangan
            ]);

            DB::commit();
            return redirect()
                ->route('switch.index', ['tab' => 'backup'])
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
}
