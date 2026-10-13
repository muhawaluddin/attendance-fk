<?php

namespace Tests\Feature;

use App\Models\AttLog;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\Mesin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class DeliFingerprintTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_deli_machine_can_be_registered_with_port_5005()
    {
        $admin = User::first() ?? User::factory()->create();

        $response = $this->actingAs($admin)->post(route('mesin.store'), [
            'serial' => 'DELI-TEST-' . uniqid(),
            'nama_mesin' => 'Deli E3960 Lab Komputer',
            'brand_mesin' => 'Deli',
            'model_tipe' => 'E3960',
            'tipe_koneksi' => 'standalone_tcp',
            'ip_address' => '192.168.1.205',
            'port' => '5005',
            'comm_key' => '0',
            'metode_verifikasi' => ['fingerprint', 'pin'],
        ]);

        $response->assertRedirect(route('mesin.index'));
        $this->assertDatabaseHas('mesins', [
            'brand_mesin' => 'Deli',
            'port' => '5005',
        ]);
    }

    public function test_deli_push_webhook_endpoint_records_scan()
    {
        $uniquePin = 'DELI_MHS_' . rand(1000, 9999);
        $timeStr = '2026-09-29 08:30:15';

        // Deli payload style 1 (JSON with dev_sn, user_id, verify_time)
        $response = $this->postJson(route('api.fingerprint.deli'), [
            'dev_sn' => 'DELI-E3960-01',
            'user_id' => $uniquePin,
            'verify_time' => $timeStr,
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'status' => 'success',
            'pin' => $uniquePin,
        ]);

        $this->assertDatabaseHas('att_logs', [
            'pin' => $uniquePin,
            'sn' => 'DELI-E3960-01',
            'scan_time' => '08:30:15',
        ]);
    }

    public function test_deli_batch_payload_records_multiple_scans()
    {
        $pin1 = 'BATCH_1_' . rand(1000, 9999);
        $pin2 = 'BATCH_2_' . rand(1000, 9999);

        $response = $this->postJson(route('api.fingerprint.deli'), [
            'dev_sn' => 'DELI-BATCH-01',
            'records' => [
                ['user_id' => $pin1, 'verify_time' => '2026-09-29 09:00:00'],
                ['user_id' => $pin2, 'verify_time' => '2026-09-29 09:01:00'],
            ]
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'count' => 2,
        ]);

        $this->assertDatabaseHas('att_logs', ['pin' => $pin1]);
        $this->assertDatabaseHas('att_logs', ['pin' => $pin2]);
    }

    public function test_deli_adms_cdata_raw_payload()
    {
        $pin = 'ADMS_' . rand(1000, 9999);
        $cdata = "{$pin}\t2026-09-29 07:45:00\t1\t1\t0\n";

        $response = $this->call(
            'POST',
            '/iclock/cdata?SN=DELI-ADMS-001&table=ATTLOG',
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'text/plain'],
            $cdata
        );

        $response->assertStatus(200);
        $this->assertStringContainsString('OK', $response->getContent());
        $this->assertDatabaseHas('att_logs', ['pin' => $pin]);
    }

    public function test_deli_usb_dat_file_import()
    {
        $admin = User::first() ?? User::factory()->create();

        $pin1 = 'USB_DAT_1_' . rand(1000, 9999);
        $pin2 = 'USB_DAT_2_' . rand(1000, 9999);

        // Biometric DAT format: PIN \t Date Time \t Status \t VerifyType
        $datContent = "{$pin1}\t2026-09-29 08:05:12\t1\t1\n{$pin2}\t2026-09-29 08:06:45\t1\t1\n";
        $file = UploadedFile::fake()->createWithContent('attlog.dat', $datContent);

        $response = $this->actingAs($admin)->post(route('mesin.import-log'), [
            'file' => $file,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('att_logs', ['pin' => $pin1]);
        $this->assertDatabaseHas('att_logs', ['pin' => $pin2]);
    }

    public function test_deli_usb_csv_file_import()
    {
        $admin = User::first() ?? User::factory()->create();

        $pin1 = 'USB_CSV_1_' . rand(1000, 9999);
        $pin2 = 'USB_CSV_2_' . rand(1000, 9999);

        $csvContent = "User ID,Scan Time,Status\n{$pin1},2026-09-29 08:10:00,1\n{$pin2},2026-09-29 08:11:00,1\n";
        $file = UploadedFile::fake()->createWithContent('attlog.csv', $csvContent);

        $response = $this->actingAs($admin)->post(route('mesin.import-log'), [
            'file' => $file,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('att_logs', ['pin' => $pin1]);
        $this->assertDatabaseHas('att_logs', ['pin' => $pin2]);
    }
}
