<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ruangan extends Model
{
    use HasFactory;

    protected $table = 'ruangans';

    protected $fillable = [
        'kode_ruangan',
        'nama_ruangan',
        'kapasitas',
        'lokasi',
        'keterangan',
    ];

    public function mesins(): HasMany
    {
        return $this->hasMany(Mesin::class, 'ruangan_id');
    }

    public function jadwalJams(): HasMany
    {
        return $this->hasMany(JadwalJam::class, 'ruangan_id');
    }
}
