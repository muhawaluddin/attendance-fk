@extends('layouts.app')

@section('title', 'Detail Jadwal ' . ($jadwal->mataKuliah->nama_mk ?? ''))
@section('page-title', 'Detail Jadwal Perkuliahan')
@section('page-subtitle', 'Informasi lengkap blok ' . ($jadwal->mataKuliah->nama_mk ?? '') . ' dan kelas pelaksana')

@section('content')
<div class="space-y-6">

    <!-- Back Button -->
    <div>
        <a href="{{ route('jadwal.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-brand-700">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Daftar Jadwal</span>
        </a>
    </div>

    <!-- Main Detail Card -->
    <div class="bg-white table-card border border-slate-200/80 shadow-sm p-6 sm:p-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-slate-100">
            <div>
                <span class="px-2.5 py-0.5 rounded text-xs font-bold uppercase bg-brand-50 text-brand-700 border border-brand-200">
                    {{ $jadwal->semester }} {{ $jadwal->thn_ajaran }}
                </span>
                <h1 class="text-2xl font-black text-slate-900 mt-2">
                    {{ $jadwal->mataKuliah->nama_mk ?? '-' }}
                </h1>
                <p class="text-xs text-slate-500 font-mono mt-0.5">
                    Kode: {{ $jadwal->mataKuliah->kode_mk ?: '-' }} &bull; {{ $jadwal->mataKuliah->sks ?? 4 }} SKS
                </p>
            </div>

            <div class="text-left md:text-right">
                <span class="text-xs text-slate-400 block font-semibold uppercase">Periode Pelaksanaan</span>
                <span class="text-sm font-bold text-slate-800">
                    {{ $jadwal->tgl_mulai ? $jadwal->tgl_mulai->format('d F Y') : '-' }} s/d {{ $jadwal->tgl_selesai ? $jadwal->tgl_selesai->format('d F Y') : '-' }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 py-6 border-b border-slate-100 text-xs">
            <div>
                <span class="text-slate-400 font-bold uppercase block mb-1">Dosen Koordinator:</span>
                <span class="font-bold text-slate-900 text-sm">{{ $jadwal->koordinator->nama_lengkap ?? '-' }}</span>
                <p class="text-slate-500 mt-0.5">NIP: {{ $jadwal->koordinator->nip ?? '-' }}</p>
            </div>
            <div>
                <span class="text-slate-400 font-bold uppercase block mb-1">Dosen Sekretaris:</span>
                <span class="font-bold text-slate-900 text-sm">{{ $jadwal->sekretaris->nama_lengkap ?? '-' }}</span>
                <p class="text-slate-500 mt-0.5">NIP: {{ $jadwal->sekretaris->nip ?? '-' }}</p>
            </div>
            <div>
                <span class="text-slate-400 font-bold uppercase block mb-1">Pengaturan Jam:</span>
                <span class="font-bold text-slate-900 text-sm">{{ $jadwal->pengaturan->nama_pengaturan ?? 'Standar' }}</span>
                <p class="text-slate-500 mt-0.5">{{ $jadwal->pengaturan->menit_perjam ?? 50 }} mnt/jam &bull; pergantian: {{ $jadwal->pengaturan->menit_pergantian ?? 10 }} mnt</p>
            </div>
        </div>

        <!-- Daftar Kelas Pelaksana -->
        <div class="pt-6">
            <h3 class="text-base font-extrabold text-slate-900 mb-4">Kelas Pelaksana Perkuliahan</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($jadwal->jadwalKelas as $jk)
                    <div class="p-5 table-card bg-slate-50 border border-slate-200/80 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="w-9 h-9 rounded-lg bg-brand-700 text-white flex items-center justify-center font-bold text-sm shadow-sm">
                                    <i class="fa-solid fa-chalkboard"></i>
                                </span>
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-100 text-blue-800">
                                    {{ $jk->kelas->angkatan ? 'Angkatan ' . $jk->kelas->angkatan : 'Aktif' }}
                                </span>
                            </div>
                            <h4 class="font-extrabold text-slate-900 text-base">{{ $jk->kelas->nama_kelas ?? 'Kelas' }}</h4>
                        </div>

                        <div class="mt-4 pt-4 border-t border-slate-200 flex items-center gap-2">
                            <!-- Submenu Sesi Jadwal -->
                            <a href="{{ route('jadwal.perkelas', $jk) }}" class="flex-1 py-2 px-3 rounded-lg bg-brand-700 hover:bg-brand-800 text-white font-bold text-xs text-center transition-colors shadow-sm">
                                Sesi Perkuliahan &rarr;
                            </a>

                            <!-- Submenu Mahasiswa Kelas -->
                            <a href="{{ route('jadwal.mahasiswa', $jk) }}" class="py-2 px-3 rounded-lg bg-navy-900 hover:bg-navy-800 text-white font-bold text-xs transition-colors shadow-sm" title="Peserta Mahasiswa">
                                <i class="fa-solid fa-users"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

</div>
@endsection
