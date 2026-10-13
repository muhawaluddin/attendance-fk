@extends('layouts.app')

@section('title', 'Rekap Presensi Dosen')
@section('page-title', 'Laporan Rekapitulasi Presensi Dosen')
@section('page-subtitle', 'Monitoring akumulasi jam kehadiran dan rekapitulasi mengajar dosen')

@section('content')
<div class="space-y-6" x-data="{
    activeTab: '{{ $activeTab }}',
    searchQuery: '{{ addslashes($search ?? '') }}',
    sessionSearch: '',
    matchesDosen(nama, nip, dept) {
        if (!this.searchQuery) return true;
        const q = this.searchQuery.toLowerCase().trim();
        return (nama && nama.toLowerCase().includes(q)) ||
               (nip && nip.toLowerCase().includes(q)) ||
               (dept && dept.toLowerCase().includes(q));
    },
    matchesSession(mk, kegiatan, kelas, ruangan, hari) {
        if (!this.sessionSearch) return true;
        const q = this.sessionSearch.toLowerCase().trim();
        return (mk && mk.toLowerCase().includes(q)) ||
               (kegiatan && kegiatan.toLowerCase().includes(q)) ||
               (kelas && kelas.toLowerCase().includes(q)) ||
               (ruangan && ruangan.toLowerCase().includes(q)) ||
               (hari && hari.toLowerCase().includes(q));
    }
}">

    <!-- Filter & Action Card -->
    <div class="bg-white table-card border border-slate-200/80 shadow-sm p-5 sm:p-6">
        <form action="{{ route('laporan.dosen') }}" method="GET" class="space-y-4">
            <input type="hidden" name="tab" :value="activeTab">
            @if($selectedDosenId)
                <input type="hidden" name="dosen_id" value="{{ $selectedDosenId }}">
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3.5 items-end">
                
                <!-- Live Search Box -->
                <div class="lg:col-span-4">
                    <label class="form-label">Cari Dosen Tertentu</label>
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" name="search" x-model="searchQuery"
                               value="{{ $search }}"
                               placeholder="Ketik nama dosen, NIP, atau bagian..."
                               class="form-input pl-8 pr-8 text-xs font-medium">
                        <button type="button" x-show="searchQuery" @click="searchQuery = ''"
                                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </button>
                    </div>
                </div>

                <!-- Departemen -->
                <div class="lg:col-span-3">
                    <label class="form-label">Departemen / Bagian</label>
                    <select name="departemen_id" onchange="this.form.submit()" class="form-input text-xs">
                        <option value="">Semua Departemen</option>
                        @foreach($departemenList as $dept)
                            <option value="{{ $dept->id }}" {{ $selectedDepartemenId == $dept->id ? 'selected' : '' }}>
                                {{ $dept->nama_departemen }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Semester -->
                <div class="lg:col-span-2">
                    <label class="form-label">Semester</label>
                    <select name="semester" onchange="this.form.submit()" class="form-input text-xs">
                        <option value="">Semua Semester</option>
                        <option value="Ganjil" {{ $semester === 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                        <option value="Genap" {{ $semester === 'Genap' ? 'selected' : '' }}>Genap</option>
                    </select>
                </div>

                <!-- Tahun Ajaran -->
                <div class="lg:col-span-2">
                    <label class="form-label">Tahun Ajaran</label>
                    <select name="thn_ajaran" onchange="this.form.submit()" class="form-input text-xs">
                        <option value="">Semua Tahun</option>
                        <option value="2025/2026" {{ $thnAjaran === '2025/2026' ? 'selected' : '' }}>2025/2026</option>
                        <option value="2026/2027" {{ $thnAjaran === '2026/2027' ? 'selected' : '' }}>2026/2027</option>
                    </select>
                </div>

                <!-- Reset / Filter Button -->
                <div class="lg:col-span-1 flex gap-1.5">
                    <a href="{{ route('laporan.dosen') }}" title="Reset Filter"
                       class="w-full py-2 px-2.5 rounded-lg border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-600 font-bold text-xs flex items-center justify-center transition-colors">
                        <i class="fa-solid fa-arrow-rotate-left"></i>
                    </a>
                </div>

            </div>

            <!-- Export Buttons & View Toggle Bar -->
            <div class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                
                <!-- View Tabs -->
                <div class="inline-flex p-1 bg-slate-100 rounded-lg text-xs font-semibold text-slate-600">
                    <button type="button" @click="activeTab = 'rekap'"
                            :class="activeTab === 'rekap' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900'"
                            class="px-3.5 py-1.5 rounded-md transition-all flex items-center gap-1.5">
                        <i class="fa-solid fa-table-list text-slate-400"></i>
                        <span>Tabel Rekapitulasi Dosen</span>
                        <span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-slate-200/70 text-slate-700">
                            {{ $rekapDosen->count() }}
                        </span>
                    </button>

                    @if($selectedDosen)
                        <button type="button" @click="activeTab = 'detail'"
                                :class="activeTab === 'detail' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900'"
                                class="px-3.5 py-1.5 rounded-md transition-all flex items-center gap-1.5">
                            <i class="fa-solid fa-calendar-check text-emerald-600"></i>
                            <span class="max-w-[140px] truncate">Rincian: {{ $selectedDosen->nama_dosen }}</span>
                        </button>
                    @endif
                </div>

                <!-- Export & Print Actions -->
                <div class="flex items-center gap-2">
                    <a href="{{ route('laporan.dosen', array_merge(request()->all(), ['export' => 'excel', 'dosen_id' => $selectedDosenId])) }}"
                       class="py-1.5 px-3 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm transition-colors"
                       title="Unduh format spreadsheet Excel">
                        <i class="fa-solid fa-file-excel"></i>
                        <span>Ekspor Excel</span>
                    </a>

                    <a href="{{ route('laporan.dosen', array_merge(request()->all(), ['print' => '1', 'dosen_id' => $selectedDosenId])) }}" target="_blank"
                       class="py-1.5 px-3 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm transition-colors"
                       title="Cetak dokumen resmi">
                        <i class="fa-solid fa-print"></i>
                        <span>Cetak Dokumen</span>
                    </a>
                </div>

            </div>
        </form>
    </div>

    <!-- Quick Stat Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $sumSesi = $rekapDosen->sum('total_sesi');
            $sumHadir = $rekapDosen->sum('hadir');
            $sumAlpa = $rekapDosen->sum('alpa');
            $avgPersen = $sumSesi > 0 ? round(($sumHadir / $sumSesi) * 100, 1) : 0;
            $dosenAktif = $rekapDosen->where('total_sesi', '>', 0)->count();
        @endphp
        <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Dosen</span>
            <div class="flex items-baseline gap-2 mt-1">
                <span class="text-2xl font-black text-slate-900">{{ $rekapDosen->count() }}</span>
                <span class="text-xs text-slate-500">({{ $dosenAktif }} aktif ajar)</span>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Jam Sesi</span>
            <div class="flex items-baseline gap-2 mt-1">
                <span class="text-2xl font-black text-brand-700">{{ $sumSesi }}</span>
                <span class="text-xs text-slate-500">Sesi Terjadwal</span>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Hadir (H)</span>
            <div class="flex items-baseline gap-2 mt-1">
                <span class="text-2xl font-black text-emerald-600">{{ $sumHadir }}</span>
                <span class="text-xs text-rose-500">/ {{ $sumAlpa }} Alpa</span>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Rata-rata Presensi</span>
            <div class="flex items-baseline gap-2 mt-1">
                <span class="text-2xl font-black {{ $avgPersen >= 75 ? 'text-emerald-600' : ($avgPersen >= 50 ? 'text-amber-600' : 'text-rose-600') }}">
                    {{ $avgPersen }}%
                </span>
                <span class="text-xs text-slate-500">Kehadiran Global</span>
            </div>
        </div>
    </div>

    <!-- TAB 1: TABEL REKAPITULASI SELURUH DOSEN -->
    <div x-show="activeTab === 'rekap'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="space-y-4">
        
        <div class="bg-white table-card border border-slate-200/80 shadow-sm overflow-hidden">
            
            <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50">
                <div>
                    <h3 class="text-sm sm:text-base font-bold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-users text-brand-700"></i>
                        <span>Tabel Rekapitulasi Presensi Dosen</span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Daftar lengkap kehadiran seluruh dosen. Ketik pencarian di atas untuk memfilter dosen tertentu.
                    </p>
                </div>

                <div class="text-xs font-medium text-slate-500">
                    Menampilkan <strong class="text-slate-900">{{ $rekapDosen->count() }}</strong> dosen
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-slate-50/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200/70">
                            <th class="py-3 px-4 w-12 text-center">No</th>
                            <th class="py-3 px-5">Dosen & NIP</th>
                            <th class="py-3 px-4">Departemen / Bagian</th>
                            <th class="py-3 px-3 text-center w-24">Total Sesi</th>
                            <th class="py-3 px-3 text-center w-20">Hadir</th>
                            <th class="py-3 px-3 text-center w-20">Alpa</th>
                            <th class="py-3 px-4 text-center w-36">% Kehadiran</th>
                            <th class="py-3 px-4 text-center w-36">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @forelse($rekapDosen as $index => $row)
                            <tr class="hover:bg-slate-50/80 transition-colors"
                                x-show="matchesDosen('{{ addslashes($row['nama']) }}', '{{ addslashes($row['nip']) }}', '{{ addslashes($row['departemen']) }}')">
                                
                                <td class="py-3 px-4 text-center text-slate-400 font-mono">
                                    {{ $index + 1 }}
                                </td>

                                <td class="py-3 px-5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-brand-50 text-brand-700 font-black text-xs flex items-center justify-center border border-brand-200/60 shrink-0">
                                            {{ strtoupper(substr($row['dosen']->nama_dosen, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-bold text-slate-900 text-xs truncate">
                                                {{ $row['nama'] }}
                                            </div>
                                            <div class="text-[11px] text-slate-400 font-mono mt-0.5 flex items-center gap-2">
                                                <span>NIP: {{ $row['nip'] }}</span>
                                                <span>&bull;</span>
                                                <span>PIN: {{ $row['pin'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-3 px-4 text-slate-600 font-medium">
                                    {{ $row['departemen'] }}
                                </td>

                                <td class="py-3 px-3 text-center font-mono font-bold text-slate-800">
                                    {{ $row['total_sesi'] }}
                                </td>

                                <td class="py-3 px-3 text-center">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                                        <i class="fa-solid fa-circle-check text-emerald-500 text-[10px]"></i>
                                        <span>{{ $row['hadir'] }}</span>
                                    </span>
                                </td>

                                <td class="py-3 px-3 text-center">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200/80">
                                        <i class="fa-solid fa-circle-xmark text-rose-500 text-[10px]"></i>
                                        <span>{{ $row['alpa'] }}</span>
                                    </span>
                                </td>

                                <td class="py-3 px-4 text-center">
                                    <div class="flex flex-col items-center gap-1">
                                        <span class="font-mono font-bold {{ $row['persen'] >= 75 ? 'text-emerald-700' : ($row['persen'] >= 50 ? 'text-amber-700' : 'text-rose-700') }}">
                                            {{ $row['persen'] }}%
                                        </span>
                                        <div class="w-24 bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                            <div class="h-1.5 rounded-full {{ $row['persen'] >= 75 ? 'bg-emerald-500' : ($row['persen'] >= 50 ? 'bg-amber-500' : 'bg-rose-500') }}"
                                                 style="width: {{ min(100, $row['persen']) }}%;"></div>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1">
                                        <a href="{{ route('laporan.dosen', array_merge(request()->all(), ['dosen_id' => $row['id'], 'tab' => 'detail'])) }}"
                                           class="py-1 px-2.5 rounded-lg bg-brand-50 hover:bg-brand-100 text-brand-700 font-semibold text-xs border border-brand-200/60 transition-colors flex items-center gap-1"
                                           title="Buka rincian sesi jadwal mengajar">
                                            <i class="fa-regular fa-calendar-days text-[11px]"></i>
                                            <span>Rincian Sesi</span>
                                        </a>

                                        <a href="{{ route('laporan.dosen', array_merge(request()->all(), ['dosen_id' => $row['id'], 'export' => 'excel'])) }}"
                                           class="p-1.5 rounded-lg text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 transition-colors"
                                           title="Unduh Excel Dosen Ini">
                                            <i class="fa-solid fa-file-excel text-xs"></i>
                                        </a>

                                        <a href="{{ route('laporan.dosen', array_merge(request()->all(), ['dosen_id' => $row['id'], 'print' => '1'])) }}" target="_blank"
                                           class="p-1.5 rounded-lg text-slate-400 hover:text-slate-800 hover:bg-slate-100 transition-colors"
                                           title="Cetak Dosen Ini">
                                            <i class="fa-solid fa-print text-xs"></i>
                                        </a>
                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-16 text-slate-400">
                                    <i class="fa-regular fa-user-xmark text-4xl mb-3 block text-slate-300"></i>
                                    <p class="font-bold text-slate-700">Tidak ada data dosen yang sesuai dengan kriteria filter.</p>
                                    <p class="text-xs text-slate-400 mt-1">Coba ubah kata kunci pencarian atau reset filter.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

    </div>

    <!-- TAB 2: RINCIAN SESI DOSEN TERTENTU -->
    <div x-show="activeTab === 'detail'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="space-y-4">
        
        @if($selectedDosen)
            <div class="bg-white table-card border border-slate-200/80 shadow-sm overflow-hidden">
                
                <!-- Dosen Profile & Header Card -->
                <div class="p-5 sm:p-6 border-b border-slate-100 bg-slate-50/50 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-start gap-3.5">
                        <div class="w-12 h-12 rounded-xl bg-brand-700 text-white font-black text-lg flex items-center justify-center shadow-sm shrink-0">
                            {{ strtoupper(substr($selectedDosen->nama_dosen, 0, 1)) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-base sm:text-lg font-black text-slate-900 leading-tight">
                                    {{ $selectedDosen->nama_lengkap }}
                                </h3>
                                <button type="button" @click="activeTab = 'rekap'"
                                        class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200/80 hover:bg-slate-300 text-slate-700 transition-colors">
                                    &larr; Rekap Semua
                                </button>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">
                                NIP: <strong class="font-mono text-slate-700">{{ $selectedDosen->nip }}</strong> &bull; PIN Finger: <strong class="font-mono text-slate-700">{{ $selectedDosen->id_finger ?: '-' }}</strong> &bull; Bagian: {{ $selectedDosen->departemen->nama_departemen ?? '-' }}
                            </p>
                        </div>
                    </div>

                    <!-- Summary Chips for this Lecturer -->
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/90 shadow-xs">
                            <i class="fa-solid fa-circle-check text-emerald-500 text-xs"></i>
                            <span>{{ $laporanDosenData->where('status', 'H')->count() }} Hadir</span>
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200/90 shadow-xs">
                            <i class="fa-solid fa-circle-xmark text-rose-500 text-xs"></i>
                            <span>{{ $laporanDosenData->where('status', 'A')->count() }} Alpa</span>
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200/80 shadow-xs">
                            <i class="fa-solid fa-layer-group text-slate-400 text-xs"></i>
                            <span>Total {{ $laporanDosenData->count() }} Sesi</span>
                        </span>
                    </div>
                </div>

                <!-- Session Search Sub-filter -->
                <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between gap-3 bg-white">
                    <div class="relative w-full max-w-xs">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" x-model="sessionSearch"
                               placeholder="Cari mata kuliah, materi, ruang..."
                               class="form-input pl-8 text-xs">
                    </div>
                    <span class="text-xs text-slate-400 hidden sm:inline">
                        Menampilkan <strong class="text-slate-700">{{ $laporanDosenData->count() }}</strong> sesi jadwal mengajar
                    </span>
                </div>

                <!-- Session Matrix Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="bg-slate-50/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200/70">
                                <th class="py-3.5 px-5 w-14 text-center">No</th>
                                <th class="py-3.5 px-5">Tanggal & Waktu</th>
                                <th class="py-3.5 px-5">Mata Kuliah / Blok</th>
                                <th class="py-3.5 px-5">Kegiatan / Materi</th>
                                <th class="py-3.5 px-5">Kelas & Ruangan</th>
                                <th class="py-3.5 px-5 text-center w-28">Status</th>
                                <th class="py-3.5 px-5 text-center w-28">Waktu Tap</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
                            @forelse($laporanDosenData as $index => $row)
                                <tr class="hover:bg-slate-50/70 transition-colors"
                                    x-show="matchesSession('{{ addslashes($row['mk']) }}', '{{ addslashes($row['kegiatan']) }}', '{{ addslashes($row['kelas']) }}', '{{ addslashes($row['ruangan']) }}', '{{ addslashes($row['hari']) }}')">
                                    <td class="py-3.5 px-5 text-center text-slate-400 font-mono">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="py-3.5 px-5 whitespace-nowrap">
                                        <div class="font-bold text-xs text-slate-900">
                                            {{ $row['hari'] }}, {{ $row['tanggal']->format('d/m/Y') }}
                                        </div>
                                        <span class="font-mono text-[11px] text-slate-500">
                                            {{ $row['jam_masuk'] }} – {{ $row['jam_keluar'] }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-5 font-bold text-xs text-brand-700">
                                        {{ $row['mk'] }}
                                    </td>
                                    <td class="py-3.5 px-5 text-slate-700">
                                        {{ $row['kegiatan'] }}
                                    </td>
                                    <td class="py-3.5 px-5 text-slate-600 whitespace-nowrap">
                                        <div class="font-semibold text-slate-800">{{ $row['kelas'] }}</div>
                                        <span class="text-slate-400">{{ $row['ruangan'] }}</span>
                                    </td>
                                    <td class="py-3.5 px-5 text-center whitespace-nowrap">
                                        @if($row['status'] === 'H')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/90 shadow-[0_1px_2px_rgba(16,185,129,0.08)]">
                                                <i class="fa-solid fa-circle-check text-emerald-500 text-xs"></i>
                                                <span>Hadir (H)</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200/90 shadow-[0_1px_2px_rgba(244,63,94,0.08)]">
                                                <i class="fa-solid fa-circle-xmark text-rose-500 text-xs"></i>
                                                <span>Alpa (A)</span>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-5 text-center whitespace-nowrap font-mono font-bold text-xs">
                                        @if($row['checklock'] && $row['checklock'] !== '-')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-100 text-slate-800 border border-slate-200/70 shadow-2xs">
                                                <i class="fa-regular fa-clock text-slate-400 text-[10px]"></i>
                                                <span>{{ $row['checklock'] }}</span>
                                            </span>
                                        @else
                                            <span class="text-slate-300 font-normal">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-16 text-slate-400">
                                        <i class="fa-regular fa-calendar-xmark text-4xl mb-3 block text-slate-300"></i>
                                        <p class="font-bold text-slate-700">Belum ada sesi jadwal perkuliahan untuk dosen ini.</p>
                                        <p class="text-xs text-slate-400 mt-1">Pastikan dosen telah ditugaskan pada jadwal perkuliahan.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        @else
            <div class="p-12 text-center bg-white table-card border border-slate-200 text-slate-400">
                <i class="fa-solid fa-user-gear text-4xl mb-3 block text-slate-300"></i>
                <p class="font-semibold text-slate-700">Pilih salah satu dosen untuk melihat rincian sesi perkuliahan.</p>
                <p class="text-xs text-slate-400 mt-1">Gunakan tombol "Rincian Sesi" pada Tabel Rekapitulasi di atas.</p>
                <button type="button" @click="activeTab = 'rekap'" class="mt-4 px-4 py-2 rounded-lg bg-brand-700 hover:bg-brand-800 text-white font-bold text-xs transition-colors">
                    Buka Tabel Rekapitulasi Dosen
                </button>
            </div>
        @endif

    </div>

</div>
@endsection
