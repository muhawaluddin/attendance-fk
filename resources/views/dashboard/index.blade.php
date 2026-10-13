@extends('layouts.app')

@section('title', 'Beranda')
@section('page-title', 'Dashboard Beranda')
@section('page-subtitle', 'Sistem Informasi Presensi Terpadu ' . ($appSetting->faculty_name ?: ($appSetting->institution_name ?? 'Universitas Hasanuddin')))

@section('content')
@php
    $ongoingCount = $jadwalHariIni->filter(function($j) use ($currentTime, $pengaturan) {
        $jamKeluar = $j->jam_keluar ?: $j->hitungJamKeluar($pengaturan->menit_perjam ?? 50, $pengaturan->menit_pergantian ?? 10);
        return $currentTime >= $j->jam_masuk && $currentTime <= $jamKeluar;
    })->count();

    $upcomingCount = $jadwalHariIni->filter(function($j) use ($currentTime) {
        return $currentTime < $j->jam_masuk;
    })->count();

    $completedCount = $jadwalHariIni->filter(function($j) use ($currentTime, $pengaturan) {
        $jamKeluar = $j->jam_keluar ?: $j->hitungJamKeluar($pengaturan->menit_perjam ?? 50, $pengaturan->menit_pergantian ?? 10);
        return $currentTime > $jamKeluar;
    })->count();
@endphp

