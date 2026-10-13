<?php

namespace App\Http\Controllers;

use App\Models\AttLog;
use App\Models\Departemen;
use App\Models\Dosen;
use App\Models\Jadwal;
use App\Models\JadwalHari;
use App\Models\JadwalJam;
use App\Models\JadwalKelas;
use App\Models\JadwalMahasiswa;
use App\Models\Kelas;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    /**
     * Laporan Absensi Mahasiswa
     */
    public function mahasiswa(Request $request)
    {
        $jadwalList = Jadwal::with(['mataKuliah', 'jadwalKelas.kelas'])
            ->orderBy('tgl_mulai', 'desc')
            ->get();

        $selectedJadwalId = $request->get('jadwal_id', $jadwalList->first()?->id);
        $selectedJadwal = $selectedJadwalId ? Jadwal::with('mataKuliah', 'pengaturan')->find($selectedJadwalId) : null;

        $kelasList = $selectedJadwal ? $selectedJadwal->jadwalKelas()->with('kelas')->get() : collect();
        $selectedKelasId = $request->get('jadwal_kelas_id', $kelasList->first()?->id);
        $selectedJadwalKelas = $selectedKelasId ? JadwalKelas::with('kelas')->find($selectedKelasId) : null;

        $selectedTanggal = $request->get('tanggal');
        $selectedJamId = $request->get('jadwal_jam_id');

        $laporanData = null;
        $sesiList = collect();
        $mahasiswaList = collect();

        if ($selectedJadwalKelas) {
            // Ambil sesi perkuliahan
            $sesiQuery = JadwalJam::whereHas('jadwalHari', function ($q) use ($selectedJadwalKelas, $selectedTanggal) {
                $q->where('jadwal_kelas_id', $selectedJadwalKelas->id);
                if (!empty($selectedTanggal)) {
                    $q->whereDate('tanggal', $selectedTanggal);
                }
            })->with(['jadwalHari', 'ruangan']);

            if (!empty($selectedJamId)) {
                $sesiQuery->where('id', $selectedJamId);
            }

            $sesiList = $sesiQuery->get()->sortBy(function ($item) {
                return $item->jadwalHari->tanggal->format('Y-m-d') . ' ' . $item->jam_masuk;
            })->values();

            // Ambil mahasiswa yang terdaftar di jadwal_kelas atau kelas terkait
            $enrolledMhsIds = JadwalMahasiswa::where('jadwal_kelas_id', $selectedJadwalKelas->id)->pluck('mahasiswa_id');

            $mahasiswaList = Mahasiswa::where(function ($q) use ($enrolledMhsIds, $selectedJadwalKelas) {
                $q->whereIn('id', $enrolledMhsIds);
                if ($selectedJadwalKelas->kelas_id) {
                    $q->orWhere('kelas_id', $selectedJadwalKelas->kelas_id);
                }
            })
            ->where('status_aktif', true)
            ->orderBy('nim')
            ->get();

            // Hitung absensi matriks
            $matrix = [];
            $totalSesi = $sesiList->count();

            foreach ($mahasiswaList as $mhs) {
                $row = [
                    'mahasiswa' => $mhs,
                    'sesi_status' => [],
                    'hadir' => 0,
                    'alpa' => 0,
                    'persen' => 0,
                ];

                foreach ($sesiList as $sesi) {
                    $tgl = $sesi->jadwalHari->tanggal->format('Y-m-d');
                    $jamMasuk = $sesi->jam_masuk;
                    $jamKeluar = $sesi->jam_keluar ?: $sesi->hitungJamKeluar();

                    // Cek log scan fingerprint
                    // Toleransi scan: mulai 30 menit sebelum jam_masuk s/d jam_keluar
                    $jamMasukToleransi = Carbon::parse($jamMasuk)->subMinutes(30)->format('H:i:s');

                    $scan = null;
                    if ($mhs->id_finger) {
                        $scan = AttLog::where('pin', $mhs->id_finger)
                            ->whereDate('scan_date', $tgl)
                            ->whereTime('scan_date', '>=', $jamMasukToleransi)
                            ->whereTime('scan_date', '<=', $jamKeluar . ':59')
                            ->orderBy('scan_date', 'asc')
                            ->first();
                    }

                    if ($scan) {
                        $row['hadir']++;
                        $row['sesi_status'][] = [
                            'status' => 'H',
                            'waktu' => Carbon::parse($scan->scan_date)->format('H:i'),
                            'ruangan' => $sesi->ruangan?->nama_ruangan ?? '-',
                        ];
                    } else {
                        $row['alpa']++;
                        $row['sesi_status'][] = [
                            'status' => 'A',
                            'waktu' => '-',
                            'ruangan' => $sesi->ruangan?->nama_ruangan ?? '-',
                        ];
                    }
                }

                $row['persen'] = $totalSesi > 0 ? round(($row['hadir'] / $totalSesi) * 100, 1) : 0;
                $matrix[] = $row;
            }

            $laporanData = $matrix;
        }

        // Jika export excel diminta
        if ($request->get('export') === 'excel' && $selectedJadwal && $selectedJadwalKelas) {
            return $this->exportExcelMahasiswa($selectedJadwal, $selectedJadwalKelas, $sesiList, $laporanData);
        }

        // Jika mode print diminta
        if ($request->get('print') === '1') {
            return view('laporan.print_mahasiswa', compact(
                'selectedJadwal',
                'selectedJadwalKelas',
                'sesiList',
                'laporanData'
            ));
        }

        return view('laporan.mahasiswa', compact(
            'jadwalList',
            'selectedJadwal',
            'kelasList',
            'selectedJadwalKelas',
            'selectedTanggal',
            'selectedJamId',
            'sesiList',
            'laporanData'
        ));
    }

    /**
     * Laporan Absensi Dosen (Rekapitulasi Seluruh Dosen & Rincian Sesi Per Dosen)
     */
    public function dosen(Request $request)
    {
        $departemenList = Departemen::orderBy('nama_departemen')->get();
        $selectedDepartemenId = $request->get('departemen_id');
        $search = $request->get('search');
        $semester = $request->get('semester');
        $thnAjaran = $request->get('thn_ajaran');
        $selectedDosenId = $request->get('dosen_id');
        $activeTab = $request->get('tab', $selectedDosenId ? 'detail' : 'rekap');

        // Daftar dosen untuk dropdown / filter
        $allDosenList = Dosen::with('departemen')->orderBy('nama_dosen')->get();

        // Query dosen untuk rekap (dengan filter departemen & search)
        $dosenQuery = Dosen::with('departemen');
        if (!empty($selectedDepartemenId)) {
            $dosenQuery->where('departemen_id', $selectedDepartemenId);
        }
        if (!empty($search)) {
            $dosenQuery->where(function ($q) use ($search) {
                $q->where('nama_dosen', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }
        $dosenList = $dosenQuery->orderBy('nama_dosen')->get();

        // Dosen yang dipilih untuk rincian sesi (jika ada)
        $selectedDosen = $selectedDosenId ? Dosen::with('departemen')->find($selectedDosenId) : null;

        // Ambil sesi perkuliahan sesuai semester & tahun ajaran
        $jadwalQuery = JadwalJam::with([
            'jadwalHari.jadwalKelas.jadwal.mataKuliah',
            'jadwalHari.jadwalKelas.kelas',
            'ruangan',
            'departemen',
            'jadwalDosens'
        ]);

        if (!empty($semester)) {
            $jadwalQuery->whereHas('jadwalHari.jadwalKelas.jadwal', function ($q) use ($semester) {
                $q->where('semester', $semester);
            });
        }

        if (!empty($thnAjaran)) {
            $jadwalQuery->whereHas('jadwalHari.jadwalKelas.jadwal', function ($q) use ($thnAjaran) {
                $q->where('thn_ajaran', $thnAjaran);
            });
        }

        $allSesi = $jadwalQuery->get();

        // Ambil AttLog relevan sekaligus
        $allPins = $dosenList->pluck('id_finger')->filter()->unique();
        if ($selectedDosen && $selectedDosen->id_finger) {
            $allPins->push($selectedDosen->id_finger);
        }
        $allLogs = AttLog::whereIn('pin', $allPins->unique())->get()->groupBy('pin');

        // Hitung akumulasi rekapitulasi untuk seluruh dosen dalam tabel
        $rekapDosen = $dosenList->map(function ($d) use ($allSesi, $allLogs) {
            $pin = $d->id_finger;
            $dosenLogs = ($pin && isset($allLogs[$pin])) ? $allLogs[$pin] : collect();

            $sesiAjar = $allSesi->filter(function ($sesi) use ($d) {
                return $sesi->jadwalDosens->contains('dosen_id', $d->id);
            });

            $totalSesi = $sesiAjar->count();
            $hadir = 0;

            foreach ($sesiAjar as $sesi) {
                if (!$sesi->jadwalHari) continue;
                $tgl = $sesi->jadwalHari->tanggal->format('Y-m-d');
                $jamMasukToleransi = Carbon::parse($sesi->jam_masuk)->subMinutes(30)->format('H:i:s');
                $jamKeluar = ($sesi->jam_keluar ?: $sesi->hitungJamKeluar()) . ':59';

                $hasScan = $dosenLogs->contains(function ($l) use ($tgl, $jamMasukToleransi, $jamKeluar) {
                    $scanDate = substr($l->scan_date, 0, 10);
                    $scanTime = substr($l->scan_date, 11, 8);
                    return $scanDate === $tgl && $scanTime >= $jamMasukToleransi && $scanTime <= $jamKeluar;
                });

                if ($hasScan) {
                    $hadir++;
                }
            }

            $alpa = max(0, $totalSesi - $hadir);
            $persen = $totalSesi > 0 ? round(($hadir / $totalSesi) * 100, 1) : 0;

            return [
                'id' => $d->id,
                'dosen' => $d,
                'nama' => $d->nama_lengkap,
                'nip' => $d->nip ?: '-',
                'pin' => $d->id_finger ?: '-',
                'departemen' => $d->departemen->nama_departemen ?? '-',
                'total_sesi' => $totalSesi,
                'hadir' => $hadir,
                'alpa' => $alpa,
                'persen' => $persen,
            ];
        });

        // Hitung rincian sesi spesifik dosen jika sedang melihat detail
        $laporanDosenData = collect();
        if ($selectedDosen) {
            $sesiSelected = $allSesi->filter(function ($sesi) use ($selectedDosen) {
                return $sesi->jadwalDosens->contains('dosen_id', $selectedDosen->id);
            })->sortBy(function ($item) {
                return $item->jadwalHari->tanggal->format('Y-m-d') . ' ' . $item->jam_masuk;
            });

            $pin = $selectedDosen->id_finger;
            $dosenLogs = ($pin && isset($allLogs[$pin])) ? $allLogs[$pin] : collect();

            $matrix = [];
            foreach ($sesiSelected as $sesi) {
                if (!$sesi->jadwalHari) continue;
                $tgl = $sesi->jadwalHari->tanggal->format('Y-m-d');
                $jamMasuk = $sesi->jam_masuk;
                $jamKeluar = $sesi->jam_keluar ?: $sesi->hitungJamKeluar();
                $jamMasukToleransi = Carbon::parse($jamMasuk)->subMinutes(30)->format('H:i:s');

                $scan = $dosenLogs->first(function ($l) use ($tgl, $jamMasukToleransi, $jamKeluar) {
                    $scanDate = substr($l->scan_date, 0, 10);
                    $scanTime = substr($l->scan_date, 11, 8);
                    return $scanDate === $tgl && $scanTime >= $jamMasukToleransi && $scanTime <= ($jamKeluar . ':59');
                });

                $matrix[] = [
                    'tanggal' => $sesi->jadwalHari->tanggal,
                    'hari' => $sesi->jadwalHari->hari,
                    'jam_masuk' => $jamMasuk,
                    'jam_keluar' => $jamKeluar,
                    'mk' => $sesi->jadwalHari->jadwalKelas->jadwal->mataKuliah->nama_mk ?? '-',
                    'kegiatan' => $sesi->kegiatan,
                    'kelas' => $sesi->jadwalHari->jadwalKelas->kelas->nama_kelas ?? '-',
                    'ruangan' => $sesi->ruangan?->nama_ruangan ?? '-',
                    'status' => $scan ? 'H' : 'A',
                    'checklock' => $scan ? Carbon::parse($scan->scan_date)->format('H:i') : '-',
                ];
            }

            $laporanDosenData = collect($matrix);
        }

        // Export excel
        if ($request->get('export') === 'excel') {
            if ($selectedDosen) {
                return $this->exportExcelDosen($selectedDosen, $semester, $thnAjaran, $laporanDosenData);
            }
            return $this->exportExcelRekapDosen($rekapDosen, $semester, $thnAjaran);
        }

        // Print dokumen
        if ($request->get('print') === '1') {
            if ($selectedDosen) {
                return view('laporan.print_dosen', compact('selectedDosen', 'semester', 'thnAjaran', 'laporanDosenData'));
            }
            return view('laporan.print_rekap_dosen', compact('rekapDosen', 'semester', 'thnAjaran'));
        }

        return view('laporan.dosen', compact(
            'allDosenList',
            'rekapDosen',
            'departemenList',
            'selectedDepartemenId',
            'search',
            'selectedDosen',
            'selectedDosenId',
            'semester',
            'thnAjaran',
            'laporanDosenData',
            'activeTab'
        ));
    }

    /**
     * Laporan Rekapitulasi Akumulasi Jam Harian Mahasiswa & Dosen
     */
    public function harian(Request $request)
    {
        $tanggal = $request->get('tanggal', now()->format('Y-m-d'));
        $tipe = $request->get('tipe', 'mahasiswa'); // 'mahasiswa' atau 'dosen'
        $kelasId = $request->get('kelas_id');
        $departemenId = $request->get('departemen_id');
        $search = $request->get('q');

        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $departemenList = Departemen::orderBy('nama_departemen')->get();

        $carbonTgl = Carbon::parse($tanggal);

        // Ambil semua sesi perkuliahan pada tanggal tersebut
        $sesiHariList = JadwalJam::whereHas('jadwalHari', function ($q) use ($tanggal) {
            $q->whereDate('tanggal', $tanggal);
        })->with([
            'jadwalHari.jadwalKelas.jadwal.mataKuliah',
            'jadwalHari.jadwalKelas.kelas',
            'jadwalDosens.dosen',
            'ruangan',
            'departemen',
        ])->get();

        $rekapData = collect();
        $totalJamTerjadwalGlobal = 0;
        $totalJamHadirGlobal = 0;

        if ($tipe === 'mahasiswa') {
            $jadwalKelasIds = $sesiHariList->pluck('jadwalHari.jadwal_kelas_id')->unique()->filter();

            $mahasiswaQuery = Mahasiswa::with('kelas');

            if (!empty($kelasId)) {
                $mahasiswaQuery->where('kelas_id', $kelasId);
            }

            if (!empty($search)) {
                $mahasiswaQuery->where(function ($q) use ($search) {
                    $q->where('nim', 'like', "%{$search}%")
                      ->orWhere('nama_mahasiswa', 'like', "%{$search}%");
                });
            }

            // Jika tidak ada filter khusus, utamakan mahasiswa yang terjadwal hari itu atau semua di kelas terkait
            if (empty($kelasId) && empty($search) && $jadwalKelasIds->isNotEmpty()) {
                $kelasListIds = $sesiHariList->pluck('jadwalHari.jadwalKelas.kelas_id')->unique()->filter();
                $mahasiswaIdsTerjadwal = JadwalMahasiswa::whereIn('jadwal_kelas_id', $jadwalKelasIds)
                    ->pluck('mahasiswa_id')
                    ->unique();
                $mahasiswaQuery->where(function ($q) use ($mahasiswaIdsTerjadwal, $kelasListIds) {
                    $q->whereIn('id', $mahasiswaIdsTerjadwal);
                    if ($kelasListIds->isNotEmpty()) {
                        $q->orWhereIn('kelas_id', $kelasListIds);
                    }
                });
            }

            $mahasiswas = $mahasiswaQuery->orderBy('nim')->get();

            foreach ($mahasiswas as $mhs) {
                // Sesi yang harus diikuti mahasiswa ini
                $sesiMhs = $sesiHariList->filter(function ($sesi) use ($mhs) {
                    $jadwalKelasId = $sesi->jadwalHari?->jadwal_kelas_id;
                    $isEnrolled = JadwalMahasiswa::where('jadwal_kelas_id', $jadwalKelasId)
                        ->where('mahasiswa_id', $mhs->id)
                        ->exists();

                    $isSameKelas = $sesi->jadwalHari?->jadwalKelas?->kelas_id == $mhs->kelas_id;
                    return $isEnrolled || $isSameKelas;
                })->values();

                if ($sesiMhs->isEmpty() && !empty($search)) {
                    $rekapData->push([
                        'id' => $mhs->id,
                        'identitas' => $mhs->nim,
                        'nama' => $mhs->nama_mahasiswa,
                        'grup' => $mhs->kelas->nama_kelas ?? 'Tanpa Kelas',
                        'pin' => $mhs->id_finger,
                        'total_sesi' => 0,
                        'total_jam_terjadwal' => 0,
                        'total_jam_hadir' => 0,
                        'total_jam_alpa' => 0,
                        'persen_hadir' => 0,
                        'sesi_details' => [],
                    ]);
                    continue;
                }

                if ($sesiMhs->isEmpty()) {
                    continue;
                }

                $jamTerjadwal = 0;
                $jamHadir = 0;
                $sesiDetails = [];

                foreach ($sesiMhs as $sesi) {
                    $durasiJam = max(1, (int)$sesi->jml_jam);
                    $jamTerjadwal += $durasiJam;

                    $jamMasuk = $sesi->jam_masuk;
                    $jamKeluar = $sesi->jam_keluar ?: $sesi->hitungJamKeluar();
                    $jamMasukToleransi = Carbon::parse($jamMasuk)->subMinutes(30)->format('H:i:s');

                    $scan = null;
                    if ($mhs->id_finger) {
                        $scan = AttLog::where('pin', $mhs->id_finger)
                            ->whereDate('scan_date', $tanggal)
                            ->whereTime('scan_date', '>=', $jamMasukToleransi)
                            ->whereTime('scan_date', '<=', $jamKeluar . ':59')
                            ->orderBy('scan_date', 'asc')
                            ->first();
                    }

                    if ($scan) {
                        $jamHadir += $durasiJam;
                        $status = 'H';
                        $waktuScan = Carbon::parse($scan->scan_date)->format('H:i');
                    } else {
                        $status = 'A';
                        $waktuScan = '-';
                    }

                    $sesiDetails[] = [
                        'mk' => $sesi->jadwalHari?->jadwalKelas?->jadwal?->mataKuliah?->nama_mk ?? 'Mata Kuliah',
                        'kegiatan' => $sesi->kegiatan,
                        'jam_masuk' => $jamMasuk,
                        'jam_keluar' => $jamKeluar,
                        'jml_jam' => $durasiJam,
                        'ruangan' => $sesi->ruangan?->nama_ruangan ?? '-',
                        'status' => $status,
                        'scan_time' => $waktuScan,
                    ];
                }

                $jamAlpa = $jamTerjadwal - $jamHadir;
                $persen = $jamTerjadwal > 0 ? round(($jamHadir / $jamTerjadwal) * 100, 1) : 0;

                $totalJamTerjadwalGlobal += $jamTerjadwal;
                $totalJamHadirGlobal += $jamHadir;

                $rekapData->push([
                    'id' => $mhs->id,
                    'identitas' => $mhs->nim,
                    'nama' => $mhs->nama_mahasiswa,
                    'grup' => $mhs->kelas->nama_kelas ?? 'Tanpa Kelas',
                    'pin' => $mhs->id_finger,
                    'total_sesi' => $sesiMhs->count(),
                    'total_jam_terjadwal' => $jamTerjadwal,
                    'total_jam_hadir' => $jamHadir,
                    'total_jam_alpa' => $jamAlpa,
                    'persen_hadir' => $persen,
                    'sesi_details' => $sesiDetails,
                ]);
            }
        } else {
            // TIPE DOSEN
            $dosenQuery = Dosen::with('departemen');

            if (!empty($departemenId)) {
                $dosenQuery->where('departemen_id', $departemenId);
            }

            if (!empty($search)) {
                $dosenQuery->where(function ($q) use ($search) {
                    $q->where('nip', 'like', "%{$search}%")
                      ->orWhere('nama_dosen', 'like', "%{$search}%");
                });
            }

            $dosens = $dosenQuery->orderBy('nama_dosen')->get();

            foreach ($dosens as $dsn) {
                $sesiDsn = $sesiHariList->filter(function ($sesi) use ($dsn) {
                    return $sesi->jadwalDosens->pluck('dosen_id')->contains($dsn->id);
                })->values();

                if ($sesiDsn->isEmpty() && !empty($search)) {
                    $rekapData->push([
                        'id' => $dsn->id,
                        'identitas' => $dsn->nip ?: '-',
                        'nama' => $dsn->nama_lengkap ?? $dsn->nama_dosen,
                        'grup' => $dsn->departemen->nama_departemen ?? 'Tanpa Departemen',
                        'pin' => $dsn->id_finger,
                        'total_sesi' => 0,
                        'total_jam_terjadwal' => 0,
                        'total_jam_hadir' => 0,
                        'total_jam_alpa' => 0,
                        'persen_hadir' => 0,
                        'sesi_details' => [],
                    ]);
                    continue;
                }

                if ($sesiDsn->isEmpty()) {
                    continue;
                }

                $jamTerjadwal = 0;
                $jamHadir = 0;
                $sesiDetails = [];

                foreach ($sesiDsn as $sesi) {
                    $durasiJam = max(1, (int)$sesi->jml_jam);
                    $jamTerjadwal += $durasiJam;

                    $jamMasuk = $sesi->jam_masuk;
                    $jamKeluar = $sesi->jam_keluar ?: $sesi->hitungJamKeluar();
                    $jamMasukToleransi = Carbon::parse($jamMasuk)->subMinutes(30)->format('H:i:s');

                    $scan = null;
                    if ($dsn->id_finger) {
                        $scan = AttLog::where('pin', $dsn->id_finger)
                            ->whereDate('scan_date', $tanggal)
                            ->whereTime('scan_date', '>=', $jamMasukToleransi)
                            ->whereTime('scan_date', '<=', $jamKeluar . ':59')
                            ->orderBy('scan_date', 'asc')
                            ->first();
                    }

                    if ($scan) {
                        $jamHadir += $durasiJam;
                        $status = 'H';
                        $waktuScan = Carbon::parse($scan->scan_date)->format('H:i');
                    } else {
                        $status = 'A';
                        $waktuScan = '-';
                    }

                    $sesiDetails[] = [
                        'mk' => $sesi->jadwalHari?->jadwalKelas?->jadwal?->mataKuliah?->nama_mk ?? 'Mata Kuliah',
                        'kelas' => $sesi->jadwalHari?->jadwalKelas?->kelas?->nama_kelas ?? '-',
                        'kegiatan' => $sesi->kegiatan,
                        'jam_masuk' => $jamMasuk,
                        'jam_keluar' => $jamKeluar,
                        'jml_jam' => $durasiJam,
                        'ruangan' => $sesi->ruangan?->nama_ruangan ?? '-',
                        'status' => $status,
                        'scan_time' => $waktuScan,
                    ];
                }

                $jamAlpa = $jamTerjadwal - $jamHadir;
                $persen = $jamTerjadwal > 0 ? round(($jamHadir / $jamTerjadwal) * 100, 1) : 0;

                $totalJamTerjadwalGlobal += $jamTerjadwal;
                $totalJamHadirGlobal += $jamHadir;

                $rekapData->push([
                    'id' => $dsn->id,
                    'identitas' => $dsn->nip ?: '-',
                    'nama' => $dsn->nama_lengkap ?? $dsn->nama_dosen,
                    'grup' => $dsn->departemen->nama_departemen ?? 'Tanpa Departemen',
                    'pin' => $dsn->id_finger,
                    'total_sesi' => $sesiDsn->count(),
                    'total_jam_terjadwal' => $jamTerjadwal,
                    'total_jam_hadir' => $jamHadir,
                    'total_jam_alpa' => $jamAlpa,
                    'persen_hadir' => $persen,
                    'sesi_details' => $sesiDetails,
                ]);
            }
        }

        $persenGlobal = $totalJamTerjadwalGlobal > 0 ? round(($totalJamHadirGlobal / $totalJamTerjadwalGlobal) * 100, 1) : 0;

        $stats = [
            'total_orang' => $rekapData->count(),
            'total_jam_terjadwal' => $totalJamTerjadwalGlobal,
            'total_jam_hadir' => $totalJamHadirGlobal,
            'total_jam_alpa' => $totalJamTerjadwalGlobal - $totalJamHadirGlobal,
            'persen_global' => $persenGlobal,
        ];

        // Export excel
        if ($request->get('export') === 'excel') {
            return $this->exportExcelHarian($tanggal, $tipe, $rekapData, $stats);
        }

        // Cetak print view
        if ($request->get('print') === '1') {
            return view('laporan.print_harian', compact(
                'tanggal',
                'carbonTgl',
                'tipe',
                'rekapData',
                'stats'
            ));
        }

        return view('laporan.harian', compact(
            'tanggal',
            'carbonTgl',
            'tipe',
            'kelasId',
            'departemenId',
            'search',
            'kelasList',
            'departemenList',
            'rekapData',
            'stats'
        ));
    }

    public function printHarian(Request $request)
    {
        $request->merge(['print' => '1']);
        return $this->harian($request);
    }

    public function excelHarian(Request $request)
    {
        $request->merge(['export' => 'excel']);
        return $this->harian($request);
    }

    protected function exportExcelHarian($tanggal, $tipe, $rekapData, $stats)
    {
        $label = $tipe === 'dosen' ? 'Dosen' : 'Mahasiswa';
        $filename = "Rekap_Jam_Harian_{$label}_{$tanggal}.xls";

        $content = view('laporan.excel_harian', compact('tanggal', 'tipe', 'rekapData', 'stats'))->render();

        return response($content, 200, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    protected function exportExcelMahasiswa($jadwal, $jadwalKelas, $sesiList, $laporanData)
    {
        $filename = 'Laporan_Absen_Mahasiswa_' . str_replace(' ', '_', $jadwal->mataKuliah->nama_mk) . '_' . str_replace(' ', '_', $jadwalKelas->kelas->nama_kelas) . '.xls';

        $content = view('laporan.excel_mahasiswa', compact('jadwal', 'jadwalKelas', 'sesiList', 'laporanData'))->render();

        return response($content, 200, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    protected function exportExcelDosen($dosen, $semester, $thnAjaran, $laporanData)
    {
        $filename = 'Laporan_Absen_Dosen_' . str_replace(' ', '_', $dosen->nama_dosen) . '.xls';

        $content = view('laporan.excel_dosen', compact('dosen', 'semester', 'thnAjaran', 'laporanData'))->render();

        return response($content, 200, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    protected function exportExcelRekapDosen($rekapDosen, $semester, $thnAjaran)
    {
        $filename = 'Laporan_Rekap_Presensi_Dosen_' . date('Ymd_His') . '.xls';

        $content = view('laporan.excel_rekap_dosen', compact('rekapDosen', 'semester', 'thnAjaran'))->render();

        return response($content, 200, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
