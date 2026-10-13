@extends('layouts.app')

@section('title', 'Sesi Jadwal ' . ($jadwalKelas->kelas->nama_kelas ?? ''))
@section('page-title', 'Jadwal Sesi Perkuliahan Perkelas')
@section('page-subtitle', ($jadwalKelas->jadwal->mataKuliah->nama_mk ?? '') . ' - ' . ($jadwalKelas->kelas->nama_kelas ?? ''))

@section('content')
<div class="space-y-6" x-data="{ modalAddJam: false, activeHariId: null, modalAddHari: false }">

    <!-- Header Navigation & Info Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <a href="{{ route('jadwal.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-brand-700">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Daftar Jadwal</span>
        </a>

        <div class="flex items-center gap-2">
            <a href="{{ route('jadwal.mahasiswa', $jadwalKelas) }}" class="px-4 py-2 rounded-lg bg-navy-900 hover:bg-navy-800 text-white font-bold text-xs shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-users"></i>
                <span>Kelola Mahasiswa Perkelas</span>
            </a>

            <button @click="modalAddHari = true" class="px-4 py-2 rounded-lg bg-brand-700 hover:bg-brand-800 text-white font-bold text-xs shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-calendar-plus"></i>
                <span>Tambah Hari Perkuliahan</span>
            </button>
        </div>
    </div>

    <!-- Header Summary Card -->
    <div class="bg-white table-card border border-slate-200/80 shadow-sm p-5 sm:p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <span class="px-2.5 py-0.5 rounded text-xs font-bold uppercase bg-brand-50 text-brand-700 border border-brand-200">
                {{ $jadwalKelas->jadwal->semester }} {{ $jadwalKelas->jadwal->thn_ajaran }}
            </span>
            <h2 class="text-xl font-extrabold text-slate-900 mt-2">
                {{ $jadwalKelas->jadwal->mataKuliah->nama_mk ?? 'Blok' }} &bull; <span class="text-brand-700">{{ $jadwalKelas->kelas->nama_kelas ?? 'Kelas' }}</span>
            </h2>
            <p class="text-xs text-slate-500 mt-1">
                Total {{ $jadwalKelas->jadwalHaris->count() }} hari terjadwal &bull; Durasi standar: {{ $pengaturan->menit_perjam }} mnt/jam
            </p>
        </div>

        <div class="text-xs space-y-1 text-slate-600 bg-slate-50 p-4 rounded-lg border border-slate-200">
            <div>Koordinator: <strong>{{ $jadwalKelas->jadwal->koordinator->nama_lengkap ?? '-' }}</strong></div>
            <div>Sekretaris: <strong>{{ $jadwalKelas->jadwal->sekretaris->nama_lengkap ?? '-' }}</strong></div>
        </div>
    </div>

    <!-- Timeline of Days -->
    <div class="space-y-4">
        @forelse($jadwalKelas->jadwalHaris->sortBy('tanggal') as $hari)
            <div class="bg-white table-card border border-slate-200/80 shadow-sm overflow-hidden">
                
                <!-- Day Header -->
                <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-brand-700 text-white flex items-center justify-center font-black text-sm shadow-sm">
                            {{ $hari->tanggal ? $hari->tanggal->format('d') : '-' }}
                        </div>
                        <div>
                            <h4 class="font-extrabold text-slate-900 text-sm">
                                {{ $hari->hari ?: $hari->tanggal->locale('id')->isoFormat('dddd') }}, {{ $hari->tanggal ? $hari->tanggal->locale('id')->isoFormat('D MMMM Y') : '-' }}
                            </h4>
                            <span class="text-xs text-slate-400 font-medium">
                                {{ $hari->jadwalJams->count() }} Sesi Perkuliahan
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <!-- Add Jam Button -->
                        <button @click="activeHariId = {{ $hari->id }}; modalAddJam = true"
                                class="px-3 py-1.5 rounded-lg bg-brand-700 hover:bg-brand-800 text-white font-bold text-xs shadow-sm flex items-center gap-1.5">
                            <i class="fa-solid fa-plus text-[10px]"></i>
                            <span>Tambah Sesi Jam</span>
                        </button>

                        <!-- Delete Day Button -->
                        <form action="{{ route('jadwal.hari.destroy', $hari) }}" method="POST" class="inline" onsubmit="return confirm('Hapus hari ini beserta seluruh sesi perkuliahannya?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 rounded transition-colors" title="Hapus Hari Ini">
                                <i class="fa-regular fa-trash-can text-sm"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Sessions of This Day -->
                <div class="p-5 sm:p-6">
                    @if($hari->jadwalJams->isEmpty())
                        <div class="text-center py-6 text-slate-400 text-xs italic">
                            Belum ada sesi jam perkuliahan untuk hari ini. Klik "Tambah Sesi Jam" untuk menginput waktu kegiatan.
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($hari->jadwalJams->sortBy('jam_masuk') as $jam)
                                @php
                                    $jamKeluar = $jam->jam_keluar ?: $jam->hitungJamKeluar($pengaturan->menit_perjam, $pengaturan->menit_pergantian);
                                @endphp
                                <div class="p-4 rounded-lg bg-slate-50 border border-slate-200/80 hover:border-slate-300 transition-all flex flex-col justify-between">
                                    <div>
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded text-xs font-mono font-bold bg-navy-950 text-white">
                                                <i class="fa-regular fa-clock text-[10px]"></i>
                                                {{ $jam->jam_masuk }} - {{ $jamKeluar }}
                                            </span>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200 text-slate-700">
                                                {{ $jam->jml_jam }} Jam
                                            </span>
                                        </div>

                                        <h5 class="font-extrabold text-slate-900 text-sm leading-snug">
                                            {{ $jam->kegiatan }}
                                        </h5>

                                        <div class="mt-3 space-y-1 text-xs text-slate-600">
                                            <div class="flex items-center gap-1.5">
                                                <i class="fa-solid fa-door-open text-brand-700 w-3.5 text-center"></i>
                                                <span>{{ $jam->ruangan->nama_ruangan ?? 'Ruangan belum diatur' }}</span>
                                            </div>
                                            @if($jam->departemen)
                                                <div class="flex items-center gap-1.5 text-slate-500">
                                                    <i class="fa-solid fa-building-columns text-slate-400 w-3.5 text-center"></i>
                                                    <span>{{ $jam->departemen->nama_departemen }}</span>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Dosen Pengampu -->
                                        @if($jam->jadwalDosens->isNotEmpty())
                                            <div class="mt-3 pt-2 border-t border-slate-200">
                                                <span class="text-[10px] text-slate-400 uppercase font-bold block mb-1">Dosen Pengajar:</span>
                                                <ul class="text-xs space-y-0.5">
                                                    @foreach($jam->jadwalDosens as $jd)
                                                        <li class="font-semibold text-slate-800 flex items-center gap-1">
                                                            <i class="fa-solid fa-user-doctor text-[9px] text-brand-700"></i>
                                                            <span>{{ $jd->dosen->nama_lengkap ?? '-' }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="mt-4 pt-3 border-t border-slate-200 flex justify-end">
                                        <form action="{{ route('jadwal.jam.destroy', $jam) }}" method="POST" class="inline" onsubmit="return confirm('Hapus sesi jam perkuliahan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-red-600 hover:text-red-800 font-bold flex items-center gap-1">
                                                <i class="fa-regular fa-trash-can text-xs"></i>
                                                <span>Hapus Sesi</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>
        @empty
            <div class="p-12 text-center bg-white table-card border border-slate-200 text-slate-400">
                Belum ada hari perkuliahan yang dibuat. Klik "Tambah Hari Perkuliahan" di atas.
            </div>
        @endforelse
    </div>

    <!-- Modal Tambah Hari Perkuliahan -->
    <div x-show="modalAddHari" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div @click.outside="modalAddHari = false" class="modal-box bg-white w-full max-w-sm">
            <div class="modal-header">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-brand-50 flex items-center justify-center">
                        <i class="fa-solid fa-calendar-plus text-brand-700"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Tambah Hari Perkuliahan</h3>
                        <p class="text-xs text-slate-500">Pilih tanggal kegiatan perkuliahan</p>
                    </div>
                </div>
                <button @click="modalAddHari = false" class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 transition-colors">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>
            <form action="{{ route('jadwal.hari.store', $jadwalKelas) }}" method="POST">
                @csrf
                <div class="modal-body space-y-3">
                    <div>
                        <label class="form-label">Tanggal Perkuliahan <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal" required value="{{ now()->toDateString() }}" class="form-input">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" @click="modalAddHari = false" class="px-4 py-2 rounded-lg text-sm font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-lg text-sm font-bold bg-brand-700 text-white hover:bg-brand-800 shadow-md">Simpan Hari</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Tambah Sesi Jam Perkuliahan -->
    <div x-show="modalAddJam" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto">
        <div @click.outside="modalAddJam = false" class="modal-box bg-white w-full max-w-lg my-8">
            <div class="modal-header">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-brand-50 flex items-center justify-center">
                        <i class="fa-solid fa-clock text-brand-700"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Tambah Sesi Jam Perkuliahan</h3>
                        <p class="text-xs text-slate-500">Atur jam masuk, durasi, materi, ruangan, dan dosen</p>
                    </div>
                </div>
                <button @click="modalAddJam = false" class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 transition-colors">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <form :action="'{{ url('jadwal-hari') }}/' + activeHariId + '/jam'" method="POST">
                @csrf
                <div class="modal-body space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="form-label">Jam Masuk (WITA) <span class="text-red-500">*</span></label>
                            <input type="text" name="jam_masuk" value="08:00" required placeholder="08:00" class="form-input font-mono text-center">
                        </div>
                        <div>
                            <label class="form-label">Jumlah Jam (SKS) <span class="text-red-500">*</span></label>
                            <input type="number" name="jml_jam" value="2" min="1" max="8" required class="form-input text-center font-bold">
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Nama Kegiatan / Materi Perkuliahan <span class="text-red-500">*</span></label>
                        <input type="text" name="kegiatan" required placeholder="Contoh: Kuliah: Patofisiologi Jantung" class="form-input">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="form-label">Ruang Kuliah <span class="text-red-500">*</span></label>
                            <select name="ruangan_id" required class="form-input">
                                <option value="">-- Pilih Ruangan --</option>
                                @foreach($ruangans as $r)
                                    <option value="{{ $r->id }}">{{ $r->nama_ruangan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Departemen Pengampu</label>
                            <select name="departemen_id" class="form-input">
                                <option value="">-- Pilih Departemen --</option>
                                @foreach($departemens as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->nama_departemen }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Pilih Dosen Pengajar</label>
                        <div class="max-h-40 overflow-y-auto p-2 bg-slate-50 border border-slate-200 rounded-lg space-y-1">
                            @foreach($dosens as $d)
                                <label class="flex items-center gap-2 p-1.5 text-xs hover:bg-white rounded-md cursor-pointer">
                                    <input type="checkbox" name="dosen_ids[]" value="{{ $d->id }}"
                                           class="rounded text-brand-700 focus:ring-brand-600 border-slate-300">
                                    <span class="font-semibold text-slate-800">{{ $d->nama_lengkap }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" @click="modalAddJam = false" class="px-4 py-2 rounded-lg text-sm font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-lg text-sm font-bold bg-brand-700 text-white hover:bg-brand-800 shadow-md">Simpan Sesi Perkuliahan</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
