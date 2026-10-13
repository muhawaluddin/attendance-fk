<html xmlns:x="urn:schemas-microsoft-com:office:excel">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        table, th, td { border: 1px solid #000; font-family: Arial, sans-serif; font-size: 11px; }
        th { background-color: #EDF3F4; font-weight: bold; text-align: center; }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .green { color: #047857; }
        .red { color: #b91c1c; }
    </style>
</head>
<body>
    <h3 align="center">
        LAPORAN REKAPITULASI PRESENSI MENGAJAR DOSEN<br>
        {{ strtoupper($appSetting->faculty_name ?? 'FAKULTAS KEDOKTERAN') }} {{ strtoupper($appSetting->institution_name ?? 'UNIVERSITAS HASANUDDIN') }}
    </h3>
    <p align="center">
        Semester: {{ $semester ?: 'Semua Semester' }} | Tahun Ajaran: {{ $thnAjaran ?: 'Semua Tahun' }} | Dicetak: {{ now()->format('d/m/Y H:i') }} WITA
    </p>

    <table>
        <thead>
            <tr>
                <th width="40">No</th>
                <th width="260">Nama Dosen & Gelar</th>
                <th width="150">NIP</th>
                <th width="180">Departemen</th>
                <th width="90">Total Sesi</th>
                <th width="80">Hadir</th>
                <th width="80">Alpa</th>
                <th width="100">% Kehadiran</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rekapDosen as $index => $row)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td class="bold">{{ $row['nama'] }}</td>
                    <td class="center font-mono">{{ $row['nip'] }}</td>
                    <td>{{ $row['departemen'] }}</td>
                    <td class="center bold">{{ $row['total_sesi'] }}</td>
                    <td class="center bold green">{{ $row['hadir'] }}</td>
                    <td class="center bold red">{{ $row['alpa'] }}</td>
                    <td class="center bold {{ $row['persen'] >= 75 ? 'green' : ($row['persen'] >= 50 ? '' : 'red') }}">
                        {{ $row['persen'] }}%
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f1f5f9; font-weight: bold;">
                <td colspan="4" class="center">TOTAL KESELURUHAN</td>
                <td class="center">{{ $rekapDosen->sum('total_sesi') }}</td>
                <td class="center green">{{ $rekapDosen->sum('hadir') }}</td>
                <td class="center red">{{ $rekapDosen->sum('alpa') }}</td>
                <td class="center">
                    {{ $rekapDosen->sum('total_sesi') > 0 ? round(($rekapDosen->sum('hadir') / $rekapDosen->sum('total_sesi')) * 100, 1) : 0 }}%
                </td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
