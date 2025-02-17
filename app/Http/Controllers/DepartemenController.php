<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Departemen;
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

        $departemen = Departemen::orderBy('nama_departemen', 'asc')->get();

        return view('admin.master.departemen', compact('title', 'breadcrumbs', 'departemen'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_departemen' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
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

}