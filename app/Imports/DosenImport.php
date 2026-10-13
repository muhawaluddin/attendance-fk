<?php

namespace App\Imports;

use App\Models\Departemen;
use App\Models\Dosen;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class DosenImport implements ToModel, WithHeadingRow, SkipsOnFailure, WithBatchInserts, WithChunkReading
{
    use SkipsFailures;

    private array $deptCache = [];

    public function model(array $row): ?Dosen
    {
        $nip  = trim($row['nip'] ?? '');
        $nama = trim($row['nama_dosen'] ?? $row['nama'] ?? '');

        if (empty($nip) || empty($nama)) {
            return null;
        }

        // Skip if NIP already exists
        if (Dosen::where('nip', $nip)->exists()) {
            return null;
        }

        // Resolve departemen_id from name
        $deptId = null;
        $deptName = trim($row['departemen'] ?? $row['nama_departemen'] ?? '');
        if ($deptName) {
            if (!isset($this->deptCache[$deptName])) {
                $dept = Departemen::where('nama_departemen', 'like', "%{$deptName}%")->first();
                $this->deptCache[$deptName] = $dept?->id;
            }
            $deptId = $this->deptCache[$deptName];
        }

        if (!$deptId && !empty($row['departemen_id'])) {
            $deptId = (int) $row['departemen_id'];
        }

        return new Dosen([
            'nip'            => $nip,
            'nama_dosen'     => $nama,
            'gelar_depan'    => trim($row['gelar_depan'] ?? '') ?: null,
            'gelar_belakang' => trim($row['gelar_belakang'] ?? '') ?: null,
            'id_finger'      => trim($row['id_finger'] ?? $row['pin'] ?? '') ?: null,
            'departemen_id'  => $deptId,
            'no_hp'          => trim($row['no_hp'] ?? $row['hp'] ?? '') ?: null,
            'email'          => trim($row['email'] ?? '') ?: null,
        ]);
    }

    public function batchSize(): int
    {
        return 200;
    }

    public function chunkSize(): int
    {
        return 200;
    }
}