<div class="space-y-5">

    <!-- Welcome Hero Banner -->
    <div class="relative overflow-hidden rounded-xl bg-gradient-to-r from-navy-950 via-slate-900 to-navy-900 text-white p-6 sm:p-7 shadow-lg border border-slate-800/80">
        <!-- Subtle Background Glow -->
        <div class="absolute -right-20 -bottom-20 w-80 h-80 rounded-full bg-brand-700/10 blur-3xl pointer-events-none"></div>
        <div class="absolute left-1/3 -top-20 w-60 h-60 rounded-full bg-blue-600/10 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="max-w-xl">
                <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-emerald-500/15 text-emerald-400 text-xs font-semibold mb-3 border border-emerald-500/25">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Sistem Aktif &bull; Waktu WITA (UTC+8)</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Selamat Datang, {{ auth()->user()->name }}!</h1>
                <p class="text-xs sm:text-sm text-slate-300 mt-2 leading-relaxed">
                    {{ $appSetting->app_name ?? 'Sistem Informasi Presensi' }} {{ $appSetting->institution_name ?? 'Universitas' }} berbasis sinkronisasi mesin sidik jari biometrik dan monitoring jadwal perkuliahan terintegrasi.
                </p>
                <div class="mt-5 flex flex-wrap gap-2.5">
                    <a href="{{ route('jadwal.index') }}" class="px-3.5 py-2 rounded-lg bg-brand-700 hover:bg-brand-600 text-white font-semibold text-xs transition-colors shadow-sm inline-flex items-center gap-2">
                        <i class="fa-solid fa-calendar-plus"></i>
                        <span>Kelola Jadwal Kuliah</span>
                    </a>
                    <a href="{{ route('laporan.harian') }}" class="px-3.5 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs transition-colors border border-slate-700/80 inline-flex items-center gap-2">
                        <i class="fa-solid fa-chart-pie"></i>
                        <span>Rekap Jam Harian</span>
                    </a>
                    <a href="{{ route('monitoring') }}" target="_blank" class="px-3.5 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition-colors shadow-sm inline-flex items-center gap-2">
                        <i class="fa-solid fa-tv"></i>
                        <span>Layar TV Monitor</span>
                    </a>
                </div>
            </div>

            <!-- Live Status & Metric Widget (Replaces static logos) -->
            <div class="grid grid-cols-2 gap-3 p-4 bg-white/5 backdrop-blur-md rounded-xl border border-white/10 shrink-0 lg:w-72">
                <div class="p-2.5 rounded-lg bg-white/5 border border-white/5">
                    <div class="flex items-center justify-between text-slate-400 mb-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider">Jam WITA</span>
                        <i class="fa-regular fa-clock text-xs text-brand-400"></i>
                    </div>
                    <div class="font-mono text-xl font-black text-white tracking-tight">{{ now()->format('H:i') }}</div>
                    <span class="text-[10px] text-slate-400 font-medium">{{ now()->locale('id')->isoFormat('D MMM Y') }}</span>
                </div>

                <div class="p-2.5 rounded-lg bg-white/5 border border-white/5">
                    <div class="flex items-center justify-between text-slate-400 mb-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider">Kuliah Hari Ini</span>
                        <i class="fa-solid fa-chalkboard text-xs text-blue-400"></i>
                    </div>
                    <div class="text-xl font-black text-white">{{ $jadwalHariIni->count() }} <span class="text-xs font-normal text-slate-400">Sesi</span></div>
                    <span class="text-[10px] text-emerald-400 font-semibold">{{ $ongoingCount }} Berlangsung</span>
                </div>

                <div class="p-2.5 rounded-lg bg-white/5 border border-white/5">
                    <div class="flex items-center justify-between text-slate-400 mb-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider">Mesin Finger</span>
                        <i class="fa-solid fa-fingerprint text-xs text-amber-400"></i>
                    </div>
                    <div class="text-xl font-black text-white">{{ $totalMesin }} <span class="text-xs font-normal text-slate-400">Unit</span></div>
                    <span class="text-[10px] text-slate-400 font-medium">Terhubung</span>
                </div>

                <div class="p-2.5 rounded-lg bg-white/5 border border-white/5">
                    <div class="flex items-center justify-between text-slate-400 mb-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider">Tap Hari Ini</span>
                        <i class="fa-solid fa-bolt text-xs text-purple-400"></i>
                    </div>
                    <div class="text-xl font-black text-brand-400">{{ number_format($totalScanHariIni) }}</div>
                    <span class="text-[10px] text-slate-400 font-medium">Log Masuk</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stat Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5">

        <a href="{{ route('mahasiswa.index') }}" class="bg-white p-4 table-card border border-slate-200/80 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group block">
            <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mb-3 group-hover:scale-105 transition-transform">
                <i class="fa-solid fa-user-graduate"></i>
            </div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Mahasiswa</p>
            <p class="text-2xl font-black text-slate-900 mt-0.5">{{ number_format($totalMahasiswa) }}</p>
            <span class="text-[10px] text-slate-400 mt-1 block">Peserta Terdaftar</span>
        </a>

        <a href="{{ route('dosen.index') }}" class="bg-white p-4 table-card border border-slate-200/80 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group block">
            <div class="w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center mb-3 group-hover:scale-105 transition-transform">
                <i class="fa-solid fa-user-doctor"></i>
            </div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Dosen</p>
            <p class="text-2xl font-black text-slate-900 mt-0.5">{{ number_format($totalDosen) }}</p>
            <span class="text-[10px] text-slate-400 mt-1 block">Tenaga Pendidik</span>
        </a>

        <a href="{{ route('ruangan.index') }}" class="bg-white p-4 table-card border border-slate-200/80 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group block">
            <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center mb-3 group-hover:scale-105 transition-transform">
                <i class="fa-solid fa-door-open"></i>
            </div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Ruang Kuliah</p>
            <p class="text-2xl font-black text-slate-900 mt-0.5">{{ number_format($totalRuangan) }}</p>
            <span class="text-[10px] text-slate-400 mt-1 block">Ruangan & Lab</span>
        </a>

        <a href="{{ route('mesin.index') }}" class="bg-white p-4 table-card border border-slate-200/80 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group block">
            <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center mb-3 group-hover:scale-105 transition-transform">
                <i class="fa-solid fa-fingerprint"></i>
            </div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Mesin Finger</p>
            <p class="text-2xl font-black text-slate-900 mt-0.5">{{ number_format($totalMesin) }}</p>
            <span class="text-[10px] text-slate-400 mt-1 block">Perangkat Standby</span>
        </a>

        <a href="{{ route('jadwal.index') }}" class="bg-white p-4 table-card border border-slate-200/80 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group block">
            <div class="w-9 h-9 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center mb-3 group-hover:scale-105 transition-transform">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Jadwal Blok</p>
            <p class="text-2xl font-black text-slate-900 mt-0.5">{{ number_format($totalJadwalAktif) }}</p>
            <span class="text-[10px] text-slate-400 mt-1 block">Kegiatan Akademik</span>
        </a>

        <a href="{{ route('laporan.harian') }}" class="bg-white p-4 table-card border border-slate-200/80 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group block">
            <div class="w-9 h-9 rounded-lg bg-brand-50 text-brand-700 flex items-center justify-center mb-3 group-hover:scale-105 transition-transform">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tap Scan Hari Ini</p>
            <p class="text-2xl font-black text-brand-700 mt-0.5">{{ number_format($totalScanHariIni) }}</p>
            <span class="text-[10px] text-slate-400 mt-1 block">Log Presensi WITA</span>
        </a>

    </div>

    <!-- Main Section: Jadwal Hari Ini & Live Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <!-- Left: Jadwal Hari Ini (2 Cols) -->
        <div class="lg:col-span-2 bg-white table-card border border-slate-200/80 shadow-sm p-5 flex flex-col justify-between">
            <div>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4 pb-3.5 border-b border-slate-100">
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
                            <i class="fa-solid fa-calendar-day text-brand-700"></i>
                            <span>Jadwal Kegiatan Hari Ini</span>
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $today->locale('id')->isoFormat('dddd, D MMMM Y') }} (WITA)</p>
                    </div>

                    <div class="flex items-center gap-1.5 text-xs">
                        @if($ongoingCount > 0)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                                {{ $ongoingCount }} Berlangsung
                            </span>
                        @endif
                        <span class="px-2.5 py-0.5 rounded text-[11px] font-bold bg-slate-100 text-slate-700">
                            Total {{ $jadwalHariIni->count() }} Sesi
                        </span>
                    </div>
                </div>

                @if($jadwalHariIni->isEmpty())
                    <div class="text-center py-12 px-4">
                        <div class="w-12 h-12 rounded-lg bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                            <i class="fa-regular fa-calendar-xmark text-xl"></i>
                        </div>
                        <p class="text-sm font-semibold text-slate-700">Tidak ada jadwal perkuliahan pada hari ini</p>
                        <p class="text-xs text-slate-400 mt-1">Gunakan menu Jadwal Kuliah untuk mengkonfigurasi sesi perkuliahan aktif.</p>
                        <div class="mt-4">
                            <a href="{{ route('jadwal.index') }}" class="px-3.5 py-2 rounded-lg bg-brand-700 hover:bg-brand-800 text-white font-bold text-xs inline-flex items-center gap-1.5 shadow-sm transition-colors">
                                <i class="fa-solid fa-plus text-xs"></i>
                                <span>Buat Jadwal Baru</span>
                            </a>
                        </div>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                                    <th class="pb-2.5">Waktu</th>
                                    <th class="pb-2.5">Blok & Kegiatan</th>
                                    <th class="pb-2.5">Kelas</th>
                                    <th class="pb-2.5">Ruangan</th>
                                    <th class="pb-2.5 text-right">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($jadwalHariIni as $j)
                                    @php
                                        $jamKeluar = $j->jam_keluar ?: $j->hitungJamKeluar($pengaturan->menit_perjam ?? 50, $pengaturan->menit_pergantian ?? 10);
                                        $isOngoing = ($currentTime >= $j->jam_masuk && $currentTime <= $jamKeluar);
                                        $isPassed  = ($currentTime > $jamKeluar);
                                    @endphp
                                    <tr class="hover:bg-slate-50/60 transition-colors {{ $isOngoing ? 'bg-emerald-50/40' : '' }}">
                                        <td class="py-3 pr-3 whitespace-nowrap">
                                            <div class="flex items-center gap-1.5 font-mono text-xs font-bold text-slate-800">
                                                <i class="fa-regular fa-clock text-slate-400 text-xs"></i>
                                                <span>{{ $j->jam_masuk }} – {{ $jamKeluar }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3 pr-3">
                                            <div class="font-bold text-slate-800 text-xs">{{ $j->jadwalHari->jadwalKelas->jadwal->mataKuliah->nama_mk ?? 'Blok' }}</div>
                                            <p class="text-xs text-slate-500 mt-0.5 line-clamp-1">{{ $j->kegiatan }}</p>
                                            @if($j->jadwalDosens->isNotEmpty())
                                                <p class="text-[11px] text-brand-700 font-medium mt-0.5 flex items-center gap-1">
                                                    <i class="fa-solid fa-user-doctor text-[9px]"></i>
                                                    <span>{{ $j->jadwalDosens->first()->dosen->nama_lengkap ?? '-' }}</span>
                                                </p>
                                            @endif
                                        </td>
                                        <td class="py-3 pr-3 whitespace-nowrap">
                                            <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 text-xs font-semibold">
                                                {{ $j->jadwalHari->jadwalKelas->kelas->nama_kelas ?? 'Kelas' }}
                                            </span>
                                        </td>
                                        <td class="py-3 pr-3 whitespace-nowrap text-xs text-slate-600">
                                            <span class="inline-flex items-center gap-1 font-semibold text-slate-700">
                                                <i class="fa-solid fa-location-dot text-slate-400 text-[10px]"></i>
                                                {{ $j->ruangan->nama_ruangan ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="py-3 text-right whitespace-nowrap">
                                            @if($isOngoing)
                                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                    Berlangsung
                                                </span>
                                            @elseif($isPassed)
                                                <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-500">Selesai</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">Akan Datang</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            @if($jadwalHariIni->isNotEmpty())
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <span>Menampilkan seluruh sesi hari ini</span>
                    <a href="{{ route('jadwal.index') }}" class="font-bold text-brand-700 hover:underline flex items-center gap-1">
                        <span>Lihat Kalender Lengkap</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            @endif
        </div>

        <!-- Right: Live Fingerprint Scan Activity -->
        <div class="bg-white table-card border border-slate-200/80 shadow-sm p-5 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4 pb-3.5 border-b border-slate-100">
                    <h3 class="font-extrabold text-slate-900 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-bolt text-amber-500"></i>
                        <span>Aktivitas Tap Fingerprint</span>
                    </h3>
                    <a href="{{ route('mesin.index') }}" class="text-xs font-semibold text-brand-700 hover:underline flex items-center gap-1">
                        <span>Simulator</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>

                <div class="space-y-2.5">
                    @forelse($latestScans as $scan)
                        <div class="p-2.5 rounded-lg bg-slate-50/80 border border-slate-100 flex items-center justify-between hover:bg-slate-100/80 transition-colors">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-white text-slate-700 border border-slate-200 flex items-center justify-center shadow-sm shrink-0">
                                    <i class="fa-solid fa-fingerprint text-brand-700 text-xs"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-slate-900 truncate max-w-[150px]" title="{{ $scan->nama_pengguna }}">
                                        {{ $scan->nama_pengguna }}
                                    </p>
                                    <p class="text-[11px] text-slate-500 truncate max-w-[150px]">
                                        <span class="font-mono text-brand-700 font-semibold">PIN {{ $scan->pin }}</span> &bull; {{ $scan->ruangan->nama_ruangan ?? 'Mesin ' . ($scan->sn ?? '-') }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="text-xs font-mono font-bold text-slate-800">
                                    {{ $scan->scan_date ? \Carbon\Carbon::parse($scan->scan_date)->format('H:i:s') : '-' }}
                                </span>
                                <p class="text-[10px] text-slate-400">
                                    {{ $scan->scan_date ? \Carbon\Carbon::parse($scan->scan_date)->diffForHumans() : '' }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-400 text-xs">
                            <i class="fa-solid fa-fingerprint text-2xl text-slate-300 mb-2 block"></i>
                            Belum ada rekaman scan sidik jari hari ini.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Pengaturan Perkuliahan Info Widget -->
            <div class="mt-4 p-3.5 rounded-lg bg-slate-900 text-white text-xs shadow-inner border border-slate-800">
                <div class="flex items-center justify-between font-bold mb-2">
                    <span class="flex items-center gap-1.5">
                        <i class="fa-solid fa-sliders text-brand-400 text-[11px]"></i>
                        <span>Parameter Perkuliahan</span>
                    </span>
                    <a href="{{ route('pengaturan.index') }}" class="text-brand-400 hover:underline text-[11px]">Ubah &rarr;</a>
                </div>
                <div class="grid grid-cols-2 gap-2 text-slate-300 text-[11px]">
                    <div class="p-1.5 rounded bg-white/5">Durasi Jam: <strong class="text-white">{{ $pengaturan->menit_perjam ?? 50 }} mnt</strong></div>
                    <div class="p-1.5 rounded bg-white/5">Pergantian: <strong class="text-white">{{ $pengaturan->menit_pergantian ?? 10 }} mnt</strong></div>
                    <div class="p-1.5 rounded bg-white/5">Jam Masuk: <strong class="text-white">{{ $pengaturan->jam_kuliah ?? '08:00' }}</strong></div>
                    <div class="p-1.5 rounded bg-white/5">Istirahat: <strong class="text-white">{{ $pengaturan->jam_mulai_istirahat ?? '12:00' }}</strong></div>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
