<html xmlns:x="urn:schemas-microsoft-com:office:excel">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        table, th, td { border: 1px solid #000; font-family: Arial, sans-serif; font-size: 11px; }
        th { background-color: #EDF3F4; font-weight: bold; }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .blue { color: #0101DF; }
        .red { color: #FF0000; }
    </style>
</head>
<body>
    <h3 align="center">
        LAPORAN ABSEN MAHASISWA SEMESTER {{ strtoupper($jadwal->semester) }} {{ $jadwal->thn_ajaran }}<br>
        FAKULTAS KEDOKTERAN UNIVERSITAS HASANUDDIN
    </h3>
    <h4>
        Blok: {{ $jadwal->mataKuliah->nama_mk }}<br>
        Kelas: {{ $jadwalKelas->kelas->nama_kelas }}
    </h4>

    <table>
        <thead>
            <tr>
                <th class="center" rowspan="3">No.</th>
                <th class="center" rowspan="3">NIM</th>
                <th class="center" rowspan="3">Nama Mahasiswa</th>
                @foreach($sesiList as $s)
                    <th class="center" colspan="2">{{ $s->jadwalHari->hari }}, {{ $s->jadwalHari->tanggal->format('d/m/Y') }}</th>
                @endforeach
                <th class="center" colspan="2" rowspan="2">Jumlah Kehadiran</th>
                <th class="center" rowspan="3">Persentase (%)</th>
            </tr>
            <tr>
                @foreach($sesiList as $s)
                    <th class="center" colspan="2">{{ $s->kegiatan }} ({{ $s->jam_masuk }} - {{ $s->jam_keluar ?: $s->hitungJamKeluar() }})</th>
                @endforeach
            </tr>
            <tr>
                @foreach($sesiList as $s)
                    <th class="center">Status</th>
                    <th class="center">Waktu</th>
                @endforeach
                <th class="center">H</th>
                <th class="center">A</th>
            </tr>
        </thead>
        <tbody>
            @foreach($laporanData as $index => $row)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td class="center" style="mso-number-format:'\@';">{{ $row['mahasiswa']->nim }}</td>
                    <td>{{ $row['mahasiswa']->nama_mahasiswa }}</td>
                    @foreach($row['sesi_status'] as $st)
                        <td class="center bold {{ $st['status'] === 'H' ? 'blue' : 'red' }}">{{ $st['status'] }}</td>
                        <td class="center">{{ $st['waktu'] }}</td>
                    @endforeach
                    <td class="center bold blue">{{ $row['hadir'] }}</td>
                    <td class="center bold red">{{ $row['alpa'] }}</td>
                    <td class="center bold {{ $row['persen'] >= 70 ? 'blue' : 'red' }}">{{ $row['persen'] }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
