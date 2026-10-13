<?php

namespace App\Http\Controllers;

use App\Models\MataKuliah;
use Illuminate\Http\Request;

class MataKuliahController extends Controller
{
    public function index(Request $request)
    {
        $query = MataKuliah::withCount('jadwals');

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('nama_mk', 'like', "%{$search}%")
                  ->orWhere('kode_mk', 'like', "%{$search}%");
            });
        }

        $mataKuliahs = $query->orderBy('nama_mk', 'asc')->paginate(15);
        return view('mata-kuliah.index', compact('mataKuliahs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_mk' => ['required', 'string', 'max:255'],
            'kode_mk' => ['nullable', 'string', 'max:50', 'unique:mata_kuliahs,kode_mk'],
            'sks' => ['nullable', 'integer', 'min:1'],
            'semester' => ['nullable', 'string', 'max:50'],
        ], [
            'nama_mk.required' => 'Nama blok/mata kuliah wajib diisi.',
            'kode_mk.unique' => 'Kode blok/mata kuliah sudah ada.',
        ]);

        MataKuliah::create($validated);
        return redirect()->route('mata-kuliah.index')->with('success', 'Mata kuliah / Blok berhasil ditambahkan!');
    }

    public function update(Request $request, MataKuliah $mataKuliah)
    {
        $validated = $request->validate([
            'nama_mk' => ['required', 'string', 'max:255'],
            'kode_mk' => ['nullable', 'string', 'max:50', 'unique:mata_kuliahs,kode_mk,' . $mataKuliah->id],
            'sks' => ['nullable', 'integer', 'min:1'],
            'semester' => ['nullable', 'string', 'max:50'],
        ], [
            'nama_mk.required' => 'Nama blok/mata kuliah wajib diisi.',
        ]);

        $mataKuliah->update($validated);
        return redirect()->route('mata-kuliah.index')->with('success', 'Data mata kuliah / Blok berhasil diperbarui!');
    }

    public function destroy(MataKuliah $mataKuliah)
    {
        $mataKuliah->delete();
        return redirect()->route('mata-kuliah.index')->with('success', 'Mata kuliah / Blok berhasil dihapus!');
    }
}
