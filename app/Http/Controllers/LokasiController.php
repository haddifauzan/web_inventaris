<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Lokasi;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Cache;

class LokasiController extends Controller
{
    private function forgetLocationCache() {
        Cache::forget('lokasi');
    }

    public function index()
    {
        $title = 'Data Lokasi';
        $breadcrumbs = [
            ['url' => '#', 'text' => 'Data Master'],
            ['url' => route('lokasi.index'), 'text' => 'Data Lokasi']
        ];

        $lokasi = Cache::rememberForever('lokasi', function () {
            return Lokasi::all();
        });

        return view('admin.master.lokasi', compact('title', 'breadcrumbs', 'lokasi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lokasi' => 'required|string|max:255',
            'deskripsi' => 'string|nullable'
        ]);

        Lokasi::create($request->all());

        $this->forgetLocationCache();

        return redirect()->route('lokasi.index')->with('success', 'Data lokasi berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_lokasi' => 'required|string|max:255',
            'deskripsi' => 'string|nullable'
        ]);

        $lokasi = Lokasi::findOrFail($id);
        $lokasi->update($request->all());

        $this->forgetLocationCache();

        return redirect()->route('lokasi.index')->with('success', 'Data lokasi berhasil diperbarui');
    }

    public function destroy($id)
    {
        $lokasi = Lokasi::findOrFail($id);
        $lokasi->delete();

        $this->forgetLocationCache();

        return redirect()->route('lokasi.index')->with('success', 'Data lokasi berhasil dihapus');
    }
}