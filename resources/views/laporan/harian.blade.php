@extends('layouts.app')

@section('title', 'Laporan Rekapitulasi Jam Harian')
@section('page-title', 'Rekapitulasi Jam Harian')
@section('page-subtitle', 'Perhitungan akumulasi jumlah jam kegiatan perkuliahan mahasiswa dan dosen dalam 1 hari')

@section('content')
<div class="space-y-6" x-data="{
    tipeAktif: '{{ $tipe }}',
    detailModal: false,
    selectedRow: null
}">

    <!-- Filter & Toolbar Card -->
    <div class="bg-white table-card border border-slate-200/80 p-5 sm:p-6 shadow-sm">
        <form action="{{ route('laporan.harian') }}" method="GET" class="space-y-4">
            
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                
                <!-- Toggle Tipe Mahasiswa / Dosen -->
                <div class="flex items-center p-1 bg-slate-100 rounded-lg w-fit border border-slate-200">
                    <button type="submit" name="tipe" value="mahasiswa" 
                            class="px-4 py-1.5 rounded-md text-xs font-bold transition-all flex items-center gap-2 {{ $tipe === 'mahasiswa' ? 'bg-brand-700 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                        <i class="fa-solid fa-user-graduate"></i>
                        <span>Mahasiswa</span>
                    </button>
                    <button type="submit" name="tipe" value="dosen" 
                            class="px-4 py-1.5 rounded-md text-xs font-bold transition-all flex items-center gap-2 {{ $tipe === 'dosen' ? 'bg-brand-700 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                        <i class="fa-solid fa-user-doctor"></i>
                        <span>Dosen Pengajar</span>
                    </button>
                </div>

                <!-- Action Buttons: Print & Excel -->
                <div class="flex items-center gap-2">
                    <a href="{{ route('laporan.harian.print', request()->all()) }}" target="_blank"
                       class="px-3.5 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs shadow-sm flex items-center gap-2 transition-colors">
                        <i class="fa-solid fa-print text-slate-500"></i>
                        <span>Cetak Laporan</span>
                    </a>
                    <a href="{{ route('laporan.harian.excel', request()->all()) }}"
                       class="px-3.5 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-900/20 flex items-center gap-2 transition-colors">
                        <i class="fa-solid fa-file-excel"></i>
                        <span>Export Excel</span>
                    </a>
                </div>

            </div>

            <!-- Filter Row: Tanggal, Filter Grup, Pencarian -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-2 border-t border-slate-100">
                
                <!-- Tanggal -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Tanggal Absensi</label>
                    <div class="relative">
                        <i class="fa-regular fa-calendar absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="date" name="tanggal" value="{{ $tanggal }}" onchange="this.form.submit()"
                               class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-600">
                    </div>
                </div>

                @if($tipe === 'mahasiswa')
                    <!-- Filter Kelas -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Kelas Mahasiswa</label>
                        <select name="kelas_id" onchange="this.form.submit()"
                                class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-600">
                            <option value="">Semua Kelas</option>
                            @foreach($kelasList as $k)
                                <option value="{{ $k->id }}" {{ $kelasId == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <!-- Filter Departemen Dosen -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Departemen / Bagian</label>
                        <select name="departemen_id" onchange="this.form.submit()"
                                class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-600">
                            <option value="">Semua Departemen</option>
                            @foreach($departemenList as $d)
                                <option value="{{ $d->id }}" {{ $departemenId == $d->id ? 'selected' : '' }}>{{ $d->nama_departemen }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- Pencarian Nama / Identitas -->
                <div class="sm:col-span-2 lg:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Cari Nama atau {{ $tipe === 'dosen' ? 'NIP' : 'NIM' }}</label>
                    <div class="flex items-center gap-2">
                        <div class="relative flex-1">
                            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input type="text" name="q" value="{{ $search }}" placeholder="Ketik nama atau nomor identitas..."
                                   class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-brand-600">
                        </div>
                        <button type="submit" class="px-4 py-2 bg-brand-700 hover:bg-brand-800 text-white rounded-lg text-xs font-bold transition-colors">
                            Filter
                        </button>
                        @if($search || $kelasId || $departemenId)
                            <a href="{{ route('laporan.harian', ['tipe' => $tipe, 'tanggal' => $tanggal]) }}" class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg text-xs font-bold" title="Reset Filter">
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        @endif
                    </div>
                </div>

            </div>

        </form>
    </div>

    <!-- Metric KPI Cards: Akumulasi Jam Harian -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        
        <div class="p-4 sm:p-5 table-card bg-white border border-slate-200/80 shadow-sm flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-lg bg-brand-50 text-brand-700 flex items-center justify-center text-lg shrink-0">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase text-slate-400">Total {{ $tipe === 'dosen' ? 'Dosen' : 'Mahasiswa' }}</p>
                <h3 class="text-2xl font-black text-slate-900 mt-0.5">{{ $stats['total_orang'] }}</h3>
                <span class="text-[10px] text-slate-500">Terjadwal hari ini</span>
            </div>
        </div>

        <div class="p-4 sm:p-5 table-card bg-white border border-slate-200/80 shadow-sm flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center text-lg shrink-0">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase text-slate-400">Total Jam Terjadwal</p>
                <h3 class="text-2xl font-black text-slate-900 mt-0.5">{{ $stats['total_jam_terjadwal'] }} <span class="text-xs font-normal text-slate-500">Jam</span></h3>
                <span class="text-[10px] text-slate-500">Beban kuliah 1 hari</span>
            </div>
        </div>

        <div class="p-4 sm:p-5 table-card bg-white border border-slate-200/80 shadow-sm flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center text-lg shrink-0">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase text-slate-400">Total Jam Hadir</p>
                <h3 class="text-2xl font-black text-emerald-700 mt-0.5">{{ $stats['total_jam_hadir'] }} <span class="text-xs font-normal text-slate-500">Jam</span></h3>
                <span class="text-[10px] text-emerald-600 font-semibold">Tercatat di finger</span>
            </div>
        </div>

        <div class="p-4 sm:p-5 table-card bg-white border border-slate-200/80 shadow-sm flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-lg bg-red-50 text-red-700 flex items-center justify-center text-lg shrink-0">
                <i class="fa-solid fa-circle-xmark"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase text-slate-400">Total Jam Alpa</p>
                <h3 class="text-2xl font-black text-red-600 mt-0.5">{{ $stats['total_jam_alpa'] }} <span class="text-xs font-normal text-slate-500">Jam</span></h3>
                <span class="text-[10px] text-red-500">Tidak ada tap masuk</span>
            </div>
        </div>

        <div class="col-span-2 lg:col-span-1 p-4 sm:p-5 table-card bg-gradient-to-br from-brand-700 to-brand-900 text-white shadow-md flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase text-white/70">Tingkat Kehadiran</p>
                <h3 class="text-2xl sm:text-3xl font-black mt-0.5">{{ $stats['persen_global'] }}%</h3>
                <span class="text-[10px] text-white/80">{{ $carbonTgl->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
            </div>
            <div class="w-11 h-11 rounded-lg bg-white/10 flex items-center justify-center text-xl">
                <i class="fa-solid fa-chart-pie"></i>
            </div>
        </div>

    </div>

    <!-- Data Table Card -->
    <div class="bg-white table-card border border-slate-200/80 shadow-sm overflow-hidden">
        
        <div class="p-5 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="font-black text-slate-900 text-base">
                    Rekapitulasi Jam Per {{ $tipe === 'dosen' ? 'Dosen Pengajar' : 'Mahasiswa' }}
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">
                    Periode: <strong class="text-slate-700">{{ $carbonTgl->locale('id')->isoFormat('dddd, D MMMM Y') }}</strong> (WITA)
                </p>
            </div>
            <div class="text-xs font-semibold text-slate-500">
                Menampilkan <strong class="text-slate-800">{{ $rekapData->count() }}</strong> data
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200/70">
                        <th class="py-3.5 px-4 w-12 text-center">No</th>
                        <th class="py-3.5 px-4">{{ $tipe === 'dosen' ? 'NIP' : 'NIM' }}</th>
                        <th class="py-3.5 px-5">Nama Lengkap</th>
                        <th class="py-3.5 px-4">{{ $tipe === 'dosen' ? 'Departemen' : 'Kelas' }}</th>
                        <th class="py-3.5 px-4">Rincian Perkuliahan Hari Ini</th>
                        <th class="py-3.5 px-4 text-center">Terjadwal</th>
                        <th class="py-3.5 px-4 text-center">Hadir</th>
                        <th class="py-3.5 px-4 text-center">Alpa</th>
                        <th class="py-3.5 px-4 text-center">% Jam</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($rekapData as $idx => $row)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            
                            <td class="py-3.5 px-4 text-center text-xs text-slate-400 font-mono">
                                {{ $idx + 1 }}
                            </td>

                            <td class="py-3.5 px-4 font-mono font-bold text-xs text-slate-800 whitespace-nowrap">
                                {{ $row['identitas'] }}
                            </td>

                            <td class="py-3.5 px-5 whitespace-nowrap">
                                <div class="font-bold text-slate-900 text-sm">{{ $row['nama'] }}</div>
                                @if(!empty($row['pin']))
                                    <span class="text-[10px] text-slate-400 font-mono">PIN Finger: {{ $row['pin'] }}</span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-700">
                                    {{ $row['grup'] }}
                                </span>
                            </td>

                            <!-- Rincian Sesi Perkuliahan Hari Itu -->
                            <td class="py-3.5 px-4">
                                @if(count($row['sesi_details']) > 0)
                                    <div class="space-y-1.5 max-w-md">
                                        @foreach($row['sesi_details'] as $s)
                                            <div class="flex items-center justify-between gap-3 p-2 rounded-lg {{ $s['status'] === 'H' ? 'bg-emerald-50/70 border border-emerald-200/60' : 'bg-red-50/70 border border-red-200/60' }} text-xs">
                                                <div class="truncate">
                                                    <div class="font-bold text-slate-800 truncate">{{ $s['mk'] }}</div>
                                                    <div class="text-[10px] text-slate-500 flex items-center gap-1.5">
                                                        <span><i class="fa-regular fa-clock"></i> {{ $s['jam_masuk'] }}-{{ $s['jam_keluar'] }} ({{ $s['jml_jam'] }} Jam)</span>
                                                        <span>&bull;</span>
                                                        <span><i class="fa-solid fa-location-dot"></i> {{ $s['ruangan'] }}</span>
                                                    </div>
                                                </div>
                                                <div class="shrink-0 text-right">
                                                    @if($s['status'] === 'H')
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-black bg-emerald-200 text-emerald-900">
                                                            <i class="fa-solid fa-check"></i> {{ $s['scan_time'] }}
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-black bg-red-200 text-red-900">
                                                            <i class="fa-solid fa-xmark"></i> Alpa
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 italic">Tidak ada sesi perkuliahan</span>
                                @endif
                            </td>

                            <!-- Jam Terjadwal -->
                            <td class="py-3.5 px-4 text-center font-bold font-mono text-sm text-slate-700">
                                {{ $row['total_jam_terjadwal'] }} Jam
                            </td>

                            <!-- Jam Hadir -->
                            <td class="py-3.5 px-4 text-center font-bold font-mono text-sm text-emerald-700 bg-emerald-50/40">
                                {{ $row['total_jam_hadir'] }} Jam
                            </td>

                            <!-- Jam Alpa -->
                            <td class="py-3.5 px-4 text-center font-bold font-mono text-sm text-red-600 bg-red-50/40">
                                {{ $row['total_jam_alpa'] }} Jam
                            </td>

                            <!-- % Kehadiran Jam -->
                            <td class="py-3.5 px-4 text-center font-black text-sm">
                                <span class="px-2 py-0.5 rounded text-xs font-bold
                                    {{ $row['persen_hadir'] >= 80 ? 'bg-emerald-100 text-emerald-800' : '' }}
                                    {{ $row['persen_hadir'] >= 50 && $row['persen_hadir'] < 80 ? 'bg-amber-100 text-amber-800' : '' }}
                                    {{ $row['persen_hadir'] < 50 ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ $row['persen_hadir'] }}%
                                </span>
                            </td>

                            <!-- Status Harian -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @if($row['total_jam_terjadwal'] == 0)
                                    <span class="text-xs text-slate-400">-</span>
                                @elseif($row['total_jam_alpa'] == 0)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/90 shadow-2xs">
                                        <i class="fa-solid fa-circle-check text-emerald-500 text-xs"></i> Hadir Penuh
                                    </span>
                                @elseif($row['total_jam_hadir'] > 0)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200/90 shadow-2xs">
                                        <i class="fa-solid fa-triangle-exclamation text-amber-500 text-xs"></i> Sebagian
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200/90 shadow-2xs">
                                        <i class="fa-solid fa-circle-xmark text-rose-500 text-xs"></i> Alpa Total
                                    </span>
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-16 text-slate-400">
                                <i class="fa-regular fa-calendar-xmark text-4xl mb-2 text-slate-300"></i>
                                <p class="font-bold text-slate-600">Tidak ada jadwal perkuliahan atau data absensi pada tanggal ini.</p>
                                <p class="text-xs text-slate-400 mt-1">Silakan pilih tanggal lain atau ubah filter pencarian di atas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</div>
@endsection
