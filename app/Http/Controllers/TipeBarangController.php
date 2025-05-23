<?php

namespace App\Http\Controllers;

use App\Models\TipeBarang;
use Illuminate\Http\Request;

class TipeBarangController extends Controller
{
    public function index()
    {
        $title = 'Data Merk Tipe Barang';
        $breadcrumbs = [
            ['url' => '#', 'text' => 'Data Master'],
            ['url' => route('tipe-barang.index'), 'text' => 'Data Merk Tipe Barang']
        ];
        $tipeBarang = TipeBarang::orderBy('tipe_merk', 'asc')->get();
        return view('admin.master.tipe', compact('title', 'breadcrumbs', 'tipeBarang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_barang' => 'required|string',
            'tipe_merk' => 'required|string|unique:tbl_tipe_barang,tipe_merk',
            'spesifikasi' => 'required|string'
        ]);

        TipeBarang::create([
            'jenis_barang' => $request->jenis_barang,
            'tipe_merk' => $request->tipe_merk,
            'spesifikasi' => $request->spesifikasi
        ]);

        return redirect()->route('tipe-barang.index')->with('success', 'Data berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'jenis_barang' => 'required|string',
            'tipe_merk' => 'required|string|unique:tbl_tipe_barang,tipe_merk,' . $id . ',id_tipe_barang',
            'spesifikasi' => 'required|string'
        ]);

        $tipeBarang = TipeBarang::findOrFail($id);
        $tipeBarang->update([
            'jenis_barang' => $request->jenis_barang,
            'tipe_merk' => $request->tipe_merk,
            'spesifikasi' => $request->spesifikasi
        ]);

        return redirect()->route('tipe-barang.index')->with('success', 'Data berhasil diupdate');
    }

    public function destroy($id)
    {
        $tipeBarang = TipeBarang::findOrFail($id);
        $tipeBarang->delete();

        return redirect()->route('tipe-barang.index')->with('success', 'Data berhasil dihapus');
    }

    public function getSpesifikasiKomputer($id)
    {
        $tipeBarang = TipeBarang::where('jenis_barang', 'Komputer')
                               ->where('id_tipe_barang', $id)
                               ->first();
                               
        if (!$tipeBarang) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $tipeBarang->spesifikasi
        ]);
    }

    public function getSpesifikasiTablet($id)
    {
        $tipeBarang = TipeBarang::where('jenis_barang', 'Tablet')
                               ->where('id_tipe_barang', $id)
                               ->first();
                               
        if (!$tipeBarang) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $tipeBarang->spesifikasi
        ]);
    }

    public function getSpesifikasiSwitch($id)
    {
        $tipeBarang = TipeBarang::where('jenis_barang', 'Switch')
                               ->where('id_tipe_barang', $id)
                               ->first();
                               
        if (!$tipeBarang) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $tipeBarang->spesifikasi
        ]);
    }
}
