<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Jadwal extends Model
{
    use HasFactory;

    protected $table = 'jadwals';

    protected $fillable = [
        'semester',
        'thn_ajaran',
        'tgl_mulai',
        'tgl_selesai',
        'mata_kuliah_id',
        'dosen_koordinator_id',
        'dosen_sekretaris_id',
        'pengaturan_id',
        'status',
    ];

    protected $casts = [
        'tgl_mulai' => 'date',
        'tgl_selesai' => 'date',
    ];

    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class, 'mata_kuliah_id');
    }

    public function koordinator(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'dosen_koordinator_id');
    }

    public function sekretaris(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'dosen_sekretaris_id');
    }

    public function pengaturan(): BelongsTo
    {
        return $this->belongsTo(Pengaturan::class, 'pengaturan_id');
    }

    public function jadwalKelas(): HasMany
    {
        return $this->hasMany(JadwalKelas::class, 'jadwal_id');
    }
}
