<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';

    protected $fillable = [
        'nama_kelas',
        'angkatan',
    ];

    public function mahasiswas(): HasMany
    {
        return $this->hasMany(Mahasiswa::class, 'kelas_id');
    }

    public function jadwalKelas(): HasMany
    {
        return $this->hasMany(JadwalKelas::class, 'kelas_id');
    }
}
