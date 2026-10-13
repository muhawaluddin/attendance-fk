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
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Users
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator ICT FKUH',
                'email' => 'admin@med.unhas.ac.id',
                'password' => Hash::make('password'),
                'level' => 'admin',
                'status' => true,
            ]
        );

        User::updateOrCreate(
            ['username' => 'ict'],
            [
                'name' => 'Tim ICT FK UNHAS',
                'email' => 'prodis1_fkuh@gmail.com',
                'password' => Hash::make('password'),
                'level' => 'operator',
                'status' => true,
            ]
        );

        // 2. Pengaturan
        $pengaturan = Pengaturan::updateOrCreate(
            ['id' => 1],
            [
                'nama_pengaturan' => 'Standar Perkuliahan FKUH',
                'menit_perjam' => 50,
                'menit_pergantian' => 10,
                'jam_kuliah' => '08:00',
                'jam_mulai_istirahat' => '12:00',
                'jam_selesai_istirahat' => '13:00',
                'jam_perhari' => 8,
                'is_active' => true,
            ]
        );

        // 3. Departemen
        $deptList = [
            ['kode' => 'IPD', 'nama' => 'Ilmu Penyakit Dalam'],
            ['kode' => 'BEDAH', 'nama' => 'Ilmu Bedah'],
            ['kode' => 'PEDIATRI', 'nama' => 'Ilmu Kesehatan Anak'],
            ['kode' => 'OBGYN', 'nama' => 'Obstetri dan Ginekologi'],
            ['kode' => 'ANATOMI', 'nama' => 'Anatomi dan Histologi'],
            ['kode' => 'FARMA', 'nama' => 'Farmakologi dan Terapi'],
            ['kode' => 'FISIO', 'nama' => 'Fisiologi Kedokteran'],
        ];

        $deptMap = [];
        foreach ($deptList as $d) {
            $dept = Departemen::firstOrCreate(
                ['nama_departemen' => $d['nama']],
                ['kode_departemen' => $d['kode']]
            );
            $deptMap[$d['kode']] = $dept->id;
        }

        // 4. Kelas
        $kelasList = [
            ['nama' => 'Kelas A', 'angkatan' => '2025'],
            ['nama' => 'Kelas B', 'angkatan' => '2025'],
            ['nama' => 'Kelas Internasional', 'angkatan' => '2025'],
        ];
        $kelasMap = [];
        foreach ($kelasList as $k) {
            $kelas = Kelas::firstOrCreate(['nama_kelas' => $k['nama']], ['angkatan' => $k['angkatan']]);
            $kelasMap[$k['nama']] = $kelas->id;
        }

        // 5. Ruangan
        $ruanganList = [
            ['kode' => 'RK-201', 'nama' => 'Ruang Kuliah 1 LT.2', 'kapasitas' => 120, 'lokasi' => 'Gedung Pendidikan LT.2'],
            ['kode' => 'RK-202', 'nama' => 'Ruang Kuliah 2 LT.2', 'kapasitas' => 120, 'lokasi' => 'Gedung Pendidikan LT.2'],
            ['kode' => 'RK-301', 'nama' => 'Ruang Kuliah 3 LT.3', 'kapasitas' => 100, 'lokasi' => 'Gedung Pendidikan LT.3'],
            ['kode' => 'CSL-1', 'nama' => 'Ruang CSL Gedung Baru', 'kapasitas' => 60, 'lokasi' => 'Gedung CSL LT.1'],
            ['kode' => 'AMPHI', 'nama' => 'Amphitheater Prof. Syarifuddin', 'kapasitas' => 250, 'lokasi' => 'Gedung Utama'],
        ];
        $ruanganMap = [];
        foreach ($ruanganList as $r) {
            $ruang = Ruangan::firstOrCreate(['nama_ruangan' => $r['nama']], [
                'kode_ruangan' => $r['kode'],
                'kapasitas' => $r['kapasitas'],
                'lokasi' => $r['lokasi'],
            ]);
            $ruanganMap[$r['nama']] = $ruang->id;
        }

        // 6. Mesin Fingerprint
        $mesinList = [
            ['serial' => 'SN-FKUH-001', 'ruangan_id' => $ruanganMap['Ruang Kuliah 1 LT.2'], 'ip' => '192.168.1.201', 'kode' => 'RK1'],
            ['serial' => 'SN-FKUH-002', 'ruangan_id' => $ruanganMap['Ruang Kuliah 2 LT.2'], 'ip' => '192.168.1.202', 'kode' => 'RK2'],
            ['serial' => 'SN-FKUH-003', 'ruangan_id' => $ruanganMap['Ruang CSL Gedung Baru'], 'ip' => '192.168.1.203', 'kode' => 'CSL'],
            ['serial' => 'SN-FKUH-004', 'ruangan_id' => $ruanganMap['Amphitheater Prof. Syarifuddin'], 'ip' => '192.168.1.204', 'kode' => 'AMPHI'],
        ];
        foreach ($mesinList as $m) {
            Mesin::firstOrCreate(['serial' => $m['serial']], [
                'activation' => 'ACT-' . strtoupper(substr(md5($m['serial']), 0, 8)),
                'kode' => $m['kode'],
                'no_mesin' => '1',
                'ip_address' => $m['ip'],
                'port' => '4370',
                'ruangan_id' => $m['ruangan_id'],
                'status_online' => true,
                'last_synced_at' => now(),
            ]);
        }

        // 7. Mata Kuliah / Blok
        $mkList = [
            ['kode' => 'BBM-101', 'nama' => 'Biomedik Dasar', 'sks' => 4, 'smt' => 'Ganjil'],
            ['kode' => 'BKD-201', 'nama' => 'Kardiovaskular', 'sks' => 4, 'smt' => 'Ganjil'],
            ['kode' => 'BRS-202', 'nama' => 'Sistem Respirasi', 'sks' => 4, 'smt' => 'Ganjil'],
            ['kode' => 'BGI-203', 'nama' => 'Gastrointestinal & Hepatobilier', 'sks' => 4, 'smt' => 'Genap'],
            ['kode' => 'BMS-204', 'nama' => 'Muskuloskeletal & Traumatologi', 'sks' => 4, 'smt' => 'Genap'],
        ];
        $mkMap = [];
        foreach ($mkList as $mk) {
            $item = MataKuliah::firstOrCreate(['nama_mk' => $mk['nama']], [
                'kode_mk' => $mk['kode'],
                'sks' => $mk['sks'],
                'semester' => $mk['smt'],
            ]);
            $mkMap[$mk['nama']] = $item->id;
        }

        // 8. Dosen
        $dosenList = [
            ['nip' => '196805121995032001', 'nama' => 'Haerani Rasyid', 'depan' => 'Prof. Dr. dr.', 'belakang' => 'M.Kes, Sp.PD-KGH', 'finger' => '1001', 'dept' => $deptMap['IPD']],
            ['nip' => '197003151998021002', 'nama' => 'Wardihan Sinrang', 'depan' => 'Dr. dr.', 'belakang' => 'M.S., Sp.B', 'finger' => '1002', 'dept' => $deptMap['BEDAH']],
            ['nip' => '197508202002121003', 'nama' => 'Firdaus Hamid', 'depan' => 'dr.', 'belakang' => 'Ph.D, Sp.MK', 'finger' => '1003', 'dept' => $deptMap['ANATOMI']],
            ['nip' => '197211102000032004', 'nama' => 'Maisuri T. Chalid', 'depan' => 'Dr. dr.', 'belakang' => 'Sp.OG(K)', 'finger' => '1004', 'dept' => $deptMap['OBGYN']],
            ['nip' => '197804052005012005', 'nama' => 'Rina Masadah', 'depan' => 'Dr. dr.', 'belakang' => 'M.Phil, Sp.PA(K)', 'finger' => '1005', 'dept' => $deptMap['FARMA']],
        ];
        $dosenMap = [];
        foreach ($dosenList as $d) {
            $dosen = Dosen::firstOrCreate(['nip' => $d['nip']], [
                'nama_dosen' => $d['nama'],
                'gelar_depan' => $d['depan'],
                'gelar_belakang' => $d['belakang'],
                'id_finger' => $d['finger'],
                'departemen_id' => $d['dept'],
                'email' => strtolower(str_replace(' ', '', $d['nama'])) . '@med.unhas.ac.id',
                'no_hp' => '0812' . rand(10000000, 99999999),
            ]);
            $dosenMap[$d['nama']] = $dosen->id;
        }

        // 9. Mahasiswa
        $mhsList = [
            ['nim' => 'C011231001', 'nama' => 'Andi Muhammad Caesar', 'finger' => '2001', 'kelas' => $kelasMap['Kelas A']],
            ['nim' => 'C011231002', 'nama' => 'Nurul Ainun Putri', 'finger' => '2002', 'kelas' => $kelasMap['Kelas A']],
            ['nim' => 'C011231003', 'nama' => 'Fathur Rahman Baso', 'finger' => '2003', 'kelas' => $kelasMap['Kelas A']],
            ['nim' => 'C011231004', 'nama' => 'Siti Khadijah Amalia', 'finger' => '2004', 'kelas' => $kelasMap['Kelas A']],
            ['nim' => 'C011231005', 'nama' => 'Ahmad Rezki Pratama', 'finger' => '2005', 'kelas' => $kelasMap['Kelas A']],
            ['nim' => 'C011231006', 'nama' => 'Tri Wahyuni Lestari', 'finger' => '2006', 'kelas' => $kelasMap['Kelas B']],
            ['nim' => 'C011231007', 'nama' => 'Muh. Daffa Al-Faruq', 'finger' => '2007', 'kelas' => $kelasMap['Kelas B']],
            ['nim' => 'C011231008', 'nama' => 'Gabriella Angeline Tan', 'finger' => '2008', 'kelas' => $kelasMap['Kelas Internasional']],
            ['nim' => 'C011231009', 'nama' => 'Kenzo Alexander Putra', 'finger' => '2009', 'kelas' => $kelasMap['Kelas Internasional']],
        ];
        $mhsObjects = [];
        foreach ($mhsList as $m) {
            $mhs = Mahasiswa::firstOrCreate(['nim' => $m['nim']], [
                'nama_mahasiswa' => $m['nama'],
                'id_finger' => $m['finger'],
                'departemen_id' => $deptMap['IPD'],
                'kelas_id' => $m['kelas'],
                'angkatan' => '2023',
                'no_hp' => '0821' . rand(10000000, 99999999),
                'jenis_kelamin' => rand(0, 1) ? 'L' : 'P',
                'status_aktif' => true,
            ]);
            $mhsObjects[] = $mhs;
        }

        // 10. Jadwal Kuliah Aktif
        $today = Carbon::today();
        $startDate = $today->copy()->startOfWeek();
        $endDate = $today->copy()->addWeeks(2)->endOfWeek();

        $jadwal = Jadwal::firstOrCreate(
            [
                'semester' => 'Ganjil',
                'thn_ajaran' => '2025/2026',
                'mata_kuliah_id' => $mkMap['Kardiovaskular'],
            ],
            [
                'tgl_mulai' => $startDate,
                'tgl_selesai' => $endDate,
                'dosen_koordinator_id' => $dosenMap['Haerani Rasyid'],
                'dosen_sekretaris_id' => $dosenMap['Firdaus Hamid'],
                'pengaturan_id' => $pengaturan->id,
                'status' => 'aktif',
            ]
        );

        // Hubungkan Jadwal dengan Kelas A
        $jadwalKelas = JadwalKelas::firstOrCreate([
            'jadwal_id' => $jadwal->id,
            'kelas_id' => $kelasMap['Kelas A'],
        ]);

        // Hubungkan Mahasiswa Kelas A ke Jadwal
        foreach ($mhsObjects as $mhs) {
            if ($mhs->kelas_id === $kelasMap['Kelas A']) {
                JadwalMahasiswa::firstOrCreate([
                    'jadwal_kelas_id' => $jadwalKelas->id,
                    'mahasiswa_id' => $mhs->id,
                ]);
            }
        }

        // Generate Hari Perkuliahan (Senin s/d Jumat)
        $period = CarbonPeriod::create($startDate, $endDate);
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

            $jHari = JadwalHari::firstOrCreate([
                'jadwal_kelas_id' => $jadwalKelas->id,
                'tanggal' => $date->toDateString(),
            ], [
                'hari' => $namaHari,
            ]);

            // Slot 1: Kuliah Pengantar Pagi (08:00 - 09:40)
            $jJam1 = JadwalJam::firstOrCreate([
                'jadwal_hari_id' => $jHari->id,
                'jam_masuk' => '08:00',
            ], [
                'ruangan_id' => $ruanganMap['Ruang Kuliah 1 LT.2'],
                'departemen_id' => $deptMap['IPD'],
                'jml_jam' => 2,
                'jam_keluar' => '09:40',
                'kegiatan' => 'Kuliah Besar: Anatomi dan Fisiologi Sistem Kardiovaskular',
            ]);

            JadwalDosen::firstOrCreate([
                'jadwal_jam_id' => $jJam1->id,
                'dosen_id' => $dosenMap['Haerani Rasyid'],
            ]);

            // Slot 2: Tutorial / CSL Siang (10:00 - 11:40)
            $jJam2 = JadwalJam::firstOrCreate([
                'jadwal_hari_id' => $jHari->id,
                'jam_masuk' => '10:00',
            ], [
                'ruangan_id' => $ruanganMap['Ruang CSL Gedung Baru'],
                'departemen_id' => $deptMap['IPD'],
                'jml_jam' => 2,
                'jam_keluar' => '11:40',
                'kegiatan' => 'CSL & Praktikum: Pemeriksaan Tekanan Darah dan Auskultasi Jantung',
            ]);

            JadwalDosen::firstOrCreate([
                'jadwal_jam_id' => $jJam2->id,
                'dosen_id' => $dosenMap['Firdaus Hamid'],
            ]);

            // Sample Log Kehadiran Fingerprint untuk hari ini dan hari-hari sebelumnya
            if ($date->lessThanOrEqualTo($today)) {
                // Log Dosen Haerani tap jam 07:55
                AttLog::firstOrCreate([
                    'sn' => 'SN-FKUH-001',
                    'pin' => '1001',
                    'scan_date' => $date->copy()->setTime(7, 55, 12),
                ], [
                    'scan_time' => '07:55:12',
                    'verified' => 1,
                    'status_scan' => 0,
                    'ruangan_id' => $ruanganMap['Ruang Kuliah 1 LT.2'],
                    'tipe_pengguna' => 'dosen',
                ]);

                // Log Mahasiswa Kelas A
                $counter = 0;
                foreach ($mhsObjects as $mhs) {
                    if ($mhs->kelas_id === $kelasMap['Kelas A']) {
                        $counter++;
                        // 80% hadir tepat waktu
                        if ($counter <= 4) {
                            $menit = rand(50, 58);
                            AttLog::firstOrCreate([
                                'sn' => 'SN-FKUH-001',
                                'pin' => $mhs->id_finger,
                                'scan_date' => $date->copy()->setTime(7, $menit, rand(10, 50)),
                            ], [
                                'scan_time' => sprintf('07:%02d:00', $menit),
                                'verified' => 1,
                                'status_scan' => 0,
                                'ruangan_id' => $ruanganMap['Ruang Kuliah 1 LT.2'],
                                'tipe_pengguna' => 'mahasiswa',
                            ]);
                        }
                    }
                }
            }
        }

        // Panggil Seeder data fingerprint 1 bulan ke belakang
        $this->call(OneMonthFingerprintSeeder::class);
    }
}
