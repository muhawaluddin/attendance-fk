<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dosen extends Model
{
    use HasFactory;

    protected $table = 'dosens';

    protected $fillable = [
        'id_finger',
        'nip',
        'nama_dosen',
        'gelar_depan',
        'gelar_belakang',
        'departemen_id',
        'no_hp',
        'email',
    ];

    public function departemen(): BelongsTo
    {
        return $this->belongsTo(Departemen::class, 'departemen_id');
    }

    public function getNamaLengkapAttribute(): string
    {
        $depan = $this->gelar_depan ? trim($this->gelar_depan) . ' ' : '';
        $belakang = $this->gelar_belakang ? ', ' . trim($this->gelar_belakang) : '';
        return $depan . $this->nama_dosen . $belakang;
    }

    public function jadwalDosens(): HasMany
    {
        return $this->hasMany(JadwalDosen::class, 'dosen_id');
    }
}
