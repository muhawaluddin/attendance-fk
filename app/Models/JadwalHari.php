<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JadwalHari extends Model
{
    use HasFactory;

    protected $table = 'jadwal_haris';

    protected $fillable = [
        'jadwal_kelas_id',
        'tanggal',
        'hari',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function jadwalKelas(): BelongsTo
    {
        return $this->belongsTo(JadwalKelas::class, 'jadwal_kelas_id');
    }

    public function jadwalJams(): HasMany
    {
        return $this->hasMany(JadwalJam::class, 'jadwal_hari_id');
    }
}
