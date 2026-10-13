@extends('layouts.app')

@section('title', 'Laporan Absen Mahasiswa')
@section('page-title', 'Laporan Rekapitulasi Absensi Mahasiswa')
@section('page-subtitle', 'Monitoring presensi mahasiswa per blok, tanggal perkuliahan, dan persentase kehadiran')

@section('content')
<div class="space-y-6">

    <!-- Filter Card -->
    <div class="bg-white table-card border border-slate-200/80 shadow-sm p-5 sm:p-6">
        <form action="{{ route('laporan.mahasiswa') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                
                <!-- Pilih Jadwal Blok -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Jadwal Blok</label>
                    <select name="jadwal_id" onchange="this.form.submit()"
                            class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-600">
                        @foreach($jadwalList as $j)
                            <option value="{{ $j->id }}" {{ ($selectedJadwal?->id == $j->id) ? 'selected' : '' }}>
                                {{ $j->mataKuliah->nama_mk ?? 'Blok' }} ({{ $j->semester }} {{ $j->thn_ajaran }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Pilih Kelas -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Kelas</label>
                    <select name="jadwal_kelas_id" onchange="this.form.submit()"
                            class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-600">
                        @forelse($kelasList as $jk)
                            <option value="{{ $jk->id }}" {{ ($selectedJadwalKelas?->id == $jk->id) ? 'selected' : '' }}>
                                {{ $jk->kelas->nama_kelas ?? 'Kelas' }}
                            </option>
                        @empty
                            <option value="">Belum ada kelas</option>
                        @endforelse
                    </select>
                </div>

                <!-- Filter Tanggal (Opsional) -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Tanggal Sesi (Opsional)</label>
                    <input type="date" name="tanggal" value="{{ $selectedTanggal }}" onchange="this.form.submit()"
                           class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-600">
                </div>

                <!-- Action Buttons: Print & Excel -->
                <div class="flex items-end gap-2">
                    @if($selectedJadwal && $selectedJadwalKelas)
                        <a href="{{ route('laporan.mahasiswa', array_merge(request()->all(), ['export' => 'excel'])) }}" 
                           class="flex-1 py-2 px-3 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center justify-center gap-1.5 shadow-sm transition-colors"
                           title="Unduh file Excel spreadsheet">
                            <i class="fa-solid fa-file-excel"></i>
                            <span>Excel</span>
                        </a>

                        <a href="{{ route('laporan.mahasiswa', array_merge(request()->all(), ['print' => '1'])) }}" target="_blank"
                           class="flex-1 py-2 px-3 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs flex items-center justify-center gap-1.5 shadow-sm transition-colors"
                           title="Cetak format cetak resmi">
                            <i class="fa-solid fa-print"></i>
                            <span>Cetak</span>
                        </a>
                    @endif
                </div>

            </div>
        </form>
    </div>

    <!-- Attendance Matrix Table -->
    @if($laporanData && count($laporanData) > 0)
        <div class="bg-white table-card border border-slate-200/80 shadow-sm overflow-hidden">
            
            <!-- Table Header Summary -->
            <div class="p-5 sm:p-6 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-50/50">
                <div>
                    <h3 class="text-base sm:text-lg font-black text-slate-900">
                        {{ $selectedJadwal->mataKuliah->nama_mk ?? 'Blok' }} &bull; {{ $selectedJadwalKelas->kelas->nama_kelas ?? 'Kelas' }}
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Semester {{ $selectedJadwal->semester }} {{ $selectedJadwal->thn_ajaran }} &bull; Total {{ count($sesiList) }} Sesi Perkuliahan
                    </p>
                </div>
                <div class="flex items-center gap-2 text-xs">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-sm">
                        <i class="fa-solid fa-circle-check text-emerald-500 text-xs"></i>
                        H = Hadir Tepat
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-bold bg-rose-50 text-rose-700 border border-rose-200 shadow-sm">
                        <i class="fa-solid fa-circle-xmark text-rose-500 text-xs"></i>
                        A = Alpa / Absen
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-bold bg-slate-100 text-slate-700 border border-slate-200 shadow-sm">
                        Lulus: &ge; 70%
                    </span>
                </div>
            </div>

            <!-- Scrollable Matrix Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-100 text-slate-700 font-extrabold text-[11px] uppercase tracking-wider border-b border-slate-200">
                            <th class="py-3 px-3 text-center border-r border-slate-200 w-10" rowspan="2">No</th>
                            <th class="py-3 px-3 text-center border-r border-slate-200 w-28" rowspan="2">NIM</th>
                            <th class="py-3 px-4 border-r border-slate-200 min-w-[200px]" rowspan="2">Nama Mahasiswa</th>
                            
                            <!-- Session Columns -->
                            @foreach($sesiList as $s)
                                <th class="py-2 px-3 text-center border-r border-slate-200 min-w-[130px]" colspan="2">
                                    <div class="font-extrabold text-slate-900">{{ $s->jadwalHari->tanggal->format('d/m/Y') }}</div>
                                    <div class="font-normal text-[10px] text-slate-500 font-mono">{{ $s->jam_masuk }} - {{ $s->jam_keluar ?: $s->hitungJamKeluar() }}</div>
                                </th>
                            @endforeach

                            <th class="py-2 px-3 text-center border-r border-slate-200" colspan="2">Total</th>
                            <th class="py-3 px-4 text-center min-w-[100px]" rowspan="2">Presensi (%)</th>
                        </tr>
                        <tr class="bg-slate-50 text-[10px] text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200">
                            @foreach($sesiList as $s)
                                <th class="py-1 px-1.5 text-center border-r border-slate-200 w-8">Sts</th>
                                <th class="py-1 px-1.5 text-center border-r border-slate-200 w-14">Tap</th>
                            @endforeach
                            <th class="py-1 px-2 text-center border-r border-slate-200 text-blue-700 w-10">H</th>
                            <th class="py-1 px-2 text-center border-r border-slate-200 text-red-700 w-10">A</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($laporanData as $index => $row)
                            @php
                                $isPass = ($row['persen'] >= 70);
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3 px-3 text-center font-mono text-slate-400 border-r border-slate-200">
                                    {{ $index + 1 }}
                                </td>
                                <td class="py-3 px-3 text-center font-mono font-bold text-brand-700 border-r border-slate-200 whitespace-nowrap">
                                    {{ $row['mahasiswa']->nim }}
                                </td>
                                <td class="py-3 px-4 font-bold text-slate-900 border-r border-slate-200 whitespace-nowrap">
                                    {{ $row['mahasiswa']->nama_mahasiswa }}
                                </td>

                                <!-- Sesi Checkmarks -->
                                @foreach($row['sesi_status'] as $stat)
                                    <td class="py-2 px-1 text-center font-bold border-r border-slate-200 {{ $stat['status'] === 'H' ? 'text-emerald-700 bg-emerald-50/40' : 'text-rose-700 bg-rose-50/40' }}">
                                        {{ $stat['status'] }}
                                    </td>
                                    <td class="py-2 px-1 text-center font-mono text-[10px] text-slate-500 border-r border-slate-200">
                                        {{ $stat['waktu'] }}
                                    </td>
                                @endforeach

                                <!-- Totals -->
                                <td class="py-2 px-2 text-center font-bold text-emerald-700 border-r border-slate-200">
                                    {{ $row['hadir'] }}
                                </td>
                                <td class="py-2 px-2 text-center font-bold text-rose-700 border-r border-slate-200">
                                    {{ $row['alpa'] }}
                                </td>
                                <td class="py-2 px-3 text-center font-bold font-mono">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $isPass ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                        {{ $row['persen'] }}%
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    @else
        <div class="p-12 text-center bg-white table-card border border-slate-200 text-slate-400">
            <i class="fa-solid fa-file-circle-xmark text-4xl mb-3 block"></i>
            <p class="font-semibold text-slate-700">Tidak ada data absensi untuk kombinasi jadwal dan kelas terpilih.</p>
            <p class="text-xs text-slate-400 mt-1">Pastikan jadwal memiliki sesi kegiatan dan mahasiswa telah didaftarkan ke kelas.</p>
        </div>
    @endif

</div>
@endsection
