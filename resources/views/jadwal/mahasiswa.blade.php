@extends('layouts.app')

@section('title', 'Mahasiswa ' . ($jadwalKelas->kelas->nama_kelas ?? ''))
@section('page-title', 'Mahasiswa Peserta Perkelas')
@section('page-subtitle', ($jadwalKelas->jadwal->mataKuliah->nama_mk ?? '') . ' - ' . ($jadwalKelas->kelas->nama_kelas ?? ''))

@section('content')
<div class="space-y-6" x-data="{ modalAddMhs: false }">

    <!-- Header Navigation & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <a href="{{ route('jadwal.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-brand-700">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Daftar Jadwal</span>
        </a>

        <div class="flex items-center gap-2">
            <a href="{{ route('jadwal.perkelas', $jadwalKelas) }}" class="px-4 py-2 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-clock"></i>
                <span>Kelola Sesi Jadwal</span>
            </a>

            <button @click="modalAddMhs = true" class="px-4 py-2 rounded-lg bg-brand-700 hover:bg-brand-800 text-white font-bold text-xs shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-user-plus"></i>
                <span>Tambah Peserta Mahasiswa</span>
            </button>
        </div>
    </div>

    <!-- Header Summary Card -->
    <div class="bg-white table-card border border-slate-200/80 shadow-sm p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="px-2.5 py-0.5 rounded text-xs font-bold uppercase bg-brand-50 text-brand-700 border border-brand-200">
                {{ $jadwalKelas->jadwal->semester }} {{ $jadwalKelas->jadwal->thn_ajaran }}
            </span>
            <h2 class="text-xl font-extrabold text-slate-900 mt-2">
                {{ $jadwalKelas->jadwal->mataKuliah->nama_mk ?? 'Blok' }} &bull; <span class="text-brand-700">{{ $jadwalKelas->kelas->nama_kelas ?? 'Kelas' }}</span>
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">
                Total {{ $jadwalKelas->jadwalMahasiswas->count() }} mahasiswa terdaftar dalam daftar absensi kelas ini
            </p>
        </div>

        <a href="{{ route('laporan.mahasiswa', ['jadwal_id' => $jadwalKelas->jadwal_id, 'jadwal_kelas_id' => $jadwalKelas->id]) }}" 
           class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center gap-2 shadow-sm shrink-0">
            <i class="fa-solid fa-file-excel"></i>
            <span>Lihat Laporan Absensi</span>
        </a>
    </div>

    <!-- Table of Enrolled Students -->
    <div class="bg-white table-card border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200/70">
                        <th class="py-3.5 px-6 w-16 text-center">No</th>
                        <th class="py-3.5 px-6">NIM</th>
                        <th class="py-3.5 px-6">Nama Mahasiswa</th>
                        <th class="py-3.5 px-6">ID Fingerprint</th>
                        <th class="py-3.5 px-6">Departemen</th>
                        <th class="py-3.5 px-6 text-right w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($jadwalKelas->jadwalMahasiswas->sortBy('mahasiswa.nim') as $index => $jm)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3.5 px-6 text-center text-xs text-slate-400 font-mono">
                                {{ $loop->iteration }}
                            </td>
                            <td class="py-3.5 px-6 font-mono text-xs font-bold text-brand-700">
                                {{ $jm->mahasiswa->nim }}
                            </td>
                            <td class="py-3.5 px-6 font-bold text-slate-800">
                                {{ $jm->mahasiswa->nama_mahasiswa }}
                            </td>
                            <td class="py-3.5 px-6">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded text-xs font-mono font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                    <i class="fa-solid fa-fingerprint text-[10px]"></i>
                                    <span>PIN: {{ $jm->mahasiswa->id_finger ?: '-' }}</span>
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-600">
                                {{ $jm->mahasiswa->departemen->nama_departemen ?? '-' }}
                            </td>
                            <td class="py-3.5 px-6 text-right">
                                <form action="{{ route('jadwal.mahasiswa.destroy', $jm) }}" method="POST" class="inline" onsubmit="return confirm('Keluarkan mahasiswa ini dari jadwal kelas?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 rounded hover:bg-red-50 transition-colors" title="Keluarkan">
                                        <i class="fa-regular fa-trash-can text-sm"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-slate-400">
                                Belum ada mahasiswa yang didaftarkan ke kelas ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah Peserta Mahasiswa -->
    <div x-show="modalAddMhs" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto">
        <div @click.outside="modalAddMhs = false" class="modal-box bg-white w-full max-w-xl my-8">
            <div class="modal-header">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-brand-50 flex items-center justify-center">
                        <i class="fa-solid fa-user-plus text-brand-700"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Tambah Peserta Mahasiswa</h3>
                        <p class="text-xs text-slate-500">Pilih satu atau beberapa mahasiswa untuk didaftarkan ke kelas ini</p>
                    </div>
                </div>
                <button @click="modalAddMhs = false" class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 transition-colors">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <form action="{{ route('jadwal.mahasiswa.store', $jadwalKelas) }}" method="POST">
                @csrf
                <div class="modal-body space-y-3">
                    <div class="max-h-80 overflow-y-auto border border-slate-200 rounded-lg p-2 space-y-1 bg-slate-50">
                        @forelse($availableMahasiswas as $mhs)
                            <label class="flex items-center justify-between p-2.5 bg-white rounded-lg border border-slate-200/60 hover:border-brand-500 cursor-pointer transition-colors">
                                <div class="flex items-center gap-3">
                                    <input type="checkbox" name="mahasiswa_ids[]" value="{{ $mhs->id }}"
                                           class="rounded text-brand-700 focus:ring-brand-600 border-slate-300">
                                    <div>
                                        <div class="font-bold text-xs text-slate-900">{{ $mhs->nama_mahasiswa }}</div>
                                        <span class="text-[11px] font-mono text-brand-700">{{ $mhs->nim }} &bull; PIN: {{ $mhs->id_finger ?: '-' }}</span>
                                    </div>
                                </div>
                                <span class="text-[10px] px-2 py-0.5 rounded bg-slate-100 text-slate-600 font-semibold">
                                    {{ $mhs->kelas->nama_kelas ?? 'Tanpa Kelas' }}
                                </span>
                            </label>
                        @empty
                            <div class="text-center py-8 text-xs text-slate-400">
                                Semua mahasiswa yang ada di sistem sudah terdaftar pada kelas ini.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" @click="modalAddMhs = false" class="px-4 py-2 rounded-lg text-sm font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-lg text-sm font-bold bg-brand-700 text-white hover:bg-brand-800 shadow-md flex items-center gap-2">
                        <i class="fa-solid fa-plus"></i>
                        <span>Tambahkan Terpilih</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
