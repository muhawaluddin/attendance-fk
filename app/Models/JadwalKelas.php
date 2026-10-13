<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JadwalKelas extends Model
{
    use HasFactory;

    protected $table = 'jadwal_kelas';

    protected $fillable = [
        'jadwal_id',
        'kelas_id',
    ];

    public function jadwal(): BelongsTo
    {
        return $this->belongsTo(Jadwal::class, 'jadwal_id');
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function jadwalHaris(): HasMany
    {
        return $this->hasMany(JadwalHari::class, 'jadwal_kelas_id');
    }

    public function jadwalMahasiswas(): HasMany
    {
        return $this->hasMany(JadwalMahasiswa::class, 'jadwal_kelas_id');
    }
}
