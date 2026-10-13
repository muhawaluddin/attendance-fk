<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mesin extends Model
{
    use HasFactory;

    protected $table = 'mesins';

    protected $fillable = [
        'serial',
        'nama_mesin',
        'brand_mesin',
        'model_tipe',
        'tipe_koneksi',
        'activation',
        'kode',
        'no_mesin',
        'ip_address',
        'port',
        'comm_key',
        'metode_verifikasi',
        'ruangan_id',
        'status_online',
        'last_synced_at',
    ];

    protected $casts = [
        'status_online' => 'boolean',
        'last_synced_at' => 'datetime',
    ];

    public function ruangan(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    /**
     * Uji konektivitas socket ke IP dan Port mesin
     * Mendukung TCP/IP, UDP, serta port fallback khusus mesin Deli (5005 <-> 4370)
     */
    public function pingSocket(): bool
    {
        if (empty($this->ip_address) || empty($this->port)) {
            return false;
        }

        $portsToTry = [(int)$this->port];

        // Jika brand Deli atau port 5005/4370, tambahkan fallback port alternatif Deli
        if (stripos($this->brand_mesin, 'deli') !== false || in_array((int)$this->port, [5005, 4370])) {
            $altPort = ((int)$this->port === 5005) ? 4370 : 5005;
            if (!in_array($altPort, $portsToTry)) {
                $portsToTry[] = $altPort;
            }
        }

        $timeout = 2; // 2 detik timeout per port

        foreach ($portsToTry as $targetPort) {
            try {
                if ($this->tipe_koneksi === 'standalone_udp') {
                    $fp = @fsockopen('udp://' . $this->ip_address, $targetPort, $errno, $errstr, $timeout);
                } else {
                    $fp = @fsockopen($this->ip_address, $targetPort, $errno, $errstr, $timeout);
                }

                if ($fp) {
                    fclose($fp);

                    // Update status dan jika port berubah ke port aktif, update portnya
                    $updates = [
                        'status_online' => true,
                        'last_synced_at' => now(),
                    ];
                    if ($targetPort !== (int)$this->port) {
                        $updates['port'] = (string)$targetPort;
                    }

                    $this->update($updates);
                    return true;
                }
            } catch (\Throwable $e) {
                // Lanjut ke port berikutnya
            }
        }

        $this->update(['status_online' => false]);
        return false;
    }
}
