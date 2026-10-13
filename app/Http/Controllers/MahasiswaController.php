<?php

namespace App\Http\Controllers;

use App\Imports\MahasiswaImport;
use App\Models\Departemen;
use App\Models\Kelas;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class MahasiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = Mahasiswa::with(['departemen', 'kelas']);

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('nama_mahasiswa', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%")
                  ->orWhere('id_finger', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        if ($request->filled('departemen_id')) {
            $query->where('departemen_id', $request->departemen_id);
        }

        if ($request->filled('angkatan')) {
            $query->where('angkatan', $request->angkatan);
        }

        $mahasiswas = $query->orderBy('nim', 'asc')->paginate(20);
        $departemens = Departemen::orderBy('nama_departemen', 'asc')->get();
        $kelas = Kelas::orderBy('nama_kelas', 'asc')->get();
        $angkatanList = Mahasiswa::select('angkatan')->whereNotNull('angkatan')->distinct()->pluck('angkatan');

        return view('mahasiswa.index', compact('mahasiswas', 'departemens', 'kelas', 'angkatanList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nim' => ['required', 'string', 'max:50', 'unique:mahasiswas,nim'],
            'nama_mahasiswa' => ['required', 'string', 'max:255'],
            'id_finger' => ['nullable', 'string', 'max:50'],
            'departemen_id' => ['nullable', 'exists:departemens,id'],
            'kelas_id' => ['nullable', 'exists:kelas,id'],
            'angkatan' => ['nullable', 'string', 'max:20'],
            'no_hp' => ['nullable', 'string', 'max:30'],
            'jenis_kelamin' => ['required', 'in:L,P'],
        ], [
            'nim.required' => 'NIM wajib diisi.',
            'nim.unique' => 'NIM sudah terdaftar.',
            'nama_mahasiswa.required' => 'Nama mahasiswa wajib diisi.',
        ]);

        Mahasiswa::create($validated);
        return redirect()->route('mahasiswa.index')->with('success', 'Data mahasiswa berhasil ditambahkan!');
    }

    public function update(Request $request, Mahasiswa $mahasiswa)
    {
        $validated = $request->validate([
            'nim' => ['required', 'string', 'max:50', 'unique:mahasiswas,nim,' . $mahasiswa->id],
            'nama_mahasiswa' => ['required', 'string', 'max:255'],
            'id_finger' => ['nullable', 'string', 'max:50'],
            'departemen_id' => ['nullable', 'exists:departemens,id'],
            'kelas_id' => ['nullable', 'exists:kelas,id'],
            'angkatan' => ['nullable', 'string', 'max:20'],
            'no_hp' => ['nullable', 'string', 'max:30'],
            'jenis_kelamin' => ['required', 'in:L,P'],
        ], [
            'nim.required' => 'NIM wajib diisi.',
            'nama_mahasiswa.required' => 'Nama mahasiswa wajib diisi.',
        ]);

        $mahasiswa->update($validated);
        return redirect()->route('mahasiswa.index')->with('success', 'Data mahasiswa berhasil diperbarui!');
    }

    public function destroy(Mahasiswa $mahasiswa)
    {
        $mahasiswa->delete();
        return redirect()->route('mahasiswa.index')->with('success', 'Data mahasiswa berhasil dihapus!');
    }

    public function destroyMany(Request $request)
    {
        $ids = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['exists:mahasiswas,id'],
        ])['ids'];

        $count = Mahasiswa::whereIn('id', $ids)->delete();
        return redirect()->route('mahasiswa.index')->with('success', "{$count} data mahasiswa berhasil dihapus.");
    }

    public function resetPassword(Mahasiswa $mahasiswa)
    {
        // Reset user password if account exists
        $user = User::where('username', $mahasiswa->nim)->first();
        if ($user) {
            $user->update(['password' => Hash::make($mahasiswa->nim)]);
            return back()->with('success', "Password akun mahasiswa {$mahasiswa->nama_mahasiswa} berhasil direset ke NIM.");
        }

        return back()->with('info', "Mahasiswa {$mahasiswa->nama_mahasiswa} belum memiliki akun login khusus (login default menggunakan NIM).");
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

        $import = new MahasiswaImport();
        Excel::import($import, $request->file('file'));

        $failures = $import->failures();
        $failCount = count($failures);

        if ($failCount > 0) {
            $msg = "Import selesai. Beberapa baris dilewati ($failCount baris gagal — mungkin NIM duplikat atau data tidak lengkap).";
            return redirect()->route('mahasiswa.index')->with('success', $msg);
        }

        return redirect()->route('mahasiswa.index')->with('success', 'Import data mahasiswa berhasil!');
    }
}
