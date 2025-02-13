<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Departemen;
use App\Models\Lokasi;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Cache;

class DepartemenController extends Controller
{
    private function forgetDepartmentCache() {
        Cache::forget('departemen');
    }

    public function index()
    {
        $title = 'Data Departemen';
        $breadcrumbs = [
            ['url' => '#', 'text' => 'Data Master'],
            ['url' => route('departemen.index'), 'text' => 'Data Departemen']
        ];

        $departemen = Departemen::with('lokasi')->get();
        $lokasi = Lokasi::all();

        return view('admin.master.departemen', compact('title', 'breadcrumbs', 'departemen', 'lokasi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_departemen' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'id_lokasi' => 'required|exists:tbl_lokasi,id_lokasi'
        ]);

        Departemen::create($request->all());

        $this->forgetDepartmentCache();

        return redirect()->route('departemen.index')->with('success', 'Data departemen berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_departemen' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'id_lokasi' => 'required|exists:tbl_lokasi,id_lokasi'
        ]);

        $departemen = Departemen::findOrFail($id);
        $departemen->update($request->all());

        $this->forgetDepartmentCache();

        return redirect()->route('departemen.index')->with('success', 'Data departemen berhasil diperbarui');
    }

    public function destroy($id)
    {
        $departemen = Departemen::findOrFail($id);
        $departemen->delete();

        $this->forgetDepartmentCache();

        return redirect()->route('departemen.index')->with('success', 'Data departemen berhasil dihapus');
    }

    public function getDepartmentsByLocation($locationId)
    {
        $departments = Departemen::where('id_lokasi', $locationId)->get();
        return response()->json(['departments' => $departments]);
    }
}