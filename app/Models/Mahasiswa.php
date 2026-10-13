<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $table = 'mahasiswas';

    protected $fillable = [
        'id_finger',
        'nim',
        'nama_mahasiswa',
        'departemen_id',
        'kelas_id',
        'angkatan',
        'no_hp',
        'jenis_kelamin',
        'status_aktif',
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
    ];

    public function departemen(): BelongsTo
    {
        return $this->belongsTo(Departemen::class, 'departemen_id');
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function jadwalMahasiswas(): HasMany
    {
        return $this->hasMany(JadwalMahasiswa::class, 'mahasiswa_id');
    }
}
