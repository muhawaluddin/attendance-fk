<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttLog extends Model
{
    use HasFactory;

    protected $table = 'att_logs';

    protected $fillable = [
        'sn',
        'pin',
        'scan_date',
        'scan_time',
        'verified',
        'status_scan',
        'work_code',
        'ruangan_id',
        'tipe_pengguna',
    ];

    protected $casts = [
        'scan_date' => 'datetime',
        'verified' => 'integer',
        'status_scan' => 'integer',
    ];

    public function ruangan(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    public function getNamaPenggunaAttribute(): string
    {
        if ($this->tipe_pengguna === 'dosen') {
            $dsn = Dosen::where('id_finger', $this->pin)->first();
            if ($dsn) {
                return $dsn->nama_lengkap;
            }
        }

        $mhs = Mahasiswa::where('id_finger', $this->pin)->first();
        if ($mhs) {
            return $mhs->nama_mahasiswa;
        }

        $dsn = Dosen::where('id_finger', $this->pin)->first();
        if ($dsn) {
            return $dsn->nama_lengkap;
        }

        return 'ID: ' . $this->pin;
    }
}
