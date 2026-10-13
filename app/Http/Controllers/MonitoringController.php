<?php

namespace App\Http\Controllers;

use App\Models\JadwalJam;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $currentTime = Carbon::now()->format('H:i');

        $jadwals = JadwalJam::whereHas('jadwalHari', function ($q) use ($today) {
            $q->whereDate('tanggal', $today);
        })
        ->with([
            'jadwalHari.jadwalKelas.jadwal.mataKuliah',
            'jadwalHari.jadwalKelas.kelas',
            'ruangan',
            'departemen',
            'jadwalDosens.dosen',
        ])
        ->orderBy('jam_masuk', 'asc')
        ->get();

        // Tandai status perkuliahan
        $jadwalsWithStatus = $jadwals->map(function ($j) use ($currentTime) {
            $jamMasuk = $j->jam_masuk;
            $jamKeluar = $j->jam_keluar ?: $j->hitungJamKeluar();

            $status = 'upcoming';
            $statusLabel = 'Akan Datang';
            $badgeClass = 'bg-amber-500/20 text-amber-400 border border-amber-500/30';

            if ($currentTime >= $jamMasuk && $currentTime <= $jamKeluar) {
                $status = 'ongoing';
                $statusLabel = 'Sedang Berlangsung';
                $badgeClass = 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 animate-pulse';
            } elseif ($currentTime > $jamKeluar) {
                $status = 'completed';
                $statusLabel = 'Selesai';
                $badgeClass = 'bg-slate-700/50 text-slate-400 border border-slate-600/30';
            }

            $j->calculated_jam_keluar = $jamKeluar;
            $j->current_status = $status;
            $j->status_label = $statusLabel;
            $j->badge_class = $badgeClass;

            return $j;
        });

        return view('monitoring.index', [
            'today' => $today,
            'currentTime' => $currentTime,
            'jadwals' => $jadwalsWithStatus,
        ]);
    }
}
