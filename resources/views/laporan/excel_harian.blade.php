<html xmlns:x="urn:schemas-microsoft-com:office:excel">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        table, th, td { border: 1px solid #000; font-family: Arial, sans-serif; font-size: 11px; }
        th { background-color: #EDF3F4; font-weight: bold; }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .green { color: #047857; font-weight: bold; }
        .red { color: #B91C1C; font-weight: bold; }
    </style>
</head>
<body>
    <h3 align="center">
        LAPORAN REKAPITULASI AKUMULASI JAM KEHADIRAN {{ strtoupper($tipe === 'dosen' ? 'DOSEN PENGAJAR' : 'MAHASISWA') }}<br>
        {{ strtoupper($appSetting->institution_name ?? 'UNIVERSITAS HASANUDDIN') }}
    </h3>
    <p align="center">
        Tanggal: <strong>{{ \Carbon\Carbon::parse($tanggal)->format('d F Y') }}</strong> | Waktu Unduh: {{ now()->format('d/m/Y H:i') }} WITA
    </p>

    <!-- Ringkasan Statistik -->
    <table>
        <tr>
            <th colspan="2">Total Peserta Terjadwal</th>
            <th>Total Jam Terjadwal</th>
            <th>Total Jam Hadir</th>
            <th>Total Jam Alpa</th>
            <th>Rata-rata Kehadiran</th>
        </tr>
        <tr>
            <td colspan="2" class="center bold">{{ $stats['total_orang'] }} Orang</td>
            <td class="center bold">{{ $stats['total_jam_terjadwal'] }} Jam</td>
            <td class="center bold green">{{ $stats['total_jam_hadir'] }} Jam</td>
            <td class="center bold red">{{ $stats['total_jam_alpa'] }} Jam</td>
            <td class="center bold">{{ $stats['persen_global'] }}%</td>
        </tr>
    </table>
    <br>

    <!-- Tabel Detail -->
    <table>
        <thead>
            <tr>
                <th class="center" width="50">No</th>
                <th class="center" width="120">{{ $tipe === 'dosen' ? 'NIP' : 'NIM' }}</th>
                <th class="center" width="220">Nama Lengkap</th>
                <th class="center" width="140">{{ $tipe === 'dosen' ? 'Departemen' : 'Kelas' }}</th>
                <th class="center" width="80">PIN Finger</th>
                <th class="center" width="300">Rincian Sesi Perkuliahan Hari Ini</th>
                <th class="center" width="90">Jam Terjadwal</th>
                <th class="center" width="80">Jam Hadir</th>
                <th class="center" width="80">Jam Alpa</th>
                <th class="center" width="70">% Jam</th>
                <th class="center" width="100">Status Kehadiran</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rekapData as $idx => $row)
                <tr>
                    <td class="center">{{ $idx + 1 }}</td>
                    <td class="bold">{{ $row['identitas'] }}</td>
                    <td>{{ $row['nama'] }}</td>
                    <td>{{ $row['grup'] }}</td>
                    <td class="center">{{ $row['pin'] ?: '-' }}</td>
                    <td>
                        @if(count($row['sesi_details']) > 0)
                            @foreach($row['sesi_details'] as $s)
                                [{{ $s['status'] === 'H' ? 'HADIR' : 'ALPA' }}] {{ $s['mk'] }} ({{ $s['jml_jam'] }} Jam, {{ $s['jam_masuk'] }}-{{ $s['jam_keluar'] }} di {{ $s['ruangan'] }}) {{ $s['status'] === 'H' ? '- Tap ' . $s['scan_time'] : '' }}<br>
                            @endforeach
                        @else
                            -
                        @endif
                    </td>
                    <td class="center bold">{{ $row['total_jam_terjadwal'] }} Jam</td>
                    <td class="center bold green">{{ $row['total_jam_hadir'] }} Jam</td>
                    <td class="center bold red">{{ $row['total_jam_alpa'] }} Jam</td>
                    <td class="center bold">{{ $row['persen_hadir'] }}%</td>
                    <td class="center">
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
                    <td colspan="11" class="center">Tidak ada data perkuliahan atau absensi pada tanggal ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
