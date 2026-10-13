<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use Illuminate\Http\Request;

class DepartemenController extends Controller
{
    public function index(Request $request)
    {
        $query = Departemen::withCount(['dosens', 'mahasiswas']);

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('nama_departemen', 'like', "%{$search}%")
                  ->orWhere('kode_departemen', 'like', "%{$search}%");
            });
        }

        $departemens = $query->orderBy('nama_departemen', 'asc')->paginate(15);
        return view('departemen.index', compact('departemens'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_departemen' => ['required', 'string', 'max:255'],
            'kode_departemen' => ['nullable', 'string', 'max:50', 'unique:departemens,kode_departemen'],
        ], [
            'nama_departemen.required' => 'Nama departemen wajib diisi.',
            'kode_departemen.unique' => 'Kode departemen sudah digunakan.',
        ]);

        Departemen::create($validated);
        return redirect()->route('departemen.index')->with('success', 'Departemen berhasil ditambahkan!');
    }

    public function update(Request $request, Departemen $departemen)
    {
        $validated = $request->validate([
            'nama_departemen' => ['required', 'string', 'max:255'],
            'kode_departemen' => ['nullable', 'string', 'max:50', 'unique:departemens,kode_departemen,' . $departemen->id],
        ], [
            'nama_departemen.required' => 'Nama departemen wajib diisi.',
        ]);

        $departemen->update($validated);
        return redirect()->route('departemen.index')->with('success', 'Data departemen berhasil diperbarui!');
    }

    public function destroy(Departemen $departemen)
    {
        $departemen->delete();
        return redirect()->route('departemen.index')->with('success', 'Departemen berhasil dihapus!');
    }
}
