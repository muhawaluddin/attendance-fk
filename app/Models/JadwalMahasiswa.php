<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JadwalMahasiswa extends Model
{
    use HasFactory;

    protected $table = 'jadwal_mahasiswas';

    protected $fillable = [
        'jadwal_kelas_id',
        'mahasiswa_id',
    ];

    public function jadwalKelas(): BelongsTo
    {
        return $this->belongsTo(JadwalKelas::class, 'jadwal_kelas_id');
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }
}
