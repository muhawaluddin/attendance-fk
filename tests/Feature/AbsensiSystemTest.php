<?php

namespace Tests\Feature;

use App\Models\Departemen;
use App\Models\Dosen;
use App\Models\Jadwal;
use App\Models\JadwalKelas;
use App\Models\Kelas;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Mesin;
use App\Models\Pengaturan;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AbsensiSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->user = User::where('username', 'admin')->first();
    }

    public function test_guest_is_redirected_to_login()
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_user_can_view_login_page()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('ABSENSI');
    }

    public function test_user_can_login_successfully()
    {
        $response = $this->post('/login', [
            'username' => 'admin',
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($this->user);
    }

    public function test_dashboard_page_loads_with_data()
    {
        $response = $this->actingAs($this->user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard Beranda');
        $response->assertSee('Jadwal Kegiatan Hari Ini');
    }

    public function test_public_monitoring_screen_works()
    {
        $response = $this->get('/monitoring');
        $response->assertStatus(200);
        $response->assertSee('JADWAL KEGIATAN PERKULIAHAN');
    }

    public function test_master_data_pages_render()
    {
        $routes = [
            'departemen.index',
            'kelas.index',
            'dosen.index',
            'mahasiswa.index',
            'mata-kuliah.index',
            'ruangan.index',
            'mesin.index',
            'pengaturan.index',
            'akun.index',
            'jadwal.index',
        ];

        foreach ($routes as $routeName) {
            $response = $this->actingAs($this->user)->get(route($routeName));
            $response->assertStatus(200);
        }
    }

    public function test_different_fingerprint_models_can_be_registered()
    {
        $ruangan = Ruangan::first();

        // 1. ZKTeco Model K40
        $responseZk = $this->actingAs($this->user)->post(route('mesin.store'), [
            'brand_mesin' => 'ZKTeco',
            'model_tipe' => 'K40 Standalone',
            'serial' => 'ZK-K40-TEST-001',
            'nama_mesin' => 'Fingerprint ZKTeco K40 R.01',
            'tipe_koneksi' => 'standalone_tcp',
            'ip_address' => '192.168.1.210',
            'port' => '4370',
            'comm_key' => '0',
            'ruangan_id' => $ruangan->id,
            'metode_verifikasi' => ['fingerprint', 'rfid', 'pin'],
        ]);
        $responseZk->assertRedirect(route('mesin.index'));
        $this->assertDatabaseHas('mesins', [
            'brand_mesin' => 'ZKTeco',
            'model_tipe' => 'K40 Standalone',
            'serial' => 'ZK-K40-TEST-001',
        ]);

        // 2. Solution Model X100-C
        $responseSolution = $this->actingAs($this->user)->post(route('mesin.store'), [
            'brand_mesin' => 'Solution',
            'model_tipe' => 'X100-C Color TFT',
            'serial' => 'SOL-X100C-TEST-002',
            'nama_mesin' => 'Solution Fingerprint X100C',
            'tipe_koneksi' => 'cloud_adms',
            'ip_address' => '192.168.1.211',
            'port' => '4370',
            'comm_key' => '0',
            'ruangan_id' => $ruangan->id,
            'metode_verifikasi' => ['fingerprint', 'pin'],
        ]);
        $responseSolution->assertRedirect(route('mesin.index'));
        $this->assertDatabaseHas('mesins', [
            'brand_mesin' => 'Solution',
            'model_tipe' => 'X100-C Color TFT',
            'serial' => 'SOL-X100C-TEST-002',
        ]);

        // 3. Fingerspot Model Revo-180
        $responseFingerspot = $this->actingAs($this->user)->post(route('mesin.store'), [
            'brand_mesin' => 'Fingerspot',
            'model_tipe' => 'Revo-180 Cloud',
            'serial' => 'FS-REVO-TEST-003',
            'nama_mesin' => 'Fingerspot Revo 180',
            'tipe_koneksi' => 'webhook_api',
            'ip_address' => '192.168.1.212',
            'port' => '4370',
            'ruangan_id' => $ruangan->id,
        ]);
        $responseFingerspot->assertRedirect(route('mesin.index'));
        $this->assertDatabaseHas('mesins', [
            'brand_mesin' => 'Fingerspot',
            'model_tipe' => 'Revo-180 Cloud',
        ]);

        // 4. Hikvision Face & Fingerprint DS-K1T Series
        $responseHik = $this->actingAs($this->user)->post(route('mesin.store'), [
            'brand_mesin' => 'Hikvision',
            'model_tipe' => 'DS-K1T804AMF',
            'serial' => 'HIK-K1T-TEST-004',
            'nama_mesin' => 'Hikvision Terminal Biometrik',
            'tipe_koneksi' => 'standalone_tcp',
            'ip_address' => '192.168.1.213',
            'port' => '8000',
            'ruangan_id' => $ruangan->id,
            'metode_verifikasi' => ['fingerprint', 'face', 'rfid'],
        ]);
        $responseHik->assertRedirect(route('mesin.index'));
        $this->assertDatabaseHas('mesins', [
            'brand_mesin' => 'Hikvision',
            'model_tipe' => 'DS-K1T804AMF',
        ]);
    }

    public function test_branding_can_be_customized_to_umb_palopo_and_theme_color_changes()
    {
        $response = $this->actingAs($this->user)->post(route('pengaturan.branding'), [
            'institution_name' => 'Universitas Mega Buana Palopo',
            'short_name' => 'UMB PALOPO',
            'faculty_name' => 'Fakultas Kesehatan',
            'app_name' => 'SIM PRESENSI',
            'website_url' => 'www.umbpalopo.ac.id',
            'email_contact' => 'info@umbpalopo.ac.id',
            'running_text' => 'Selamat datang di Sistem Absensi Mahasiswa UMB Palopo.',
            'theme_color' => 'umb-blue',
            'primary_color_hex' => '#1d4ed8',
        ]);

        $response->assertRedirect(route('pengaturan.index'));
        $response->assertSessionHas('success');

        // Pastikan di database tersimpan UMB Palopo dan tema umb-blue
        $this->assertDatabaseHas('pengaturans', [
            'institution_name' => 'Universitas Mega Buana Palopo',
            'short_name' => 'UMB PALOPO',
            'theme_color' => 'umb-blue',
            'primary_color_hex' => '#1d4ed8',
        ]);

        // Verifikasi pada dashboard tampil UMB PALOPO
        $dashboardRes = $this->actingAs($this->user)->get('/dashboard');
        $dashboardRes->assertSee('Universitas Mega Buana Palopo');
        $dashboardRes->assertSee('UMB PALOPO');

        // Verifikasi pada layar monitoring TV juga tampil UMB Palopo
        $monitoringRes = $this->get('/monitoring');
        $monitoringRes->assertSee('Universitas Mega Buana Palopo');
        $monitoringRes->assertSee('www.umbpalopo.ac.id');
        $monitoringRes->assertSee('Selamat datang di Sistem Absensi Mahasiswa UMB Palopo.');
    }

    public function test_fingerprint_push_webhook_api_receives_attendance_log()
    {
        $mhs = Mahasiswa::first();
        $mesin = Mesin::first();

        $payload = [
            'sn' => $mesin->serial,
            'pin' => $mhs->id_finger,
            'scan_date' => now()->format('Y-m-d H:i:s'),
        ];

        $response = $this->postJson('/api/fingerprint/push', $payload);
        $response->assertStatus(201);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Attendance log recorded',
        ]);

        $this->assertDatabaseHas('att_logs', [
            'sn' => $mesin->serial,
            'pin' => $mhs->id_finger,
        ]);
    }

    public function test_mesin_test_connection_route()
    {
        $mesin = Mesin::first();

        $response = $this->actingAs($this->user)->post(route('mesin.test-connection', $mesin));
        $response->assertRedirect();
        // Socket ping ke local dummy IP akan menghasilkan response redirect (baik success ataupun warning timeout)
        $this->assertTrue(session()->has('success') || session()->has('error'));
    }

    public function test_laporan_mahasiswa_and_dosen_render()
    {
        $jadwal = Jadwal::first();
        $jadwalKelas = JadwalKelas::first();

        $response = $this->actingAs($this->user)->get(route('laporan.mahasiswa', [
            'jadwal_id' => $jadwal->id,
            'jadwal_kelas_id' => $jadwalKelas->id,
        ]));
        $response->assertStatus(200);
        $response->assertSee('Laporan Rekapitulasi Absensi Mahasiswa');

        $dosen = Dosen::first();
        $responseDosen = $this->actingAs($this->user)->get(route('laporan.dosen', [
            'dosen_id' => $dosen->id,
        ]));
        $responseDosen->assertStatus(200);
        $responseDosen->assertSee('Laporan Rekapitulasi Presensi Dosen');
    }

    public function test_mesin_simulate_tap_works()
    {
        $mesin = Mesin::first();
        $mhs = Mahasiswa::first();

        $response = $this->actingAs($this->user)->post(route('mesin.simulate-tap'), [
            'mesin_id' => $mesin->id,
            'pin' => $mhs->id_finger,
            'tipe_pengguna' => 'mahasiswa',
            'scan_time' => '08:05',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('att_logs', [
            'pin' => $mhs->id_finger,
            'sn' => $mesin->serial,
        ]);
    }

    public function test_laporan_harian_mahasiswa_calculates_daily_hours()
    {
        $today = now()->format('Y-m-d');

        $response = $this->actingAs($this->user)->get(route('laporan.harian', [
            'tipe' => 'mahasiswa',
            'tanggal' => $today,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Rekapitulasi Jam Harian');
        $response->assertSee('Total Jam Terjadwal');
        $response->assertSee('Total Jam Hadir');
        $response->assertSee('Rekapitulasi Jam Per Mahasiswa');
    }

    public function test_laporan_harian_dosen_calculates_daily_hours()
    {
        $today = now()->format('Y-m-d');

        $response = $this->actingAs($this->user)->get(route('laporan.harian', [
            'tipe' => 'dosen',
            'tanggal' => $today,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Rekapitulasi Jam Harian');
        $response->assertSee('Total Jam Terjadwal');
        $response->assertSee('Total Jam Hadir');
        $response->assertSee('Rekapitulasi Jam Per Dosen Pengajar');
    }

    public function test_laporan_harian_print_and_excel_export()
    {
        $today = now()->format('Y-m-d');

        // Test Print View
        $responsePrint = $this->actingAs($this->user)->get(route('laporan.harian.print', [
            'tipe' => 'mahasiswa',
            'tanggal' => $today,
        ]));
        $responsePrint->assertStatus(200);
        $responsePrint->assertSee('LAPORAN REKAPITULASI AKUMULASI JAM KEHADIRAN');

        // Test Excel Export
        $responseExcel = $this->actingAs($this->user)->get(route('laporan.harian.excel', [
            'tipe' => 'dosen',
            'tanggal' => $today,
        ]));
        $responseExcel->assertStatus(200);
        $responseExcel->assertHeader('Content-Type', 'application/vnd.ms-excel');
    }
}
