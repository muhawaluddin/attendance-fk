<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JadwalDosen extends Model
{
    use HasFactory;

    protected $table = 'jadwal_dosens';

    protected $fillable = [
        'jadwal_jam_id',
        'dosen_id',
    ];

    public function jadwalJam(): BelongsTo
    {
        return $this->belongsTo(JadwalJam::class, 'jadwal_jam_id');
    }

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'dosen_id');
    }
}
