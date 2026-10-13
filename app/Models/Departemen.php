<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Departemen extends Model
{
    use HasFactory;

    protected $table = 'departemens';

    protected $fillable = [
        'kode_departemen',
        'nama_departemen',
    ];

    public function dosens(): HasMany
    {
        return $this->hasMany(Dosen::class, 'departemen_id');
    }

    public function mahasiswas(): HasMany
    {
        return $this->hasMany(Mahasiswa::class, 'departemen_id');
    }
}
