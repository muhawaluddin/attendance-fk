<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index(Request $request)
    {
        $query = Kelas::withCount('mahasiswas');

        if ($request->filled('q')) {
            $query->where('nama_kelas', 'like', "%{$request->q}%");
        }

        $kelas = $query->orderBy('nama_kelas', 'asc')->paginate(15);
        return view('kelas.index', compact('kelas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kelas' => ['required', 'string', 'max:255'],
            'angkatan' => ['nullable', 'string', 'max:50'],
        ], [
            'nama_kelas.required' => 'Nama kelas wajib diisi.',
        ]);

        Kelas::create($validated);
        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil ditambahkan!');
    }

    public function update(Request $request, Kelas $kelas)
    {
        $validated = $request->validate([
            'nama_kelas' => ['required', 'string', 'max:255'],
            'angkatan' => ['nullable', 'string', 'max:50'],
        ], [
            'nama_kelas.required' => 'Nama kelas wajib diisi.',
        ]);

        $kelas->update($validated);
        return redirect()->route('kelas.index')->with('success', 'Data kelas berhasil diperbarui!');
    }

    public function destroy(Kelas $kelas)
    {
        $kelas->delete();
        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil dihapus!');
    }
}
