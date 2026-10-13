<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Rekapitulasi Jam Harian ({{ $tipe === 'dosen' ? 'Dosen' : 'Mahasiswa' }}) - {{ $appSetting->institution_name ?? 'Universitas' }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #111; margin: 25px; line-height: 1.4; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .header { margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 12px; }
        .header h2, .header h3, .header h4 { margin: 2px 0; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #333; padding: 5px 8px; vertical-align: top; }
        th { background-color: #f1f5f9; font-weight: bold; text-align: center; }
        .badge-hadir { color: #047857; font-weight: bold; }
        .badge-alpa { color: #b91c1c; font-weight: bold; }
        .summary-box { display: flex; justify-content: space-between; margin: 15px 0; padding: 10px; background-color: #f8fafc; border: 1px solid #cbd5e1; }
        .print-btn { padding: 8px 16px; background: #0f172a; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; margin-bottom: 15px; }
        @media print {
            .no-print { display: none !important; }
            body { margin: 10mm; }
        }
    </style>
</head>
<body>

    <div class="no-print text-right">
        <button onclick="window.print()" class="print-btn">Cetak Dokumen (Ctrl + P)</button>
    </div>

    <!-- Kop Surat Resmi -->
    <div class="header text-center">
        <h3>{{ $appSetting->institution_name ?? 'UNIVERSITAS HASANUDDIN' }}</h3>
        <h2>{{ $appSetting->faculty_name ?: 'SISTEM ABSENSI PERKULIAHAN' }}</h2>
        <h4>LAPORAN REKAPITULASI AKUMULASI JAM KEHADIRAN {{ strtoupper($tipe === 'dosen' ? 'DOSEN PENGAJAR' : 'MAHASISWA') }}</h4>
        <p style="margin: 3px 0; font-size: 11px;">
            Hari / Tanggal: <strong>{{ $carbonTgl->locale('id')->isoFormat('dddd, D MMMM Y') }}</strong> &bull; Waktu Cetak: {{ now()->format('d/m/Y H:i') }} WITA
        </p>
    </div>

    <!-- Ringkasan Jam -->
    <table style="width: 100%; margin-bottom: 15px; border: none;">
        <tr style="border: none;">
            <td style="border: none; padding: 2px;">Total Peserta Terjadwal: <strong>{{ $stats['total_orang'] }} Orang</strong></td>
            <td style="border: none; padding: 2px;">Total Jam Terjadwal: <strong>{{ $stats['total_jam_terjadwal'] }} Jam</strong></td>
            <td style="border: none; padding: 2px;">Total Jam Hadir: <strong>{{ $stats['total_jam_hadir'] }} Jam</strong></td>
            <td style="border: none; padding: 2px;">Total Jam Alpa: <strong>{{ $stats['total_jam_alpa'] }} Jam</strong></td>
            <td style="border: none; padding: 2px; text-align: right;">Rata-rata Kehadiran: <strong>{{ $stats['persen_global'] }}%</strong></td>
        </tr>
    </table>

    <!-- Tabel Rekapitulasi -->
    <table>
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="12%">{{ $tipe === 'dosen' ? 'NIP' : 'NIM' }}</th>
                <th width="20%">Nama Lengkap</th>
                <th width="12%">{{ $tipe === 'dosen' ? 'Departemen' : 'Kelas' }}</th>
                <th>Rincian Perkuliahan & Jam Masuk Scan</th>
                <th width="8%">Terjadwal</th>
                <th width="8%">Hadir</th>
                <th width="8%">Alpa</th>
                <th width="6%">%</th>
                <th width="10%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rekapData as $idx => $row)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="font-bold">{{ $row['identitas'] }}</td>
                    <td>{{ $row['nama'] }}</td>
                    <td>{{ $row['grup'] }}</td>
                    <td>
                        @if(count($row['sesi_details']) > 0)
                            <ul style="margin: 0; padding-left: 15px;">
                                @foreach($row['sesi_details'] as $s)
                                    <li>
                                        <strong>{{ $s['mk'] }}</strong> ({{ $s['jml_jam'] }} Jam) &bull; {{ $s['jam_masuk'] }}-{{ $s['jam_keluar'] }} ({{ $s['ruangan'] }}) &rarr; 
                                        @if($s['status'] === 'H')
                                            <span class="badge-hadir">Hadir (Tap: {{ $s['scan_time'] }})</span>
                                        @else
                                            <span class="badge-alpa">Alpa</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <em>Tidak ada jadwal perkuliahan</em>
                        @endif
                    </td>
                    <td class="text-center font-bold">{{ $row['total_jam_terjadwal'] }} Jam</td>
                    <td class="text-center font-bold badge-hadir">{{ $row['total_jam_hadir'] }} Jam</td>
                    <td class="text-center font-bold badge-alpa">{{ $row['total_jam_alpa'] }} Jam</td>
                    <td class="text-center font-bold">{{ $row['persen_hadir'] }}%</td>
                    <td class="text-center">
                        @if($row['total_jam_terjadwal'] == 0)
                            -
                        @elseif($row['total_jam_alpa'] == 0)
                            Hadir Penuh
                        @elseif($row['total_jam_hadir'] > 0)
                            Sebagian
                        @else
                            Alpa Total
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center" style="padding: 20px;">
                        Tidak ada data perkuliahan atau absensi pada tanggal ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Tanda Tangan Pengesahan -->
    <div style="margin-top: 35px; display: flex; justify-content: space-between;">
        <div style="width: 40%; text-align: center;">
            <p>Mengetahui,</p>
            <p style="margin-bottom: 50px;">Kepala Bagian Akademik</p>
            <p class="font-bold">_________________________</p>
            <p>NIP. ........................................</p>
        </div>
        <div style="width: 40%; text-align: center;">
            <p>Palopo / Makassar, {{ now()->locale('id')->isoFormat('D MMMM Y') }}</p>
            <p style="margin-bottom: 50px;">Operator SIM Presensi</p>
            <p class="font-bold">{{ auth()->user()->name ?? 'Administrator' }}</p>
            <p>NIP/ID. {{ auth()->user()->username ?? 'admin' }}</p>
        </div>
    </div>

</body>
</html>
