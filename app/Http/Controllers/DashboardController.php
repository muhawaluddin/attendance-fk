<?php

namespace App\Http\Controllers;

use App\Models\AttLog;
use App\Models\Departemen;
use App\Models\Dosen;
use App\Models\Jadwal;
use App\Models\JadwalHari;
use App\Models\JadwalJam;
use App\Models\Kelas;
use App\Models\Mahasiswa;
use App\Models\Mesin;
use App\Models\Pengaturan;
use App\Models\Ruangan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $currentTime = Carbon::now()->format('H:i');

        // Statistik
        $totalMahasiswa = Mahasiswa::count();
        $totalDosen = Dosen::count();
        $totalRuangan = Ruangan::count();
        $totalMesin = Mesin::count();
        $totalJadwalAktif = Jadwal::where('status', 'aktif')->count();
        $totalScanHariIni = AttLog::whereDate('scan_date', $today)->count();

        // Jadwal Hari Ini
        $jadwalHariIni = JadwalJam::whereHas('jadwalHari', function ($q) use ($today) {
            $q->whereDate('tanggal', $today);
        })
        ->with(['jadwalHari.jadwalKelas.jadwal.mataKuliah', 'jadwalHari.jadwalKelas.kelas', 'ruangan', 'jadwalDosens.dosen'])
        ->orderBy('jam_masuk', 'asc')
        ->get();

        // Scan Activity Terbaru
        $latestScans = AttLog::with('ruangan')
            ->orderBy('scan_date', 'desc')
            ->limit(8)
            ->get();

        $pengaturan = Pengaturan::where('is_active', true)->first() ?? new Pengaturan();

        return view('dashboard.index', compact(
            'totalMahasiswa',
            'totalDosen',
            'totalRuangan',
            'totalMesin',
            'totalJadwalAktif',
            'totalScanHariIni',
            'jadwalHariIni',
            'latestScans',
            'pengaturan',
            'currentTime',
            'today'
        ));
    }
}
