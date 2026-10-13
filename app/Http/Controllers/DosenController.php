<?php

namespace App\Http\Controllers;

use App\Imports\DosenImport;
use App\Models\Departemen;
use App\Models\Dosen;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DosenController extends Controller
{
    public function index(Request $request)
    {
        $query = Dosen::with('departemen');

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('nama_dosen', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('id_finger', 'like', "%{$search}%");
            });
        }

        if ($request->filled('departemen_id')) {
            $query->where('departemen_id', $request->departemen_id);
        }

        $dosens = $query->orderBy('nama_dosen', 'asc')->paginate(15);
        $departemens = Departemen::orderBy('nama_departemen', 'asc')->get();

        return view('dosen.index', compact('dosens', 'departemens'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nip' => ['required', 'string', 'max:50', 'unique:dosens,nip'],
            'nama_dosen' => ['required', 'string', 'max:255'],
            'gelar_depan' => ['nullable', 'string', 'max:50'],
            'gelar_belakang' => ['nullable', 'string', 'max:50'],
            'id_finger' => ['nullable', 'string', 'max:50'],
            'departemen_id' => ['nullable', 'exists:departemens,id'],
            'no_hp' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:100'],
        ], [
            'nip.required' => 'NIP wajib diisi.',
            'nip.unique' => 'NIP sudah terdaftar.',
            'nama_dosen.required' => 'Nama dosen wajib diisi.',
        ]);

        Dosen::create($validated);
        return redirect()->route('dosen.index')->with('success', 'Data dosen berhasil ditambahkan!');
    }

    public function update(Request $request, Dosen $dosen)
    {
        $validated = $request->validate([
            'nip' => ['required', 'string', 'max:50', 'unique:dosens,nip,' . $dosen->id],
            'nama_dosen' => ['required', 'string', 'max:255'],
            'gelar_depan' => ['nullable', 'string', 'max:50'],
            'gelar_belakang' => ['nullable', 'string', 'max:50'],
            'id_finger' => ['nullable', 'string', 'max:50'],
            'departemen_id' => ['nullable', 'exists:departemens,id'],
            'no_hp' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:100'],
        ], [
            'nip.required' => 'NIP wajib diisi.',
            'nama_dosen.required' => 'Nama dosen wajib diisi.',
        ]);

        $dosen->update($validated);
        return redirect()->route('dosen.index')->with('success', 'Data dosen berhasil diperbarui!');
    }

    public function destroy(Dosen $dosen)
    {
        $dosen->delete();
        return redirect()->route('dosen.index')->with('success', 'Data dosen berhasil dihapus!');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ], [
            'file.required' => 'File wajib diunggah.',
            'file.mimes'    => 'Format file harus xlsx, xls, atau csv.',
            'file.max'      => 'Ukuran file maksimal 5 MB.',
        ]);

        $import = new DosenImport();
        Excel::import($import, $request->file('file'));

        $failures = $import->failures();
        $failCount = count($failures);

        if ($failCount > 0) {
            $msg = "Import selesai. Beberapa baris dilewati ($failCount baris gagal — mungkin NIP duplikat atau data tidak lengkap).";
            return redirect()->route('dosen.index')->with('success', $msg);
        }

        return redirect()->route('dosen.index')->with('success', 'Import data dosen berhasil!');
    }
}
