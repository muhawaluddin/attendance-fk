<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JadwalJam extends Model
{
    use HasFactory;

    protected $table = 'jadwal_jams';

    protected $fillable = [
        'jadwal_hari_id',
        'ruangan_id',
        'departemen_id',
        'jam_masuk',
        'jml_jam',
        'jam_keluar',
        'kegiatan',
    ];

    public function jadwalHari(): BelongsTo
    {
        return $this->belongsTo(JadwalHari::class, 'jadwal_hari_id');
    }

    public function ruangan(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    public function departemen(): BelongsTo
    {
        return $this->belongsTo(Departemen::class, 'departemen_id');
    }

    public function jadwalDosens(): HasMany
    {
        return $this->hasMany(JadwalDosen::class, 'jadwal_jam_id');
    }

    public function hitungJamKeluar(int $menitPerjam = 50, int $menitPergantian = 10): string
    {
        if (!empty($this->jam_keluar)) {
            return $this->jam_keluar;
        }

        $parts = explode(':', $this->jam_masuk);
        $totalMinutes = ((int)$parts[0] * 60) + (int)($parts[1] ?? 0);
        $jml = max(1, (int)$this->jml_jam);
        $durasi = ($jml * $menitPerjam) + (($jml - 1) * $menitPergantian);
        $endMinutes = $totalMinutes + $durasi;

        $jam = floor($endMinutes / 60) % 24;
        $menit = $endMinutes % 60;

        return sprintf('%02d:%02d', $jam, $menit);
    }
}
