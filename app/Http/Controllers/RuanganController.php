<?php

namespace App\Http\Controllers;

use App\Models\Mesin;
use App\Models\Ruangan;
use Illuminate\Http\Request;

class RuanganController extends Controller
{
    public function index(Request $request)
    {
        $query = Ruangan::with(['mesins', 'jadwalJams']);

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('nama_ruangan', 'like', "%{$search}%")
                  ->orWhere('kode_ruangan', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%");
            });
        }

        $ruangans = $query->orderBy('nama_ruangan', 'asc')->paginate(15);
        $availableMesins = Mesin::orderBy('serial', 'asc')->get();

        return view('ruangan.index', compact('ruangans', 'availableMesins'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_ruangan' => ['required', 'string', 'max:255'],
            'kode_ruangan' => ['nullable', 'string', 'max:50'],
            'kapasitas' => ['nullable', 'integer', 'min:1'],
            'lokasi' => ['nullable', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string'],
            'mesin_ids' => ['nullable', 'array'],
            'mesin_ids.*' => ['exists:mesins,id'],
        ], [
            'nama_ruangan.required' => 'Nama ruang kuliah wajib diisi.',
        ]);

        $ruangan = Ruangan::create([
            'nama_ruangan' => $validated['nama_ruangan'],
            'kode_ruangan' => $validated['kode_ruangan'] ?? null,
            'kapasitas' => $validated['kapasitas'] ?? null,
            'lokasi' => $validated['lokasi'] ?? null,
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        if (!empty($validated['mesin_ids'])) {
            Mesin::whereIn('id', $validated['mesin_ids'])->update(['ruangan_id' => $ruangan->id]);
        }

        return redirect()->route('ruangan.index')->with('success', 'Ruang kuliah berhasil ditambahkan!');
    }

    public function update(Request $request, Ruangan $ruangan)
    {
        $validated = $request->validate([
            'nama_ruangan' => ['required', 'string', 'max:255'],
            'kode_ruangan' => ['nullable', 'string', 'max:50'],
            'kapasitas' => ['nullable', 'integer', 'min:1'],
            'lokasi' => ['nullable', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string'],
            'mesin_ids' => ['nullable', 'array'],
            'mesin_ids.*' => ['exists:mesins,id'],
        ], [
            'nama_ruangan.required' => 'Nama ruang kuliah wajib diisi.',
        ]);

        $ruangan->update([
            'nama_ruangan' => $validated['nama_ruangan'],
            'kode_ruangan' => $validated['kode_ruangan'] ?? null,
            'kapasitas' => $validated['kapasitas'] ?? null,
            'lokasi' => $validated['lokasi'] ?? null,
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        // Update relasi mesin
        Mesin::where('ruangan_id', $ruangan->id)->update(['ruangan_id' => null]);
        if (!empty($validated['mesin_ids'])) {
            Mesin::whereIn('id', $validated['mesin_ids'])->update(['ruangan_id' => $ruangan->id]);
        }

        return redirect()->route('ruangan.index')->with('success', 'Data ruang kuliah berhasil diperbarui!');
    }

    public function destroy(Ruangan $ruangan)
    {
        $ruangan->delete();
        return redirect()->route('ruangan.index')->with('success', 'Ruang kuliah berhasil dihapus!');
    }
}
