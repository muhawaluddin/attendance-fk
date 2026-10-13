<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use App\Models\Dosen;
use App\Models\Jadwal;
use App\Models\JadwalDosen;
use App\Models\JadwalHari;
use App\Models\JadwalJam;
use App\Models\JadwalKelas;
use App\Models\JadwalMahasiswa;
use App\Models\Kelas;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Pengaturan;
use App\Models\Ruangan;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        $query = Jadwal::with(['mataKuliah', 'koordinator', 'sekretaris', 'jadwalKelas.kelas'])
            ->withCount('jadwalKelas');

        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        if ($request->filled('thn_ajaran')) {
            $query->where('thn_ajaran', $request->thn_ajaran);
        }

        if ($request->filled('mata_kuliah_id')) {
            $query->where('mata_kuliah_id', $request->mata_kuliah_id);
        }

        $jadwals = $query->orderBy('tgl_mulai', 'desc')->paginate(12);

        $mataKuliahs = MataKuliah::orderBy('nama_mk')->get();
        $dosens = Dosen::orderBy('nama_dosen')->get();
        $kelas = Kelas::orderBy('nama_kelas')->get();
        $pengaturans = Pengaturan::orderBy('nama_pengaturan')->get();

        return view('jadwal.index', compact('jadwals', 'mataKuliahs', 'dosens', 'kelas', 'pengaturans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'semester' => ['required', 'string'],
            'thn_ajaran' => ['required', 'string'],
            'mata_kuliah_id' => ['required', 'exists:mata_kuliahs,id'],
            'tgl_mulai' => ['required', 'date'],
            'tgl_selesai' => ['required', 'date', 'after_or_equal:tgl_mulai'],
            'dosen_koordinator_id' => ['nullable', 'exists:dosens,id'],
            'dosen_sekretaris_id' => ['nullable', 'exists:dosens,id'],
            'pengaturan_id' => ['nullable', 'exists:pengaturans,id'],
            'kelas_ids' => ['required', 'array', 'min:1'],
            'kelas_ids.*' => ['exists:kelas,id'],
        ], [
            'semester.required' => 'Pilih semester.',
            'thn_ajaran.required' => 'Tahun ajaran wajib diisi.',
            'mata_kuliah_id.required' => 'Pilih blok / mata kuliah.',
            'tgl_mulai.required' => 'Tanggal mulai wajib diisi.',
            'tgl_selesai.required' => 'Tanggal selesai wajib diisi.',
            'tgl_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'kelas_ids.required' => 'Pilih minimal satu kelas.',
        ]);

        $jadwal = Jadwal::create([
            'semester' => $validated['semester'],
            'thn_ajaran' => $validated['thn_ajaran'],
            'mata_kuliah_id' => $validated['mata_kuliah_id'],
            'tgl_mulai' => $validated['tgl_mulai'],
            'tgl_selesai' => $validated['tgl_selesai'],
            'dosen_koordinator_id' => $validated['dosen_koordinator_id'] ?? null,
            'dosen_sekretaris_id' => $validated['dosen_sekretaris_id'] ?? null,
            'pengaturan_id' => $validated['pengaturan_id'] ?? Pengaturan::where('is_active', true)->value('id'),
            'status' => 'aktif',
        ]);

        // Hubungkan kelas & generate hari (Senin s/d Jumat)
        $period = CarbonPeriod::create($validated['tgl_mulai'], $validated['tgl_selesai']);

        foreach ($validated['kelas_ids'] as $kelasId) {
            $jKelas = JadwalKelas::create([
                'jadwal_id' => $jadwal->id,
                'kelas_id' => $kelasId,
            ]);

            // Auto-assign mahasiswa yang terdaftar di kelas tersebut
            $mahasiswas = Mahasiswa::where('kelas_id', $kelasId)->get();
            foreach ($mahasiswas as $mhs) {
                JadwalMahasiswa::create([
                    'jadwal_kelas_id' => $jKelas->id,
                    'mahasiswa_id' => $mhs->id,
                ]);
            }

            // Generate hari perkuliahan
            foreach ($period as $date) {
                if ($date->isWeekend()) {
                    continue;
                }
                $namaHari = match ($date->dayOfWeek) {
                    1 => 'Senin',
                    2 => 'Selasa',
                    3 => 'Rabu',
                    4 => 'Kamis',
                    5 => 'Jumat',
                    default => 'Hari'
                };

                JadwalHari::create([
                    'jadwal_kelas_id' => $jKelas->id,
                    'tanggal' => $date->toDateString(),
                    'hari' => $namaHari,
                ]);
            }
        }

        return redirect()->route('jadwal.index')->with('success', 'Jadwal kuliah berhasil dibuat dan tanggal perkuliahan otomatis digenerate!');
    }

    public function show(Jadwal $jadwal)
    {
        $jadwal->load(['mataKuliah', 'koordinator', 'sekretaris', 'pengaturan', 'jadwalKelas.kelas']);
        return view('jadwal.show', compact('jadwal'));
    }

    public function destroy(Jadwal $jadwal)
    {
        $jadwal->delete();
        return redirect()->route('jadwal.index')->with('success', 'Jadwal kuliah berhasil dihapus!');
    }

    /**
     * Submenu Jadwal Perkelas
     */
    public function jadwalPerkelas(JadwalKelas $jadwalKelas)
    {
        $jadwalKelas->load([
            'jadwal.mataKuliah',
            'jadwal.pengaturan',
            'kelas',
            'jadwalHaris.jadwalJams.ruangan',
            'jadwalHaris.jadwalJams.departemen',
            'jadwalHaris.jadwalJams.jadwalDosens.dosen',
        ]);

        $ruangans = Ruangan::orderBy('nama_ruangan')->get();
        $departemens = Departemen::orderBy('nama_departemen')->get();
        $dosens = Dosen::orderBy('nama_dosen')->get();
        $pengaturan = $jadwalKelas->jadwal->pengaturan ?? Pengaturan::where('is_active', true)->first() ?? new Pengaturan();

        return view('jadwal.perkelas', compact('jadwalKelas', 'ruangans', 'departemens', 'dosens', 'pengaturan'));
    }

    /**
     * Tambah Hari Perkuliahan ke Jadwal Kelas
     */
    public function storeHari(Request $request, JadwalKelas $jadwalKelas)
    {
        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
        ]);

        $carbon = Carbon::parse($validated['tanggal']);
        $namaHari = match ($carbon->dayOfWeek) {
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
        };

        $hari = JadwalHari::firstOrCreate([
            'jadwal_kelas_id' => $jadwalKelas->id,
            'tanggal' => $validated['tanggal'],
        ], [
            'hari' => $namaHari,
        ]);

        return back()->with('success', 'Hari perkuliahan tanggal ' . $hari->tanggal->format('d/m/Y') . ' berhasil ditambahkan.');
    }

    /**
     * Hapus Hari Perkuliahan
     */
    public function destroyHari(JadwalHari $jadwalHari)
    {
        $jadwalHari->delete();
        return back()->with('success', 'Hari perkuliahan beserta jadwal jamnya berhasil dihapus.');
    }

    /**
     * Tambah Slot Jam Perkuliahan
     */
    public function storeJam(Request $request, JadwalHari $jadwalHari)
    {
        $validated = $request->validate([
            'jam_masuk' => ['required', 'string'],
            'jml_jam' => ['required', 'integer', 'min:1'],
            'kegiatan' => ['required', 'string'],
            'ruangan_id' => ['nullable', 'exists:ruangans,id'],
            'departemen_id' => ['nullable', 'exists:departemens,id'],
            'dosen_ids' => ['nullable', 'array'],
            'dosen_ids.*' => ['exists:dosens,id'],
        ], [
            'jam_masuk.required' => 'Jam masuk wajib diisi.',
            'jml_jam.required' => 'Jumlah jam wajib diisi.',
            'kegiatan.required' => 'Nama kegiatan / materi wajib diisi.',
        ]);

        $pengaturan = $jadwalHari->jadwalKelas->jadwal->pengaturan ?? Pengaturan::where('is_active', true)->first();
        $menitPerjam = $pengaturan->menit_perjam ?? 50;
        $menitPergantian = $pengaturan->menit_pergantian ?? 10;

        // Hitung jam keluar
        $parts = explode(':', $validated['jam_masuk']);
        $totalMinutes = ((int)$parts[0] * 60) + (int)($parts[1] ?? 0);
        $durasi = ((int)$validated['jml_jam'] * $menitPerjam) + (((int)$validated['jml_jam'] - 1) * $menitPergantian);
        $endMinutes = $totalMinutes + $durasi;
        $jamKeluar = sprintf('%02d:%02d', floor($endMinutes / 60) % 24, $endMinutes % 60);

        $jJam = JadwalJam::create([
            'jadwal_hari_id' => $jadwalHari->id,
            'ruangan_id' => $validated['ruangan_id'] ?? null,
            'departemen_id' => $validated['departemen_id'] ?? null,
            'jam_masuk' => $validated['jam_masuk'],
            'jml_jam' => $validated['jml_jam'],
            'jam_keluar' => $jamKeluar,
            'kegiatan' => $validated['kegiatan'],
        ]);

        if (!empty($validated['dosen_ids'])) {
            foreach ($validated['dosen_ids'] as $dosenId) {
                JadwalDosen::create([
                    'jadwal_jam_id' => $jJam->id,
                    'dosen_id' => $dosenId,
                ]);
            }
        }

        return back()->with('success', 'Sesi perkuliahan berhasil ditambahkan!');
    }

    /**
     * Hapus Slot Jam
     */
    public function destroyJam(JadwalJam $jadwalJam)
    {
        $jadwalJam->delete();
        return back()->with('success', 'Sesi jam perkuliahan berhasil dihapus.');
    }

    /**
     * Submenu Mahasiswa Perkelas
     */
    public function mahasiswaPerkelas(JadwalKelas $jadwalKelas)
    {
        $jadwalKelas->load(['jadwal.mataKuliah', 'kelas', 'jadwalMahasiswas.mahasiswa.departemen']);

        $registeredIds = $jadwalKelas->jadwalMahasiswas->pluck('mahasiswa_id')->toArray();
        $availableMahasiswas = Mahasiswa::whereNotIn('id', $registeredIds)
            ->orderBy('nim')
            ->get();

        return view('jadwal.mahasiswa', compact('jadwalKelas', 'availableMahasiswas'));
    }

    /**
     * Tambah Mahasiswa ke Jadwal Kelas
     */
    public function storeMahasiswa(Request $request, JadwalKelas $jadwalKelas)
    {
        $validated = $request->validate([
            'mahasiswa_ids' => ['required', 'array', 'min:1'],
            'mahasiswa_ids.*' => ['exists:mahasiswas,id'],
        ], [
            'mahasiswa_ids.required' => 'Pilih setidaknya satu mahasiswa.',
        ]);

        $count = 0;
        foreach ($validated['mahasiswa_ids'] as $mhsId) {
            $exists = JadwalMahasiswa::where('jadwal_kelas_id', $jadwalKelas->id)
                ->where('mahasiswa_id', $mhsId)
                ->exists();

            if (!$exists) {
                JadwalMahasiswa::create([
                    'jadwal_kelas_id' => $jadwalKelas->id,
                    'mahasiswa_id' => $mhsId,
                ]);
                $count++;
            }
        }

        return back()->with('success', "{$count} mahasiswa berhasil didaftarkan ke jadwal kelas ini.");
    }

    /**
     * Hapus Mahasiswa dari Jadwal Kelas
     */
    public function destroyMahasiswa(JadwalMahasiswa $jadwalMahasiswa)
    {
        $jadwalMahasiswa->delete();
        return back()->with('success', 'Mahasiswa berhasil dihapus dari jadwal kelas ini.');
    }
}
