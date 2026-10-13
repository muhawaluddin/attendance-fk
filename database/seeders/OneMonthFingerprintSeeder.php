<?php

namespace Database\Seeders;

use App\Models\AttLog;
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
use App\Models\Mesin;
use App\Models\Pengaturan;
use App\Models\Ruangan;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Seeder;

class OneMonthFingerprintSeeder extends Seeder
{
    /**
     * Run the database seeds to populate 1 month of realistic fingerprint attendance data.
     */
    public function run(): void
    {
        $today = Carbon::today();
        $startDate = $today->copy()->subDays(30);

        // Ambil data referensi master
        $pengaturan = Pengaturan::where('is_active', true)->first() ?? Pengaturan::first();
        $dosens = Dosen::whereNotNull('id_finger')->get();
        $mahasiswas = Mahasiswa::whereNotNull('id_finger')->get();
        $kelasList = Kelas::all();
        $ruangans = Ruangan::all();
        $mesins = Mesin::all();
        $mataKuliahs = MataKuliah::all();
        $departemens = Departemen::all();

        if ($dosens->isEmpty() || $mahasiswas->isEmpty() || $mesins->isEmpty()) {
            $this->command?->warn('Data master dosen, mahasiswa, atau mesin belum lengkap. Jalankan DatabaseSeeder terlebih dahulu.');
            return;
        }

        // Mapping Mesin per Ruangan
        $mesinRuanganMap = [];
        foreach ($mesins as $m) {
            if ($m->ruangan_id) {
                $mesinRuanganMap[$m->ruangan_id] = $m;
            }
        }

        $defaultMesin = $mesins->first();

        // 1. Pastikan ada Jadwal Blok Kuliah yang mencakup rentang 30 hari terakhir
        $mkKardio = $mataKuliahs->firstWhere('kode_mk', 'KARDIO-301') ?? $mataKuliahs->first();
        $mkRespirasi = $mataKuliahs->firstWhere('kode_mk', 'RESPIRASI-302') ?? ($mataKuliahs->skip(1)->first() ?? $mkKardio);

        // Buat atau perbarui Jadwal Kardiovaskular
        $jadwalKardio = Jadwal::firstOrCreate([
            'semester' => 'Ganjil',
            'thn_ajaran' => '2025/2026',
            'mata_kuliah_id' => $mkKardio->id,
        ], [
            'tgl_mulai' => $startDate->toDateString(),
            'tgl_selesai' => $today->copy()->addWeeks(2)->toDateString(),
            'dosen_koordinator_id' => $dosens->first()->id,
            'dosen_sekretaris_id' => $dosens->skip(1)->first()?->id ?? $dosens->first()->id,
            'pengaturan_id' => $pengaturan?->id,
            'status' => 'aktif',
        ]);

        // Hubungkan Jadwal Kardiovaskular ke Kelas A & Kelas B
        $jadwalKelasList = [];
        foreach ($kelasList as $kelas) {
            $jk = JadwalKelas::firstOrCreate([
                'jadwal_id' => $jadwalKardio->id,
                'kelas_id' => $kelas->id,
            ]);
            $jadwalKelasList[$kelas->id] = $jk;

            // Daftarkan mahasiswa ke Jadwal Kelas
            $mhsInKelas = $mahasiswas->where('kelas_id', $kelas->id);
            foreach ($mhsInKelas as $mhs) {
                JadwalMahasiswa::firstOrCreate([
                    'jadwal_kelas_id' => $jk->id,
                    'mahasiswa_id' => $mhs->id,
                ]);
            }
        }

        $ruangK1 = $ruangans->firstWhere('kode_ruangan', 'RK-201') ?? $ruangans->first();
        $ruangK2 = $ruangans->firstWhere('kode_ruangan', 'RK-202') ?? ($ruangans->skip(1)->first() ?? $ruangK1);
        $ruangCSL = $ruangans->firstWhere('kode_ruangan', 'CSL-1') ?? $ruangK2;

        $deptIPD = $departemens->firstWhere('kode_departemen', 'IPD') ?? $departemens->first();
        $deptBedah = $departemens->firstWhere('kode_departemen', 'BEDAH') ?? ($departemens->skip(1)->first() ?? $deptIPD);

        // 2. Loop setiap hari dari 30 hari yang lalu hingga hari ini
        $period = CarbonPeriod::create($startDate, $today);
        $totalLogsCreated = 0;

        foreach ($period as $date) {
            // Lewati hari Sabtu dan Minggu (perkuliahan aktif Senin - Jumat)
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

            // Untuk setiap kelas aktif (Kelas A dan Kelas B)
            foreach ($jadwalKelasList as $kelasId => $jk) {
                $jHari = JadwalHari::firstOrCreate([
                    'jadwal_kelas_id' => $jk->id,
                    'tanggal' => $date->toDateString(),
                ], [
                    'hari' => $namaHari,
                ]);

                // Ruangan tergantung kelas
                $ruanganSesi1 = ($kelasId == 1) ? $ruangK1 : $ruangK2;
                $ruanganSesi2 = ($kelasId == 1) ? $ruangCSL : $ruangK1;

                $mesinSesi1 = $mesinRuanganMap[$ruanganSesi1->id] ?? $defaultMesin;
                $mesinSesi2 = $mesinRuanganMap[$ruanganSesi2->id] ?? $defaultMesin;

                // Tentukan Dosen Pengajar bergilir
                $dosenIndex1 = ($date->dayOfYear + $kelasId) % $dosens->count();
                $dosenIndex2 = ($date->dayOfYear + $kelasId + 1) % $dosens->count();
                $dosen1 = $dosens[$dosenIndex1];
                $dosen2 = $dosens[$dosenIndex2];

                // Sesi 1: Kuliah Pagi (08:00 - 09:40, 2 Jam)
                $jJam1 = JadwalJam::firstOrCreate([
                    'jadwal_hari_id' => $jHari->id,
                    'jam_masuk' => '08:00',
                ], [
                    'ruangan_id' => $ruanganSesi1->id,
                    'departemen_id' => $deptIPD?->id,
                    'jml_jam' => 2,
                    'jam_keluar' => '09:40',
                    'kegiatan' => 'Kuliah Tatap Muka: Patofisiologi & Diagnosis Klinis (' . $namaHari . ')',
                ]);

                JadwalDosen::firstOrCreate([
                    'jadwal_jam_id' => $jJam1->id,
                    'dosen_id' => $dosen1->id,
                ]);

                // Sesi 2: Tutorial / Praktikum Siang (10:00 - 11:40, 2 Jam)
                $jJam2 = JadwalJam::firstOrCreate([
                    'jadwal_hari_id' => $jHari->id,
                    'jam_masuk' => '10:00',
                ], [
                    'ruangan_id' => $ruanganSesi2->id,
                    'departemen_id' => $deptBedah?->id,
                    'jml_jam' => 2,
                    'jam_keluar' => '11:40',
                    'kegiatan' => 'Praktikum & Diskusi Kasus Terbimbing (' . $namaHari . ')',
                ]);

                JadwalDosen::firstOrCreate([
                    'jadwal_jam_id' => $jJam2->id,
                    'dosen_id' => $dosen2->id,
                ]);

                // Sesi 3 (Khusus Senin & Kamis): Kuliah Siang (13:00 - 14:40, 2 Jam)
                if (in_array($date->dayOfWeek, [1, 4])) {
                    $dosen3 = $dosens[($date->dayOfYear + 2) % $dosens->count()];
                    $jJam3 = JadwalJam::firstOrCreate([
                        'jadwal_hari_id' => $jHari->id,
                        'jam_masuk' => '13:00',
                    ], [
                        'ruangan_id' => $ruanganSesi1->id,
                        'departemen_id' => $deptIPD?->id,
                        'jml_jam' => 2,
                        'jam_keluar' => '14:40',
                        'kegiatan' => 'Pleno Hasil Diskusi Kelompok & Farmakoterapi',
                    ]);

                    JadwalDosen::firstOrCreate([
                        'jadwal_jam_id' => $jJam3->id,
                        'dosen_id' => $dosen3->id,
                    ]);
                }

                // ==========================================
                // GENERATE ATT_LOG FINGERPRINT UNTUK TANGGAL INI
                // ==========================================

                // A. LOG DOSEN 1 (Sesi 1 Jam 08:00)
                // 95% Dosen hadir tepat waktu (07:45 - 07:58)
                if (rand(1, 100) <= 95) {
                    $menitDosen = rand(45, 58);
                    $detikDosen = rand(5, 55);
                    $scanDateDosen1 = $date->copy()->setTime(7, $menitDosen, $detikDosen);

                    AttLog::firstOrCreate([
                        'sn' => $mesinSesi1->serial,
                        'pin' => $dosen1->id_finger,
                        'scan_date' => $scanDateDosen1,
                    ], [
                        'scan_time' => sprintf('07:%02d:%02d', $menitDosen, $detikDosen),
                        'verified' => 1,
                        'status_scan' => 0,
                        'ruangan_id' => $ruanganSesi1->id,
                        'tipe_pengguna' => 'dosen',
                    ]);
                    $totalLogsCreated++;
                }

                // B. LOG DOSEN 2 (Sesi 2 Jam 10:00)
                // 90% Dosen hadir (09:45 - 09:59)
                if (rand(1, 100) <= 90) {
                    $menitDosen2 = rand(46, 59);
                    $detikDosen2 = rand(5, 55);
                    $scanDateDosen2 = $date->copy()->setTime(9, $menitDosen2, $detikDosen2);

                    AttLog::firstOrCreate([
                        'sn' => $mesinSesi2->serial,
                        'pin' => $dosen2->id_finger,
                        'scan_date' => $scanDateDosen2,
                    ], [
                        'scan_time' => sprintf('09:%02d:%02d', $menitDosen2, $detikDosen2),
                        'verified' => 1,
                        'status_scan' => 0,
                        'ruangan_id' => $ruanganSesi2->id,
                        'tipe_pengguna' => 'dosen',
                    ]);
                    $totalLogsCreated++;
                }

                // C. LOG MAHASISWA KELAS INI
                $mhsKelas = $mahasiswas->where('kelas_id', $kelasId);

                foreach ($mhsKelas as $mhs) {
                    // Sesi 1: 90% Mahasiswa tap masuk (antara 07:40 - 08:10)
                    if (rand(1, 100) <= 90) {
                        $isEarly = rand(1, 100) <= 75; // 75% datang sebelum jam 8
                        if ($isEarly) {
                            $m = rand(45, 59);
                            $scanDateMhs = $date->copy()->setTime(7, $m, rand(1, 59));
                            $scanTimeStr = sprintf('07:%02d:%02d', $m, rand(1, 59));
                        } else {
                            $m = rand(1, 10);
                            $scanDateMhs = $date->copy()->setTime(8, $m, rand(1, 59));
                            $scanTimeStr = sprintf('08:%02d:%02d', $m, rand(1, 59));
                        }

                        AttLog::firstOrCreate([
                            'sn' => $mesinSesi1->serial,
                            'pin' => $mhs->id_finger,
                            'scan_date' => $scanDateMhs,
                        ], [
                            'scan_time' => $scanTimeStr,
                            'verified' => 1,
                            'status_scan' => 0,
                            'ruangan_id' => $ruanganSesi1->id,
                            'tipe_pengguna' => 'mahasiswa',
                        ]);
                        $totalLogsCreated++;
                    }

                    // Sesi 2: 85% Mahasiswa tap masuk di sesi siang (09:45 - 10:10)
                    if (rand(1, 100) <= 85) {
                        $isBefore = rand(1, 100) <= 70;
                        if ($isBefore) {
                            $m = rand(48, 59);
                            $scanDateMhs2 = $date->copy()->setTime(9, $m, rand(1, 59));
                            $scanTimeStr2 = sprintf('09:%02d:%02d', $m, rand(1, 59));
                        } else {
                            $m = rand(1, 8);
                            $scanDateMhs2 = $date->copy()->setTime(10, $m, rand(1, 59));
                            $scanTimeStr2 = sprintf('10:%02d:%02d', $m, rand(1, 59));
                        }

                        AttLog::firstOrCreate([
                            'sn' => $mesinSesi2->serial,
                            'pin' => $mhs->id_finger,
                            'scan_date' => $scanDateMhs2,
                        ], [
                            'scan_time' => $scanTimeStr2,
                            'verified' => 1,
                            'status_scan' => 0,
                            'ruangan_id' => $ruanganSesi2->id,
                            'tipe_pengguna' => 'mahasiswa',
                        ]);
                        $totalLogsCreated++;
                    }
                }
            }
        }

        $this->command?->info("Berhasil membuat data absensi fingerprint dummy selama 1 bulan ({$startDate->format('d/m/Y')} s/d {$today->format('d/m/Y')})!");
    }
}
