<?php

namespace App\Imports;

use App\Models\Kelas;
use App\Models\Mahasiswa;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class MahasiswaImport implements ToModel, WithHeadingRow, SkipsOnFailure, WithBatchInserts, WithChunkReading
{
    use SkipsFailures;

    private array $kelasCache = [];

    public function model(array $row): ?Mahasiswa
    {
        $nim = trim($row['nim'] ?? '');
        $nama = trim($row['nama_mahasiswa'] ?? $row['nama'] ?? '');

        if (empty($nim) || empty($nama)) {
            return null;
        }

        // Skip if NIM already exists
        if (Mahasiswa::where('nim', $nim)->exists()) {
            return null;
        }

        // Resolve kelas_id from kelas name if given
        $kelasId = null;
        $kelasName = trim($row['kelas'] ?? $row['nama_kelas'] ?? '');
        if ($kelasName) {
            if (!isset($this->kelasCache[$kelasName])) {
                $kelas = Kelas::where('nama_kelas', $kelasName)->first();
                $this->kelasCache[$kelasName] = $kelas?->id;
            }
            $kelasId = $this->kelasCache[$kelasName];
        }

        // Also support direct kelas_id column
        if (!$kelasId && !empty($row['kelas_id'])) {
            $kelasId = (int) $row['kelas_id'];
        }

        $jk = strtoupper(trim($row['jenis_kelamin'] ?? $row['jk'] ?? 'L'));
        if (!in_array($jk, ['L', 'P'])) {
            $jk = 'L';
        }

        return new Mahasiswa([
            'nim'            => $nim,
            'nama_mahasiswa' => $nama,
            'id_finger'      => trim($row['id_finger'] ?? $row['pin'] ?? '') ?: null,
            'kelas_id'       => $kelasId,
            'angkatan'       => trim($row['angkatan'] ?? '') ?: null,
            'no_hp'          => trim($row['no_hp'] ?? $row['hp'] ?? '') ?: null,
            'jenis_kelamin'  => $jk,
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
