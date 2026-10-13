<?php

namespace Tests\Feature;

use App\Models\AttLog;
use App\Models\Mahasiswa;
use App\Models\Mesin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RealtimeLogTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
        $this->user = User::where('username', 'admin')->first();
    }

    public function test_can_access_realtime_log_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('log-realtime.index'));

        $response->assertStatus(200);
        $response->assertSee('LIVE SCAN RADAR');
        $response->assertSee('Status Konektivitas Perangkat');
    }

    public function test_can_fetch_realtime_feed_json(): void
    {
        $mhs = Mahasiswa::create([
            'nim' => 'C011231099',
            'nama_mahasiswa' => 'Test Mahasiswa Realtime',
            'id_finger' => '9999',
            'jenis_kelamin' => 'L',
            'status_aktif' => true,
        ]);

        $log = AttLog::create([
            'sn' => 'SN-TEST-001',
            'pin' => '9999',
            'scan_date' => now(),
            'scan_time' => now()->format('H:i:s'),
            'verified' => 1,
            'status_scan' => 0,
            'tipe_pengguna' => 'mahasiswa',
        ]);

        $response = $this->actingAs($this->user)->getJson(route('log-realtime.feed'));

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'success');
        $response->assertJsonFragment([
            'pin' => '9999',
            'nama' => 'Test Mahasiswa Realtime',
        ]);
    }

    public function test_can_ping_machine_via_json(): void
    {
        $mesin = Mesin::create([
            'serial' => 'SN-PING-TEST',
            'nama_mesin' => 'Mesin Ping Test',
            'brand_mesin' => 'Fingerspot',
            'tipe_koneksi' => 'standalone_tcp',
            'ip_address' => '127.0.0.1',
            'port' => '4370',
        ]);

        $response = $this->actingAs($this->user)->postJson(route('log-realtime.ping', $mesin));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'success',
            'mesin_id',
            'brand',
            'response_ms',
        ]);
    }
}
